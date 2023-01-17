<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AboutUsTestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('about_us_testimonials')->delete();

        \DB::statement(
            "INSERT INTO `about_us_testimonials` (`id`, `name`, `message`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (0, 'Barney Stinson', 'Nulla eleifend pulvinar purus, molestie euismod odio\r\n                      imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in\r\n                      non lorem. Donec interdum. Ut sit amet erat nec nibh\r\n                      rhoncus varius in non lorem.', 1, '2021-10-29 21:59:00', '2021-10-29 21:59:46', NULL),
            (1, 'Beatrice Murphy', 'Nulla eleifend pulvinar purus, molestie euismod odio\r\nimperdiet ac. Ut sit amet erat nec nibh rhoncus varius in\r\nnon lorem. Donec interdum. Ut sit amet erat nec nibh\r\nrhoncus varius in non lorem.', 1, '2021-10-29 21:55:23', '2021-10-29 21:57:51', NULL),
            (2, 'Louis Roberts', 'Nulla eleifend pulvinar purus, molestie euismod odio\r\n                      imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in\r\n                      non lorem. Donec interdum. Ut sit amet erat nec nibh\r\n                      rhoncus varius in non lorem.', 1, '2021-10-29 21:58:21', '2021-10-29 21:59:16', NULL),
            (3, 'Rob Barney', 'Nulla eleifend pulvinar purus, molestie euismod odio\r\n                      imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in\r\n                      non lorem. Donec interdum. Ut sit amet erat nec nibh\r\n                      rhoncus varius in non lorem.', 1, '2021-10-29 21:58:34', '2021-10-29 21:59:31', NULL);
            "
        );
    }
}
