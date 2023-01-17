<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('services')->delete();
        
        \DB::table('services')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Mailbox Migration',
                'slug' => 'mailbox-migration',
                'description' => 'A cloud-based solution to quickly transfer your emails without data loss and business disruption.
                We ensure virus-free migration of mailboxes and email archives in a simplified way. Save time, money and resources.
                ',
                'image' => 'service-line-2-img.png',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-23 08:05:46',
                'updated_at' => '2020-07-23 08:05:46',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Cloud Migration',
                'slug' => 'cloud-migration',
                'description' => 'Bring ease by removing the complexity factor while transferring massive data in the cloud environment.
                Migrate multiple workloads from cloud storage like G Drive, One Drives, Dropbox, and more without any interruption.
                ',
                'image' => 'service-line-2-img.png',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-23 08:06:43',
                'updated_at' => '2020-07-23 08:06:43',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Contacts Migration',
                'slug' => 'contacts-migration',
                'description' => 'Share contacts between Gmail, Hotmail and Yahoo to make them available for all users so that everyone can seamlessly get on the business.  
                Move Immunity offers unsurpassed functionality in any secure, stable environment.',
                'image' => 'service-line-2-img.png',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-23 08:07:07',
                'updated_at' => '2020-07-23 08:07:07',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Calendar Migration',
                'slug' => 'calendar-migration',
                'description' => 'An intuitive solution for individuals and businesses to migrate Calendar events and resources from one account to another.
                Move Google, Hotmail or Yahoo Calendars to new accounts without disrupting the links between calendar entries.',
                'image' => 'service-line-2-img.png',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-23 08:07:37',
                'updated_at' => '2020-07-23 08:07:37',
            ),
        ));
        
        
    }
}