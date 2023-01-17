<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AboutUsContentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('about_us_contents')->delete();

        \DB::statement(
            "INSERT INTO `about_us_contents` (`id`, `name`, `description`, `image`, `image_position`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (0, 'Business Has Only Two Functions Marketing And Innovation', 'Aenean sed nibh a magna posuere tempor. Nunc faucibus pellentesque nunc in aliquet. Donec congue, nunc.', 'https://emarketing.arhamsoft.info/storage/about-us-page/feature-feature7.png', 2, 1, '2021-10-29 20:57:32', '2021-10-29 21:07:40', NULL),
            (1, 'Results-Driven Email Marketing', 'Lorem ipsum dolor sit amet conse ctetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco labori.\r\n\r\nLorem ipsum dolor sit amet conse ctetur adipisicing elit\r\nLorem ipsum dolor sit amet conse ctetur adipisicing elit\r\nLorem ipsum dolor sit amet conse ctetur adipisicing elit\r\nLorem ipsum dolor sit amet conse ctetur adipisicing elit', 'https://emarketing.arhamsoft.info/storage/about-us-page/feature-feature2.png', 2, 1, '2021-10-29 20:58:40', '2021-10-29 21:07:31', NULL),
            (5, 'Email Campaigns', 'Nulla eleifend pulvinar purus, molestie euismod odio imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in non lorem. Donec interdum.', 'https://emarketing.arhamsoft.info/storage/about-us-page/feature-email.png', NULL, 1, '2021-10-29 21:17:31', '2021-10-29 21:17:31', NULL),
            (6, 'Sms Campaigns', 'Nulla eleifend pulvinar purus, molestie euismod odio\r\n                    imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in\r\n                    non lorem. Donec interdum.', 'https://emarketing.arhamsoft.info/storage/about-us-page/feature-sms.png', NULL, 1, '2021-10-29 21:20:56', '2021-10-29 21:20:56', NULL),
            (7, 'Campaigns Analytics', 'Nulla eleifend pulvinar purus, molestie euismod odio\r\n                    imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in\r\n                    non lorem. Donec interdum.', 'https://emarketing.arhamsoft.info/storage/about-us-page/feature-analytics.png', NULL, 1, '2021-10-29 21:21:19', '2021-12-17 18:38:30', NULL);
            "
        );
    }
}
