<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RightsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('rights')->delete();
        \DB::table('rights')->insert(array(
            0 =>
            array(
                'id' => 1,
                'module_id' => 1,
                'name' => 'Roles Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:19',
            ),
            1 =>
            array(
                'id' => 2,
                'module_id' => 1,
                'name' => 'Create Role',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            2 =>
            array(
                'id' => 3,
                'module_id' => 1,
                'name' => 'Edit Role',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            3 =>
            array(
                'id' => 4,
                'module_id' => 1,
                'name' => 'Delete Role',
                'status' => 1,
                'created_at' => '2020-09-11 13:42:17',
                'updated_at' => '2020-09-11 13:42:17',
            ),
            4 =>
            array(
                'id' => 5,
                'module_id' => 2,
                'name' => 'Sub-Admins Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            5 =>
            array(
                'id' => 6,
                'module_id' => 2,
                'name' => 'Create Sub-Admin',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            6 =>
            array(
                'id' => 7,
                'module_id' => 2,
                'name' => 'Edit Sub-Admin',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            7 =>
            array(
                'id' => 8,
                'module_id' => 2,
                'name' => 'Delete Sub-Admin',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            8 =>
            array(
                'id' => 9,
                'module_id' => 2,
                'name' => 'Edit Profile',
                'status' => 1,
                'created_at' => '2019-09-28 05:00:00',
                'updated_at' => '2019-09-21 05:00:00',
            ),
            9 =>
            array(
                'id' => 10,
                'module_id' => 22,
                'name' => 'Site Setting',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            10 =>
            array(
                'id' => 11,
                'module_id' => 3,
                'name' => 'Users Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            11 =>
            array(
                'id' => 12,
                'module_id' => 3,
                'name' => 'Create User',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            12 =>
            array(
                'id' => 13,
                'module_id' => 3,
                'name' => 'Edit User',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            13 =>
            array(
                'id' => 14,
                'module_id' => 3,
                'name' => 'Delete User',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            // newstart

            14 =>
            array(
                'id' => 66,
                'module_id' => 20,
                'name' => 'Services Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            15 =>
            array(
                'id' => 67,
                'module_id' => 20,
                'name' => 'Create Service',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            16 =>
            array(
                'id' => 68,
                'module_id' => 20,
                'name' => 'Edit Service',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            17 =>
            array(
                'id' => 69,
                'module_id' => 20,
                'name' => 'Delete Service',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            18 =>
            array(
                'id' => 57,
                'module_id' => 7,
                'name' => 'CMS Pages Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            19 =>
            array(
                'id' => 58,
                'module_id' => 7,
                'name' => 'Create CMS Page',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            20 =>
            array(
                'id' => 59,
                'module_id' => 7,
                'name' => 'Edit CMS Page',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            21 =>
            array(
                'id' => 60,
                'module_id' => 7,
                'name' => 'Delete CMS Page',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            22 =>
            array(
                'id' => 61,
                'module_id' => 19,
                'name' => 'Countries Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            23 =>
            array(
                'id' => 62,
                'module_id' => 19,
                'name' => 'Create Country',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            24 =>
            array(
                'id' => 63,
                'module_id' => 19,
                'name' => 'Edit Country',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            25 =>
            array(
                'id' => 64,
                'module_id' => 19,
                'name' => 'Delete Country',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            //new end
            28 =>
            array(
                'id' => 29,
                'module_id' => 4,
                'name' => 'FAQs Listing',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            29 =>
            array(
                'id' => 30,
                'module_id' => 4,
                'name' => 'Create FAQ',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            30 =>
            array(
                'id' => 31,
                'module_id' => 4,
                'name' => 'Edit FAQ',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            31 =>
            array(
                'id' => 32,
                'module_id' => 4,
                'name' => 'Delete FAQ',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            32 =>
            array(
                'id' => 33,
                'module_id' => 14,
                'name' => 'SMS Campaign Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            33 =>
            array(
                'id' => 34,
                'module_id' => 14,
                'name' => 'SMS Campaign Report',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            34 =>
            array(
                'id' => 35,
                'module_id' => 15,
                'name' => 'Email Campaign Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            35 =>
            array(
                'id' => 36,
                'module_id' => 15,
                'name' => 'Email Campaign Report',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),

            37 =>
            array(
                'id' => 38,
                'module_id' => 17,
                'name' => 'About Us Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            38 =>
            array(
                'id' => 39,
                'module_id' => 17,
                'name' => 'Create About Us',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            39 =>
            array(
                'id' => 40,
                'module_id' => 17,
                'name' => 'Edit About Us',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            40 =>
            array(
                'id' => 41,
                'module_id' => 17,
                'name' => 'Delete About Us',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            41 =>
            array(
                'id' => 42,
                'module_id' => 17,
                'name' => 'Testimonials Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            42 =>
            array(
                'id' => 43,
                'module_id' => 17,
                'name' => 'Create Testimonial',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            43 =>
            array(
                'id' => 44,
                'module_id' => 17,
                'name' => 'Edit Testimonial',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            44 =>
            array(
                'id' => 45,
                'module_id' => 17,
                'name' => 'Delete Testimonial',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            45 =>
            array(
                'id' => 70,
                'module_id' => 20,
                'name' => 'Home Contents Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            46 =>
            array(
                'id' => 71,
                'module_id' => 20,
                'name' => 'Create Home Content',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            47 =>
            array(
                'id' => 72,
                'module_id' => 20,
                'name' => 'Edit Home Content',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            48 =>
            array(
                'id' => 73,
                'module_id' => 20,
                'name' => 'Delete Home Content',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            49 =>
            array(
                'id' => 50,
                'module_id' => 16,
                'name' => 'Features Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            50 =>
            array(
                'id' => 51,
                'module_id' => 16,
                'name' => 'Create Feature',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            51 =>
            array(
                'id' => 52,
                'module_id' => 16,
                'name' => 'Edit Feature',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            52 =>
            array(
                'id' => 53,
                'module_id' => 16,
                'name' => 'Delete Feature',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            53 =>
            array(
                'id' => 74,
                'module_id' => 20,
                'name' => 'Home Content Labels Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            54 =>
            array(
                'id' => 75,
                'module_id' => 20,
                'name' => 'Create Home Content Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            55 =>
            array(
                'id' => 76,
                'module_id' => 20,
                'name' => 'Edit Home Content Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            56 =>
            array(
                'id' => 77,
                'module_id' => 20,
                'name' => 'Delete Home Content Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            57 =>
            array(
                'id' => 82,
                'module_id' => 21,
                'name' => 'Email Campaign Templates Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            58 =>
            array(
                'id' => 83,
                'module_id' => 21,
                'name' => 'Create Email Campaign Templates',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            59 =>
            array(
                'id' => 84,
                'module_id' => 21,
                'name' => 'Edit Email Campaign Templates',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            60 =>
            array(
                'id' => 85,
                'module_id' => 21,
                'name' => 'Delete Email Campaign Templates',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            61 =>
            array(
                'id' => 86,
                'module_id' => 5,
                'name' => 'Email Templates Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            62 =>
            array(
                'id' => 87,
                'module_id' => 5,
                'name' => 'Create Email Template',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            63 =>
            array(
                'id' => 88,
                'module_id' => 5,
                'name' => 'Edit Email Template',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            64 =>
            array(
                'id' => 89,
                'module_id' => 5,
                'name' => 'Delete Email Template',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            65 =>
            array(
                'id' => 90,
                'module_id' => 6,
                'name' => 'Email Template Labels Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            66 =>
            array(
                'id' => 91,
                'module_id' => 6,
                'name' => 'Create Email Template Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            67 =>
            array(
                'id' => 92,
                'module_id' => 6,
                'name' => 'Edit Email Template Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            68 =>
            array(
                'id' => 93,
                'module_id' => 6,
                'name' => 'Delete Email Template Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            69 =>
            array(
                'id' => 94,
                'module_id' => 12,
                'name' => 'Contact Us Query Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            // 70 =>
            // array(
            //     'id' => 95,
            //     'module_id' => 12,
            //     'name' => 'Create Contact Us Query',
            //     'status' => 1,
            //     'created_at' => '2019-12-02 05:00:00',
            //     'updated_at' => '2019-12-02 05:00:00',
            // ),
            71 =>
            array(
                'id' => 96,
                'module_id' => 12,
                'name' => 'Edit Contact Us Query',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            72 =>
            array(
                'id' => 97,
                'module_id' => 23,
                'name' => 'Split Campaign Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            73 =>
            array(
                'id' => 98,
                'module_id' => 23,
                'name' => 'Split Campaign Report',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),

            74 =>
            array(
                'id' => 15,
                'module_id' => 24,
                'name' => 'Packages Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            75 =>
            array(
                'id' => 16,
                'module_id' => 24,
                'name' => 'Create Package',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            76 =>
            array(
                'id' => 17,
                'module_id' => 24,
                'name' => 'Edit Package',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            77 =>
            array(
                'id' => 18,
                'module_id' => 24,
                'name' => 'Delete Package',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            78 =>
            array(
                'id' => 19,
                'module_id' => 24,
                'name' => 'Clone Package',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            79 =>
            array(
                'id' => 20,
                'module_id' => 24,
                'name' => 'Package Subscribers listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            80 =>
            array(
                'id' => 21,
                'module_id' => 25,
                'name' => 'Package Features Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            81 =>
            array(
                'id' => 22,
                'module_id' => 25,
                'name' => 'Edit Package Feature',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            82 =>
            array(
                'id' => 99,
                'module_id' => 27,
                'name' => 'Languages Listing',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            83 =>
            array(
                'id' => 100,
                'module_id' => 27,
                'name' => 'Create Language',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            84 =>
            array(
                'id' => 101,
                'module_id' => 27,
                'name' => 'Edit Language',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            85 =>
            array(
                'id' => 102,
                'module_id' => 27,
                'name' => 'Delete Language',
                'status' => 0,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            86 =>
            array(
                'id' => 103,
                'module_id' => 28,
                'name' => 'Language Translations Listing',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            87 =>
            array(
                'id' => 104,
                'module_id' => 28,
                'name' => 'Bulk Translate',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            88 =>
            array(
                'id' => 105,
                'module_id' => 28,
                'name' => 'Edit Language Translation',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            89 =>
            array(
                'id' => 106,
                'module_id' => 28,
                'name' => 'Delete Language Translation',
                'status' => 0,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            90 =>
            array(
                'id' => 107,
                'module_id' => 28,
                'name' => 'Partial Translate',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            91 =>
            array(
                'id' => 108,
                'module_id' => 28,
                'name' => 'Advance Filters',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            92 =>
            array(
                'id' => 109,
                'module_id' => 29,
                'name' => 'Label Translations',
                'status' => 1,
                'created_at' => '2019-12-02 17:20:34',
                'updated_at' => '2019-12-02 17:20:34',
            ),
            93 =>
            array(
                'id' => 110,
                'module_id' => 26,
                'name' => 'Pay As You Go Package Settings Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            // 94 =>
            // array(
            //     'id' => 111,
            //     'module_id' => 26,
            //     'name' => 'Create Package Setting',
            //     'status' => 1,
            //     'created_at' => '2019-12-02 05:00:00',
            //     'updated_at' => '2019-12-02 05:00:00',
            // ),
            95 =>
            array(
                'id' => 112,
                'module_id' => 26,
                'name' => 'Edit Package Setting',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            // 96 =>
            // array(
            //     'id' => 113,
            //     'module_id' => 26,
            //     'name' => 'Delete Package Setting',
            //     'status' => 1,
            //     'created_at' => '2019-12-02 05:00:00',
            //     'updated_at' => '2019-12-02 05:00:00',
            // ),

            97 =>
            array(
                'id' => 114,
                'module_id' => 20,
                'name' => 'Service Labels Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            98 =>
            array(
                'id' => 115,
                'module_id' => 20,
                'name' => 'Create Service Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            99 =>
            array(
                'id' => 116,
                'module_id' => 20,
                'name' => 'Edit Service Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            100 =>
            array(
                'id' => 117,
                'module_id' => 20,
                'name' => 'Delete Service Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            101 =>
            array(
                'id' => 118,
                'module_id' => 17,
                'name' => 'About us Labels Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            102 =>
            array(
                'id' => 119,
                'module_id' => 17,
                'name' => 'Create About us Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            103 =>
            array(
                'id' => 120,
                'module_id' => 17,
                'name' => 'Edit About us Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            104 =>
            array(
                'id' => 121,
                'module_id' => 17,
                'name' => 'Delete About us Labels',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            105 =>
            array(
                'id' => 122,
                'module_id' => 7,
                'name' => 'CMS Page Labels Listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            106 =>
            array(
                'id' => 123,
                'module_id' => 7,
                'name' => 'Create CMS Page Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            107 =>
            array(
                'id' => 124,
                'module_id' => 7,
                'name' => 'Edit CMS Page Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            108 =>
            array(
                'id' => 125,
                'module_id' => 7,
                'name' => 'Delete CMS Page Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            109 =>
            array(
                'id' => 126,
                'module_id' => 16,
                'name' => 'Feature Labels Listing',
                'status' => 1,
                'created_at' => '2019-11-29 23:42:17',
                'updated_at' => '2019-11-29 23:42:17',
            ),
            110 =>
            array(
                'id' => 127,
                'module_id' => 16,
                'name' => 'Create Feature Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            111 =>
            array(
                'id' => 128,
                'module_id' => 16,
                'name' => 'Edit Feature Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            112 =>
            array(
                'id' => 129,
                'module_id' => 16,
                'name' => 'Delete Feature Label',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            113 =>
            array(
                'id' => 130,
                'module_id' => 13,
                'name' => 'Payment Setting',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            114 =>
            array(
                'id' => 131,
                'module_id' => 13,
                'name' => 'Package Payments',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            115 =>
            array(
                'id' => 132,
                'module_id' => 13,
                'name' => 'Pay as you go Package Payments',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            116 =>
            array(
                'id' => 133,
                'module_id' => 31,
                'name' => 'Lawful Interception listing',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            117 =>
            array(
                'id' => 134,
                'module_id' => 31,
                'name' => 'User Profile',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            118 =>
            array(
                'id' => 135,
                'module_id' => 31,
                'name' => 'User Payments',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            119 =>
            array(
                'id' => 136,
                'module_id' => 31,
                'name' => 'User Subscriptions',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            120 =>
            array(
                'id' => 137,
                'module_id' => 31,
                'name' => 'User SMS Campaigns',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

            121 =>
            array(
                'id' => 138,
                'module_id' => 31,
                'name' => 'User Email Campaigns',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            122 =>
            array(
                'id' => 139,
                'module_id' => 31,
                'name' => 'User Split Campaigns',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),
            123 =>
            array(
                'id' => 140,
                'module_id' => 31,
                'name' => 'Download User Data',
                'status' => 1,
                'created_at' => '2019-12-02 05:00:00',
                'updated_at' => '2019-12-02 05:00:00',
            ),

        ));
    }
}
