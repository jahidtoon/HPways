<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'code' => 'basic',
                'name' => 'Basic',
                'price_cents' => 46999,
                'features' => [
                    'Account setup guidance',
                    'Step-by-step instructions',
                    'Satisfaction guarantee',
                    'Chat support',
                    'All required USCIS forms prepared',
                    'Print & ship packet',
                    'Case manager assigned',
                ],
            ],
            [
                'code' => 'advanced',
                'name' => 'Advanced',
                'price_cents' => 79999,
                'features' => [
                    'Everything in Basic',
                    'Attorney legal review',
                    'Translation of required documents',
                    'RFE response support',
                ],
            ],
            [
                'code' => 'premium',
                'name' => 'Premium',
                'price_cents' => 109999,
                'features' => [
                    'Everything in Advanced',
                    'One video consultation with attorney',
                    'USCIS interview preparation kit',
                ],
            ],
        ];

        // Global packages (visa_type null) for every category (can later clone per visa_type if needed)
        foreach ($packages as $package) {
            DB::table('packages')->insert([
                'visa_category_id' => null,
                'visa_type' => null,
                'code' => $package['code'],
                'name' => $package['name'],
                'price_cents' => $package['price_cents'],
                'features' => json_encode($package['features']),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
