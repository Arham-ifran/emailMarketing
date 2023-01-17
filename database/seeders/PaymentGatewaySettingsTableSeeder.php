<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentGatewaySettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('payment_gateway_settings')->delete();

        \DB::table('payment_gateway_settings')->insert(array(
            0 =>
            array(
                'id' => 1,
                'paypal_sandbox_api_username' => NULL,
                'paypal_sandbox_api_password' => NULL,
                'paypal_sandbox_api_secret' => NULL,
                'paypal_sandbox_api_base_url' => NULL,
                'paypal_live_api_username' => NULL,
                'paypal_live_api_password' => NULL,
                'paypal_live_api_secret' => NULL,
                'paypal_live_api_base_url' => NULL,
                'paypal_mode' => 'sandbox',
                'paypal_status' => 0,
                'mollie_status' => 1,
                'mollie_mode' => 'sandbox',
                'mollie_live_api_key' => NULL,
                'mollie_sandbox_api_key' => 'test_T9PfwQ7NJ37VFkV4UNVs9tQv7SkKzf',
                'deleted_at' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
            ),
        ));
    }
}
