<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class N400PackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'N400';

        $packageIds = DB::table('packages')->where('visa_type',$visaType)->pluck('id')->all();
        if (empty($packageIds)) return;

        // Curated list from requirements
        $canonical = collect([
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1],
            ['code'=>'FORM_N400','label'=>'Form N-400 (Application for Naturalization)','required'=>1],

            // Identity Documents
            ['code'=>'GREEN_CARD','label'=>'Copy of valid Permanent Resident Card (front/back)','required'=>1],
            ['code'=>'STATE_ID','label'=>'State-issued ID (driver’s license or state ID)','required'=>1],

            // Biographic & Civil Documents
            ['code'=>'BIRTH_CERT','label'=>'Birth certificate (if needed for special cases)','required'=>0],
            ['code'=>'MARRIAGE_CERT','label'=>'Marriage certificate(s) (if applicable)','required'=>0],
            ['code'=>'DIVORCE_OR_TERMINATION','label'=>'Divorce decrees/annulment papers/death certificates (for prior marriages)','required'=>0],
            ['code'=>'NAME_CHANGE_DOCS','label'=>'Name change documents (if legally changed name)','required'=>0],

            // Residence & Eligibility
            ['code'=>'CONTINUOUS_RESIDENCE','label'=>'Proof of continuous residence & physical presence (leases/mortgages/utility/employment)','required'=>1],
            ['code'=>'SELECTIVE_SERVICE','label'=>'Selective Service registration proof (if required)','required'=>0],
            ['code'=>'TAX_TRANSCRIPTS','label'=>'Certified tax transcripts or returns (as relevant)','required'=>0],

            // Family-related (if applicable)
            ['code'=>'SPOUSE_USC_PROOF','label'=>'Proof of spouse’s U.S. citizenship (if applying based on 3-year marriage rule)','required'=>0],
            ['code'=>'ONGOING_MARITAL_UNION','label'=>'Proof of ongoing marital union (joint accounts/leases/children birth certs)','required'=>0],

            // Military (if applicable)
            ['code'=>'FORM_N426','label'=>'Form N-426 (Request for Certification of Military/National Guard Service)','required'=>0],
            ['code'=>'MILITARY_RECORDS','label'=>'Military records (DD-214, NGB-22, discharge papers)','required'=>0],

            // Other
            ['code'=>'ADDITIONAL_DOCS_OPTIONAL','label'=>'Any other additional documents (optional)','required'=>0],
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
