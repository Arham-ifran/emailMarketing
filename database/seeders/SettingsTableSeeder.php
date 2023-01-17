<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('settings')->delete();

        //         \DB::table('settings')->insert(array(
        //             0 =>
        //             array(
        //                 'id' => 1,
        //                 'option_name' => 'site_title',
        //                 'option_value' => 'Email marketing',
        //             ),
        //             1 =>
        //             array(
        //                 'id' => 2,
        //                 'option_name' => 'office_address',
        //                 'option_value' => 'Erftstraße 15, 38120 Braunschweig Germany',
        //             ),
        //             2 =>
        //             array(
        //                 'id' => 3,
        //                 'option_name' => 'contact_number',
        //                 'option_value' => '+49 1579 2301998',
        //             ),
        //             3 =>
        //             array(
        //                 'id' => 4,
        //                 'option_name' => 'contact_email',
        //                 'option_value' => 'info@emarketing.com',
        //             ),
        //             4 =>
        //             array(
        //                 'id' => 5,
        //                 'option_name' => 'operating_hours',
        //                 'option_value' => 'Monday to Friday 9 AM - 4 PM
        // (UTC+02:00)',
        //             ),
        //             5 =>
        //             array(
        //                 'id' => 6,
        //                 'option_name' => 'instagram',
        //                 'option_value' => 'https://www.instagram.com/',
        //             ),
        //             6 =>
        //             array(
        //                 'id' => 7,
        //                 'option_name' => 'facebook',
        //                 'option_value' => 'https://www.facebook.com',
        //             ),
        //             7 =>
        //             array(
        //                 'id' => 8,
        //                 'option_name' => 'twitter',
        //                 'option_value' => 'https://twitter.com',
        //             ),
        //             8 =>
        //             array(
        //                 'id' => 9,
        //                 'option_name' => 'linkedin',
        //                 'option_value' => 'https://linkedin.com',
        //             ),

        //             13 =>
        //             array(
        //                 'id' => 14,
        //                 'option_name' => 'company_name',
        //                 'option_value' => 'TIMmunity GmbH',
        //             ),
        //             14 =>
        //             array(
        //                 'id' => 15,
        //                 'option_name' => 'company_registration_number',
        //                 'option_value' => 'DE327709293',
        //             ),
        //             15 =>
        //             array(
        //                 'id' => 16,
        //                 'option_name' => 'website',
        //                 'option_value' => 'https://timmunity.org',
        //             ),
        //             16 =>
        //             array(
        //                 'id' => 17,
        //                 'option_name' => 'commercial_register_address',
        //                 'option_value' => 'Handelsregister Braunschweig HRB 208156',
        //             ),

        //             18 =>
        //             array(
        //                 'id' => 19,
        //                 'option_name' => 'company_street',
        //                 'option_value' => 'Erftstr. 15',
        //             ),
        //             19 =>
        //             array(
        //                 'id' => 20,
        //                 'option_name' => 'company_zip_code',
        //                 'option_value' => '38120',
        //             ),
        //             20 =>
        //             array(
        //                 'id' => 21,
        //                 'option_name' => 'company_city',
        //                 'option_value' => 'Braunschweig',
        //             ),
        //             21 =>
        //             array(
        //                 'id' => 22,
        //                 'option_name' => 'company_country',
        //                 'option_value' => 'Germany',
        //             ),
        //             22 =>
        //             array(
        //                 'id' => 23,
        //                 'option_name' => 'bank_name',
        //                 'option_value' => 'Commerzbank',
        //             ),
        //             23 =>
        //             array(
        //                 'id' => 24,
        //                 'option_name' => 'iban',
        //                 'option_value' => 'DE12 2704 0080 0480 6725 00',
        //             ),
        //             24 =>
        //             array(
        //                 'id' => 25,
        //                 'option_name' => 'code',
        //                 'option_value' => 'COBADEFFXXX',
        //             ),
        //             // ===============
        //             25 =>
        //             array(
        //                 'id' => 26,
        //                 'option_name' => 'user_deletion_days',
        //                 'option_value' => '30',
        //             ),

        //             26 =>
        //             array(
        //                 'id' => 27,
        //                 'option_name' => 'user_inactivation_days',
        //                 'option_value' => '30',
        //             ),
        //             27 =>
        //             array(
        //                 'id' => 28,
        //                 'option_name' => 'user_soft_deletion_days',
        //                 'option_value' => '15',
        //             ),
        //             28 =>
        //             array(
        //                 'id' => 29,
        //                 'option_name' => 'first_notification',
        //                 'option_value' => '5',
        //             ),
        //             29 =>
        //             array(
        //                 'id' => 30,
        //                 'option_name' => 'second_notification',
        //                 'option_value' => '3',
        //             ),
        //             30 =>
        //             array(
        //                 'id' => 31,
        //                 'option_name' => 'third_notification',
        //                 'option_value' => '1',
        //             ),
        //             31 =>
        //             array(
        //                 'id' => 32,
        //                 'option_name' => 'vat_id',
        //                 'option_value' => '56432',
        //             ),
        //             32 =>
        //             array(
        //                 'id' => 33,
        //                 'option_name' => 'vat',
        //                 'option_value' => '34',
        //             ),
        //             33 =>
        //             array(
        //                 'id' => 34,
        //                 'option_name' => 'mailgun_username',
        //                 'option_value' => 'postmaster@sandboxa2397a17d4a14e958c3e2b11f83bd129.mailgun.org',
        //             ),
        //             34 =>
        //             array(
        //                 'id' => 35,
        //                 'option_name' => 'mailgun_password',
        //                 'option_value' => 'cdafc1b023cb57faef7d96ad44201b2b-2bf328a5-18249894',
        //             ),
        //             35 =>
        //             array(
        //                 'id' => 36,
        //                 'option_name' => 'mail_from_address',
        //                 'option_value' => 'as.bounce.emails@gmail.com',
        //             ),
        //             36 =>
        //             array(
        //                 'id' => 37,
        //                 'option_name' => 'mail_from_app_name',
        //                 'option_value' => 'Email Marketing',
        //             ),
        //             37 =>
        //             array(
        //                 'id' => 38,
        //                 'option_name' => 'twilio_sid',
        //                 'option_value' => 'ACaf811c275428098ce453d2c4ee0157ed',
        //             ),
        //             38 =>
        //             array(
        //                 'id' => 39,
        //                 'option_name' => 'twilio_auth_token',
        //                 'option_value' => '0d4a4fcb72797330f31d8be06ff34698',
        //             ),
        //             39 =>
        //             array(
        //                 'id' => 40,
        //                 'option_name' => 'twilio_number',
        //                 'option_value' => '+13097278940',
        //             ),
        //             40 =>
        //             array(
        //                 'id' => 41,
        //                 'option_name' => 'imap_username',
        //                 'option_value' => 'as.bounce.emails@gmail.com',
        //             ),
        //             41 =>
        //             array(
        //                 'id' => 42,
        //                 'option_name' => 'imap_password',
        //                 'option_value' => 'abhfkgfurxjdaywg',
        //             ),
        //         ));

        \DB::statement(
            "INSERT INTO `settings` (`id`, `option_name`, `option_value`, `created_at`, `updated_at`) VALUES
    (1, 'site_title', 'Email marketing', NULL, NULL),
    (2, 'office_address', 'Erftstraße 15, 38120 Braunschweig Germany', NULL, NULL),
    (3, 'contact_number', '+49 1579 2301998', NULL, NULL),
    (4, 'contact_email', 'info@moveimmunity.com', NULL, NULL),
    (5, 'operating_hours', 'Monday to Friday 9 AM - 4 PM\r\n(UTC+02:00)', NULL, NULL),
    (6, 'instagram', 'https://www.instagram.com', NULL, NULL),
    (7, 'facebook', 'https://www.facebook.com', NULL, NULL),
    (8, 'twitter', 'https://twitter.com', NULL, NULL),
    (9, 'linkedin', 'https://linkedin.com', NULL, NULL),
    (14, 'company_name', 'TIMmunity GmbH', NULL, NULL),
    (15, 'company_registration_number', 'DE327709293', NULL, NULL),
    (16, 'website', 'https://timmunity.org', NULL, NULL),
    (17, 'commercial_register_address', 'Handelsregister Braunschweig HRB 208156', NULL, NULL),
    (19, 'company_street', 'Erftstr. 15', NULL, NULL),
    (20, 'company_zip_code', '38120', NULL, NULL),
    (21, 'company_city', 'Braunschweig', NULL, NULL),
    (22, 'company_country', 'Germany', NULL, NULL),
    (23, 'bank_name', 'Commerzbank', NULL, NULL),
    (24, 'iban', 'DE12 2704 0080 0480 6725 00', NULL, NULL),
    (25, 'code', 'COBADEFFXXX', NULL, NULL),
    (26, 'user_deletion_days', '15', NULL, NULL),
    (28, 'user_inactivation_days', '30', NULL, NULL),
    (29, 'user_soft_deletion_days', '15', NULL, NULL),
    (30, 'first_notification', '5', NULL, NULL),
    (31, 'second_notification', '3', NULL, NULL),
    (32, 'third_notification', '1', NULL, NULL),
    (33, 'vat_id', '56432', NULL, NULL),
    (34, 'vat', '34', NULL, NULL),
    (35, 'mailgun_username', 'postmaster@sandboxa2397a17d4a14e958c3e2b11f83bd129.mailgun.org', NULL, NULL),
    (36, 'mailgun_password', 'cdafc1b023cb57faef7d96ad44201b2b-2bf328a5-18249894', NULL, NULL),
    (37, 'mail_from_address', 'no-reply@marketingemail.biz', NULL, NULL),
    (38, 'mail_from_app_name', 'Email Marketing', NULL, NULL),
    (39, 'twilio_sid', 'ACaf811c275428098ce453d2c4ee0157ed', NULL, NULL),
    (40, 'twilio_auth_token', '0d4a4fcb72797330f31d8be06ff34698', NULL, NULL),
    (41, 'twilio_number', '+13097278940', NULL, NULL),
    (42, 'imap_username', 'as.bounce.emails@gmail.com', NULL, NULL),
    (43, 'imap_password', 'abhfkgfurxjdaywg', NULL, NULL),
    (44, 'payment_relief_days', '8', NULL, NULL);
    "
        );
    }
}
