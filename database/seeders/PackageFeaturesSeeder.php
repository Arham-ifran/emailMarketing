<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PackageFeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('package_features')->delete();
        // \DB::table('package_features')->insert(array(
        //     1 => array('id' => 1, 'name' => 'Total Emails', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
        //     2 => array('id' => 2, 'name' => 'Total SMS', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
        //     3 => array('id' => 3, 'name' => 'Total Contacts for Email', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2021-12-28 08:32:34'),
        //     4 => array('id' => 4, 'name' => 'Total Contacts for SMS', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     5 => array('id' => 5, 'name' => 'Basic Email Templates', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     6 => array('id' => 6, 'name' => 'Design Email Templates', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     7 => array('id' => 7, 'name' => 'Add HTML templates', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2021-12-28 08:32:17'),
        //     8 => array('id' => 8, 'name' => 'Create SMS Templates', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     9 => array('id' => 9, 'name' => 'View Campaign Report', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2021-06-04 05:03:52'),
        //     10 => array('id' => 10, 'name' => 'Send Scheduled Campaigns', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     11 => array('id' => 11, 'name' => 'Send Recursive Campaigns', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     12 => array('id' => 12, 'name' => 'Send Split Testing Campaigns', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        //     13 => array('id' => 13, 'name' => 'Have API Access', 'info' => NULL, 'count' => 0, 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2019-09-26 15:57:00'),
        // ));

        \DB::statement(
            "INSERT INTO `package_features` (`id`, `name`, `info`, `count`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
            (1, 'Total Emails', '', 1, 1, NULL, '2019-09-26 14:57:00', '2020-05-03 23:01:58'),
            (2, 'Total SMS', '', 1, 1, NULL, '2019-09-26 14:57:00', '2020-05-03 23:01:58'),
            (3, 'Total Contacts', '', 1, 1, NULL, '2019-09-26 14:57:00', '2021-12-28 08:32:34'),
            (5, 'Basic Email Templates', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00'),
            (6, 'Design Email Templates', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00'),
            (7, 'Add HTML templates', '', 0, 1, NULL, '2019-09-26 14:57:00', '2021-12-28 08:32:17'),
            (8, 'Create SMS Templates', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00'),
            (9, 'View Campaign Report', '', 0, 1, NULL, '2019-09-26 14:57:00', '2021-06-04 04:03:52'),
            (10, 'Send Scheduled Campaigns', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00'),
            (11, 'Send Recursive Campaigns', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00'),
            (12, 'Send Split Testing Campaigns', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00'),
            (13, 'Have API Access', '', 0, 1, NULL, '2019-09-26 14:57:00', '2019-09-26 14:57:00');
            "
        );
    }
}
