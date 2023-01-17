<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PackageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('package_settings')->delete();

        \DB::statement(
            "INSERT INTO `package_settings` (`id`, `name`, `info`, `module`, `start_range`, `end_range`, `price_without_vat`, `price_with_vat`, `is_voucher_setting`, `status`, `created_at`, `updated_at`) VALUES
            (1, 'Basic Email', 'Email Span 1', 2, 1, 1000, 1.00, 0.50, 0, 1, '2022-01-04 14:08:33', '2022-01-04 14:08:33'),
            (2, 'Standard Email', 'Email Span 2', 2, 1001, 2000, 2.00, 0.00, 0, 1, '2022-01-04 14:12:16', '2022-01-04 14:12:16'),
            (3, 'Pro Email', 'Email Span 3', 2, 2001, NULL, 3.00, 0.00, 0, 1, '2022-01-04 14:13:52', '2022-01-04 14:12:52'),
            (4, 'Basic SMS', 'SMS Span 1', 3, 1, 1000, 1.00, 0.00, 0, 1, '2022-01-04 14:17:10', '2022-01-04 14:17:10'),
            (5, 'Standard SMS', 'SMS Span 2', 3, 1001, 2000, 2.00, 1.00, 0, 1, '2022-01-04 14:17:40', '2022-01-04 14:17:40'),
            (6, 'Pro SMS', 'SMS Span 3', 3, 3001, NULL, 3.00, 1.50, 0, 1, '2022-01-04 14:18:54', '2022-01-04 14:18:54'),
            (7, 'Basic Contact', 'Contact Span 1', 1, 1, 500, 0.50, 0.25, 0, 1, '2022-01-04 14:20:54', '2022-01-04 14:20:54'),
            (8, 'Standard Contacts', 'Contacts Span 2', 1, 501, 1000, 1.00, 0.50, 0, 1, '2022-01-04 14:21:36', '2022-01-04 14:22:14'),
            (9, 'Pro Contacts', 'Contacts Span 3', 1, 1001, NULL, 1.50, 0.75, 0, 1, '2022-01-04 14:23:58', '2022-01-04 14:23:58');
            "
        );
    }
}
