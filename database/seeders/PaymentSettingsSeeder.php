<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentSetting;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'gateway' => 'stripe',
                'is_active' => false,
                'credentials' => [
                    'publishable_key' => '',
                    'secret_key' => '',
                    'webhook_secret' => '',
                ],
                'settings' => [
                    'currency' => 'usd',
                    'mode' => 'test', // test or live
                ],
            ],
            [
                'gateway' => 'paypal',
                'is_active' => false,
                'credentials' => [
                    'client_id' => '',
                    'client_secret' => '',
                    'webhook_id' => '',
                ],
                'settings' => [
                    'currency' => 'USD',
                    'mode' => 'sandbox', // sandbox or live
                ],
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentSetting::updateOrCreate(
                ['gateway' => $gateway['gateway']],
                $gateway
            );
        }
    }
}
