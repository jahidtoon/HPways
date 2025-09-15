<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackagesSeeder extends Seeder
{
    public function run()
    {
        $packages = [
            [
                'name' => 'Basic',
                'price' => 469.99,
                'features' => [
                    'User Account Creation',
                    'Step by step guidance',
                    '100% User Satisfaction Guarantee',
                    'Chat Support',
                    'Filling all required Forms',
                    'Print and Shipped',
                    'Case Manager assigned',
                    'Legal review by an experienced immigration attorney',
                ],
            ],
            [
                'name' => 'Advanced',
                'price' => 799.99,
                'features' => [
                    'User Account Creation',
                    'Step by step guidance',
                    '100% User Satisfaction Guarantee',
                    'Chat Support',
                    'Filling all required Forms',
                    'Print and Shipped',
                    'Case Manager assigned',
                    'Legal review by an experienced immigration attorney',
                    'Translation of all required documents',
                    'Post submission support for USCIS RFE',
                ],
            ],
            [
                'name' => 'Premium',
                'price' => 1099.99,
                'features' => [
                    'User Account Creation',
                    'Step by step guidance',
                    '100% User Satisfaction Guarantee',
                    'Chat Support',
                    'Filling all required Forms',
                    'Print and Shipped',
                    'Case Manager assigned',
                    'Legal review by an experienced immigration attorney',
                    'Translation of all required documents',
                    'Post submission support for USCIS RFE',
                    'One-time video consultation with an immigration attorney',
                    'USCIS interview Preparation kit',
                ],
            ],
        ];

        $visaCategories = DB::table('visa_categories')->get();
        foreach ($visaCategories as $category) {
            foreach ($packages as $package) {
                DB::table('packages')->insert([
                    'visa_category_id' => $category->id,
                    'name' => $package['name'],
                    'price' => $package['price'],
                    'description' => null,
                    'features' => json_encode($package['features']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
