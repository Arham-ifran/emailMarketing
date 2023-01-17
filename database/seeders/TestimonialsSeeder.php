<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestimonialsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('testimonials')->delete();

        \DB::statement(
            "INSERT INTO `testimonials` (`id`, `name`, `message`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
            (1, 'Sarah', 'I have had an amazing experience sending campaigns from Email Marketing.', 1, '2021-11-22 18:50:04', '2021-11-22 18:50:04', NULL),
            (2, 'sam', 'I have had an amazing experience sending campaigns from Email Marketing.', 1, '2021-12-17 18:45:42', '2021-12-17 18:45:58', NULL);
            "
        );
    }
}
