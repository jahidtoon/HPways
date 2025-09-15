<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VisaCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Marriage Green Card inside the United States',
            'Parent/Child Adjustment of Status inside the United States',
            'Petition for a Spouse outside the United States',
            'Petition for a Child/Parent/Sibling outside the United States',
            'K1 Fiancé Visa - (USCIS Petition only)',
            'Petition to Remove Conditions on Your Conditional Residence',
            'Renew or Replace your Permanent Residence Card',
            'DACA Renewal Request',
            'Application for US Citizenship through Naturalization',
        ];

        foreach ($categories as $category) {
            DB::table('visa_categories')->insert([
                'name' => $category,
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
