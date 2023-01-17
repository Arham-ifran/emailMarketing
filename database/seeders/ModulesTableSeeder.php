<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('modules')->delete();
        \DB::table('modules')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Roles',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Sub-Admins',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Users',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'FAQs',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),

            4 =>
            array(
                'id' => 5,
                'name' => 'Email Templates',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Email Template Labels',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'CMS Pages',
                'status' => 1,
                'created_at' => '2019-12-11 05:00:00',
                'updated_at' => '2019-12-11 05:00:00',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'CMS Page Labels',
                'status' => 1,
                'created_at' => '2019-12-11 05:00:00',
                'updated_at' => '2019-12-11 05:00:00',
            ),

            9 =>
            array(
                'id' => 10,
                'name' => 'Home Contents',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Home Content Labels',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Contact Us Queries',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Payment',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'SMS Campaigns',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Email Campaigns',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Features',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'About Us',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Services',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'Countries VAT Listing',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'Home Contents',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'Email Campaign Templates',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'Site Settings',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'Split Testing Campaigns',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'Packages',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'Package Features',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'Pay As You Go Package Settings',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'Languages',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'Language Translations',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            28 =>
            array(
                'id' => 29,
                'name' => 'Label Translations',
                'status' => 1,
                'created_at' => '2019-11-29 23:39:55',
                'updated_at' => '2019-11-29 23:39:55',
            ),
            29 =>
            array(
                'id' => 30,
                'name' => 'Contact Us Queries',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
            30 =>
            array(
                'id' => 31,
                'name' => 'Lawful Interception',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-28 05:00:00',
            ),
        ));
    }
}
