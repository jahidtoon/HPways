<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DacaPackageDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages') || !Schema::hasTable('package_required_documents')) {
            return;
        }

        $visaType = 'DACA';

        $packageIds = DB::table('packages')->where('visa_type',$visaType)->pluck('id')->all();
        if (empty($packageIds)) return;

        // Build curated list from requirements
        $canonical = collect([
            // Forms
            ['code'=>'FORM_G1145','label'=>'Form G-1145 (E-notification of Application)','required'=>1],
            ['code'=>'FORM_I821D','label'=>'Form I-821D (Consideration of Deferred Action for Childhood Arrivals - Renewal)','required'=>1],
            ['code'=>'FORM_I765','label'=>'Form I-765 (Application for Employment Authorization)','required'=>1],
            ['code'=>'FORM_I765WS','label'=>'Form I-765WS (Worksheet - financial need for work authorization)','required'=>1],

            // Identity / status proofs
            ['code'=>'CURRENT_EAD_COPY','label'=>'Copy of current Employment Authorization Document (EAD)','required'=>1],
            ['code'=>'GOVT_ID','label'=>'Government-issued photo ID (passport, driver’s license, etc.)','required'=>1],

            // Presence / residence
            ['code'=>'NO_DEPARTURE_PROOF','label'=>'At least one document showing you have not left the U.S. since last approval','required'=>1],

            // Updated records
            ['code'=>'UPDATED_SCHOOL_EMPLOYMENT_MEDICAL','label'=>'Updated school, employment, or medical records','required'=>0],
            ['code'=>'UPDATED_RENT_BILLS_BANK','label'=>'Updated rent, utility bills, or bank statements','required'=>0],

            // Criminal / immigration (if applicable)
            ['code'=>'COURT_DISPOSITIONS_NEW','label'=>'Certified court dispositions for any new arrests/charges/convictions','required'=>0],
            ['code'=>'IMMIGRATION_RECORDS_NEW','label'=>'Immigration documents for any new proceedings/filings/notices','required'=>0],

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
