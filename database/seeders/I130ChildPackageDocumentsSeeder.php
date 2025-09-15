<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class I130ChildPackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'I130_CHILD';

        // Ensure three tier packages exist for category 4
        $features = [
            'User Account Creation',
            'Step by step guidance',
            '100% User Satisfaction Guarantee',
            'Chat Support',
            'Filling all required Forms',
            'Print and Shipped',
            'Case Manager assigned',
            'Legal review by an experienced immigration attorney',
        ];

        $now = now();
        // Insert packages per tier if missing (avoid ON CONFLICT dependency)
        if (Schema::hasColumn('packages','visa_type')) {
            $existingByCode = DB::table('packages')
                ->where('visa_type',$visaType)
                ->pluck('id','code')->all();

            foreach ([
                ['code'=>'basic','name'=>'Basic','price_cents'=>29999],
                ['code'=>'advanced','name'=>'Advanced','price_cents'=>49999],
                ['code'=>'premium','name'=>'Premium','price_cents'=>69999],
            ] as $tier) {
                if (!array_key_exists($tier['code'], $existingByCode)) {
                    $row = [
                        'visa_type' => $visaType,
                        'code' => $tier['code'],
                        'name' => $tier['name'],
                        'price_cents' => $tier['price_cents'],
                        'features' => json_encode($features),
                        'active' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    if (Schema::hasColumn('packages','visa_category_id')) {
                        $row['visa_category_id'] = 4;
                    }
                    DB::table('packages')->insert($row);
                }
            }
        }

        $packageIds = DB::table('packages')->where('visa_type',$visaType)->pluck('id')->all();
        if (empty($packageIds)) return;

        $canonical = collect([
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1],
            ['code'=>'FORM_I130','label'=>'Form I-130 (Petition for Alien Relative)','required'=>1],

            // Petitioner (US Citizen or Green Card Holder)
            ['code'=>'PETITIONER_STATUS_PROOF','label'=>'Proof of U.S. citizenship OR green card (front and back) — one required','required'=>1],
            ['code'=>'PETITIONER_ID','label'=>'Government-issued photo ID (passport, driver’s license, etc.)','required'=>1],

            // Proof of Relationship
            ['code'=>'REL_PRIMARY_BIRTH_CERT','label'=>'Child’s birth certificate showing parent’s name','required'=>1],
            ['code'=>'REL_ADOPTION','label'=>'If adopted: final adoption decree + proof of legal custody and residency before age 16','required'=>0],
            ['code'=>'REL_STEPPARENT','label'=>'If step-parent: marriage certificate of child’s parent + proof of termination of prior marriages','required'=>0],
            ['code'=>'REL_OUT_OF_WEDLOCK','label'=>'If father and child born out of wedlock: proof of legitimation or evidence of emotional/financial relationship','required'=>0],
            ['code'=>'REL_DNA','label'=>'Paternal or Maternal DNA test result from an approved USCIS laboratory (if available/applicable)','required'=>0],

            // Secondary Proof of Relationship (choose at least five — enforce via UI/validation)
            ['code'=>'SEC_DNA_TEST','label'=>'DNA test result from USCIS-approved lab','required'=>0],
            ['code'=>'SEC_MEDICAL_RECORDS','label'=>'Medical or health records showing parent and child’s names','required'=>0],
            ['code'=>'SEC_CHURCH_RECORDS','label'=>'Church or religious documents listing parent and child’s name','required'=>0],
            ['code'=>'SEC_INSURANCE','label'=>'Insurance records naming both petitioner and beneficiary','required'=>0],
            ['code'=>'SEC_EMPLOYMENT_RECORDS','label'=>'Employment records showing parent and child’s names','required'=>0],
            ['code'=>'SEC_TAX_FINANCIAL','label'=>'Financial records (tax returns) listing parent and child’s name','required'=>0],
            ['code'=>'SEC_CENSUS_TRIBAL','label'=>'Census or tribal records in both names','required'=>0],
            ['code'=>'SEC_GOVT_IDS','label'=>'Government records or identification documents showing both names','required'=>0],
            ['code'=>'SEC_ONGOING_SUPPORT','label'=>'Proof of ongoing parent and child relationship (money transfers/remittance, etc.)','required'=>0],

            // Beneficiary (Child)
            ['code'=>'CHILD_PASSPORT','label'=>'Child’s passport biographic page','required'=>1],

            // Additional Documents
            ['code'=>'NAME_CHANGE_DOCS','label'=>'Marriage certificate, divorce decree, adoption decree, or court order for any name changes (parent or child) — if applicable','required'=>0],
        ])->map(fn($r)=> (object) $r);

        $rows = [];
        foreach ($packageIds as $pid) {
            foreach ($canonical as $c) {
                $rows[] = [
                    'package_id' => $pid,
                    'code' => strtoupper($c->code),
                    'label' => $c->label,
                    'required' => (bool)($c->required ?? 0),
                    'translation_possible' => false,
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('package_required_documents')->upsert(
            $rows,
            ['package_id','code'],
            ['label','required','translation_possible','active','updated_at']
        );
    }
}
