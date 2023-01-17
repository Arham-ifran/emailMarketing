<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('features')->delete();

        \DB::statement(
            "INSERT INTO `features` (`id`, `name`, `description`, `image`, `image_position`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (0, 'Content Management', 'Cras gravida bibendum dolor eu varius. Morbi fermentum velit nisl, eget vehicula lorem sodales eget. Donec quis volutpat orci. Sed ipsum felis, tristique id egestas et, convallis ac velit.', NULL, NULL, 1, '2021-10-29 19:46:22', '2021-12-17 17:59:16', NULL),
            (1, 'Content Management', 'Nulla eleifend pulvinar purus, molestie euismod odio imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in non lorem. Donec interdum, lectus in convallis pulvinar, enim elit porta sapien, vel finibus erat felis sed neque. Etiam aliquet neque sagittis erat tincidunt aliquam. Vestibulum at neque hendrerit, mollis dolor at, blandit justo. Integer ac interdum purus.', 'https://emarketing.arhamsoft.info/storage/features/feature-feature2.png', 2, 1, '2021-10-29 19:45:47', '2021-10-29 20:06:43', NULL),
            (2, 'Emails & Sms Campaigns', 'Nulla eleifend pulvinar purus, molestie euismod odio imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in non lorem. Donec interdum, lectus in convallis pulvinar, enim elit porta sapien, vel finibus erat felis sed neque. Etiam aliquet neque sagittis erat tincidunt aliquam. Vestibulum at neque hendrerit, mollis dolor at, blandit justo. Integer ac interdum purus.', 'https://emarketing.arhamsoft.info/storage/features/feature-feature3.png', 1, 1, '2021-10-29 19:44:54', '2021-10-29 20:07:23', NULL),
            (4, 'A/B Testing', 'Cras gravida bibendum dolor eu varius. Morbi fermentum velit nisl, eget vehicula lorem sodales eget. Donec quis volutpat orci. Sed ipsum felis, tristique id egestas et, convallis ac velit.', NULL, NULL, 1, '2021-10-29 19:48:04', '2021-10-29 19:48:15', NULL),
            (5, 'Drag N Drop Template Builders', 'Nulla eleifend pulvinar purus, molestie euismod odio imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in non lorem. Donec interdum, lectus in convallis pulvinar, enim elit porta sapien, vel finibus erat felis sed neque. Etiam aliquet neque sagittis erat tincidunt aliquam. Vestibulum at neque hendrerit, mollis dolor at, blandit justo. Integer ac interdum purus.', 'https://emarketing.arhamsoft.info/storage/features/feature-feature5.png', 2, 1, '2021-10-29 19:44:01', '2021-10-29 20:07:38', NULL),
            (6, 'Pre-Defined Templates', 'Nulla eleifend pulvinar purus, molestie euismod odio imperdiet ac. Ut sit amet erat nec nibh rhoncus varius in non lorem. Donec interdum, lectus in convallis pulvinar, enim elit porta sapien, vel finibus erat felis sed neque. Etiam aliquet neque sagittis erat tincidunt aliquam. Vestibulum at neque hendrerit, mollis dolor at, blandit justo. Integer ac interdum purus.', 'https://emarketing.arhamsoft.info/storage/features/feature-feature7.png', 1, 1, '2021-10-29 19:43:28', '2021-10-29 20:07:53', NULL),
            (7, 'Detailed Analytics', 'Cras gravida bibendum dolor eu varius. Morbi fermentum velit nisl, eget vehicula lorem sodales eget. Donec quis volutpat orci. Sed ipsum felis, tristique id egestas et, convallis ac velit.', '', NULL, 1, NULL, '2021-10-29 18:50:23', NULL);
            "
        );
    }
}
