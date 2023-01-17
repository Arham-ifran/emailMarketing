<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Hash;
class AdminsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('admins')->delete();
        
        \DB::table('admins')->insert(array (
            0 => 
            array (
                'id' => 1,
                'role_id' => 1,
                'name' => 'Super Admin',
                'email' => 'admin@arhamsoft.com',
                'password' => Hash::make('12345678'),
                'original_password' => '12345678',
                'profile_image' => 'profile-image-5e15add8bd39f.png',
                'remember_token' => '',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-09-17 19:47:46',
                'updated_at' => '2020-02-13 18:39:57',
            ),
        ));
        
        
    }
}