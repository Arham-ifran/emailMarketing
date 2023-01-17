<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('services')->delete();

        \DB::statement(
            "INSERT INTO `services` (`id`, `name`, `slug`, `description`, `image`, `status`, `deleted_at`, `created_at`, `updated_at`) VALUES
            (1, 'Email Marketing', 'mailbox-migration', 'That’s why we’ve build an email marketing software...', 'service-line-2-img.png', 1, NULL, '2020-07-23 12:05:46', '2021-12-17 17:44:35'),
            (2, 'SMS Marketing', 'cloud-migration', 'That’s why we’ve build an email marketing software...', 'service-line-2-img.png', 1, NULL, '2020-07-23 12:06:43', '2020-07-23 12:06:43'),
            (3, 'Split Testing', 'contacts-migration', 'That’s why we’ve build an email marketing software...', 'service-line-2-img.png', 1, NULL, '2020-07-23 12:07:07', '2020-07-23 12:07:07'),
            (4, 'Templates', 'calendar-migration', 'That’s why we’ve build an email marketing software that’s easy to use, save your time and get results.', 'service-line-2-img.png', 1, NULL, '2020-07-23 12:07:37', '2020-07-23 12:07:37'),
            (5, 'Analytics & Reports', 'analytics', 'That’s why we’ve build an email marketing software that’s easy to use, save your time and get results.', 'service-line-2-img.png', 1, NULL, '2020-07-23 12:07:37', '2020-07-23 12:07:37');
            "
        );
    }
}
