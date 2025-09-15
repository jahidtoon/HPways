<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class I130ParentPackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'I130_PARENT';
        $packageIds = DB::table('packages')->where('visa_type',$visaType)->pluck('id')->all();
        if (empty($packageIds)) return;

        $canonical = collect([
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1],
            ['code'=>'FORM_I130','label'=>'Form I-130 (Petition for Alien Relative)','required'=>1],

            // Petitioner (US Citizen Child)
            ['code'=>'PETITIONER_USC_PROOF','label'=>'Proof of U.S. citizenship (birth cert, US passport, or Certificate of Naturalization/Citizenship)','required'=>1],
            ['code'=>'PETITIONER_ID','label'=>'Government-issued photo ID (petitioner)','required'=>1],

            // Relationship Proof
            ['code'=>'REL_PRIMARY_BIRTH_CERT','label'=>'US citizen child’s birth certificate showing parent’s name','required'=>1],
            ['code'=>'REL_ADOPTION','label'=>'If adopted: final adoption decree + proof of legal custody/residency before age 16','required'=>0],
            ['code'=>'REL_STEPPARENT','label'=>'If step-parent: marriage certificate of US citizen child’s parent + prior marriage terminations','required'=>0],
            ['code'=>'REL_OUT_OF_WEDLOCK','label'=>'If father/child born out of wedlock: proof of legitimation or emotional/financial relationship','required'=>0],
            ['code'=>'REL_DNA','label'=>'DNA test result from USCIS-approved lab (if needed)','required'=>0],

            // Secondary Relationship Proof (choose at least five — enforce via UI/validation)
            ['code'=>'SEC_DNA_TEST','label'=>'DNA test result from USCIS-approved lab','required'=>0],
            ['code'=>'SEC_MEDICAL_RECORDS','label'=>'Medical/health records showing both names','required'=>0],
            ['code'=>'SEC_CHURCH_RECORDS','label'=>'Church/religious records listing both names','required'=>0],
            ['code'=>'SEC_INSURANCE','label'=>'Insurance records naming both petitioner and beneficiary','required'=>0],
            ['code'=>'SEC_EMPLOYMENT_RECORDS','label'=>'Employment records showing both names','required'=>0],
            ['code'=>'SEC_TAX_FINANCIAL','label'=>'Financial records (tax returns) listing both names','required'=>0],
            ['code'=>'SEC_CENSUS_TRIBAL','label'=>'Census or tribal records in both names','required'=>0],
            ['code'=>'SEC_GOVT_IDS','label'=>'Government records/IDs showing both names','required'=>0],
            ['code'=>'SEC_ONGOING_SUPPORT','label'=>'Proof of ongoing relationship (money transfers/remittances, etc.)','required'=>0],

            // Beneficiary (Parent)
            ['code'=>'PARENT_PASSPORT','label'=>'Parent’s passport biographic page','required'=>1],

            // Other
            ['code'=>'ADDITIONAL_DOCS_OPTIONAL','label'=>'Any other additional documents (optional)','required'=>0],
            ['code'=>'NAME_CHANGE_DOCS','label'=>'Marriage/divorce/adoption decree or court order for any name changes (parent or child) — if applicable','required'=>0],
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
