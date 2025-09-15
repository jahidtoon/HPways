<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PackageDescriptiveNamesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('packages')) return;

        $map = [
            'I90' => 'Application to Replace Permanent Resident Card',
            'K1' => 'Application for K1 Fiancé(e) Petition',
            'I485' => 'Application for Marriage-Based Adjustment of Status (AOS)',
            'I751' => 'Application for Removal of Conditions on residence (Marriage-Based conditional LPR) – Joint Filing Only',
            'DACA' => 'Application for Deferred Action for Childhood Arrivals – DACA (Renewals)',
            'N400' => 'Application for Naturalization',
            'I130' => 'Application for Spouse Abroad',
            'I130_PARENT' => 'Application for Parent Abroad',
            'I130_CHILD' => 'Application for Child Abroad',
            'I130_SIBLING' => 'Application for Sibling Abroad',
            'I485_PARENT' => 'Application for Parent Adjustment of Status (AOS)',
            'I485_CHILD' => 'Application for Child Adjustment of Status (AOS)'
        ];

        $now = now();
        foreach ($map as $vt => $title) {
            DB::table('packages')
                ->where('visa_type',$vt)
                ->update(['name'=>$title,'updated_at'=>$now]);
        }
    }
}
