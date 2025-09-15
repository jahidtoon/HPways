<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class I130SiblingPackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'I130_SIBLING';

        // Create packages if missing (category 4)
        if (Schema::hasColumn('packages','visa_type')) {
            $now = now();
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
            $existing = DB::table('packages')->where('visa_type',$visaType)->pluck('id','code')->all();
            foreach ([
                ['code'=>'basic','name'=>'Basic','price_cents'=>29999],
                ['code'=>'advanced','name'=>'Advanced','price_cents'=>49999],
                ['code'=>'premium','name'=>'Premium','price_cents'=>69999],
            ] as $tier) {
                if (!array_key_exists($tier['code'], $existing)) {
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

            // Petitioner (US Citizen Sibling)
            ['code'=>'PETITIONER_USC_PROOF','label'=>'Proof of U.S. citizenship (birth cert, US passport, or Certificate of Naturalization/Citizenship)','required'=>1],
            ['code'=>'PETITIONER_ID','label'=>'Government-issued photo ID (passport, driver’s license, etc.)','required'=>1],

            // Proof of Relationship
            ['code'=>'REL_PETITIONER_BC','label'=>'Petitioner birth certificate','required'=>1],
            ['code'=>'REL_SIBLING_BC','label'=>'Sibling birth certificate (showing at least one common parent)','required'=>1],
            ['code'=>'REL_HALF_SIB_PROOF','label'=>'If half-siblings: proof of termination of parents’ prior marriages','required'=>0],
            ['code'=>'REL_ADOPTED_SIB','label'=>'If adopted siblings: adoption decrees + proof both adopted by same parent(s) before age 16','required'=>0],
            ['code'=>'REL_STEP_SIB','label'=>'If step-siblings: marriage certificate of common parent to stepparent + prior marriage terminations (relationship formed before age 18)','required'=>0],

            // Secondary Proof (choose at least four — enforce via UI/validation)
            ['code'=>'SEC_DNA_TEST','label'=>'Sibling DNA test result from USCIS-approved lab','required'=>0],
            ['code'=>'SEC_MEDICAL_RECORDS','label'=>'Medical/health records showing parent and siblings’ names','required'=>0],
            ['code'=>'SEC_CHURCH_RECORDS','label'=>'Church/religious records listing parent and siblings’ names','required'=>0],
            ['code'=>'SEC_INSURANCE','label'=>'Insurance records (health/life) listing the names of both siblings','required'=>0],
            ['code'=>'SEC_EMPLOYMENT_RECORDS','label'=>'Employment records showing both siblings’ names','required'=>0],
            ['code'=>'SEC_FINANCIAL_RECORDS','label'=>'Financial records listing both siblings’ names','required'=>0],
            ['code'=>'SEC_CENSUS_TRIBAL','label'=>'Census or tribal records showing names','required'=>0],
            ['code'=>'SEC_ONGOING_SUPPORT','label'=>'Proof of ongoing sibling relationship (money transfers/remittances, etc.)','required'=>0],
        ])->map(fn($r)=> (object) $r);

        $now = now();
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
