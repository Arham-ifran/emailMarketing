<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('languages')->delete();

        \DB::table('languages')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'English',
                'code' => 'en',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-09-17 15:01:31',
                'updated_at' => '2019-09-17 15:01:31',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'German',
                'code' => 'de',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-09-17 15:01:31',
                'updated_at' => '2019-09-17 15:01:31',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Italian',
                'code' => 'it',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-09-17 15:01:31',
                'updated_at' => '2019-09-17 15:01:31',
            ),
        ));
    }
}
