<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class FaqsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('faqs')->delete();
        
        \DB::table('faqs')->insert(array (
            0 => 
            array (
                'id' => 1,
                'question' => 'How can I switch between the plans?',
                'answer' => 'You can change a plan anytime in your subcription setting.We always prorate charges despending on the cost of your new plan.',
                'status' => 1,
                'deleted_at' => '2019-11-27 12:54:37',
                'created_at' => '2019-09-17 15:00:42',
                'updated_at' => '2020-01-28 12:12:22',
            ),
            1 => 
            array (
                'id' => 2,
                'question' => 'How can I switch between the plans?',
                'answer' => 'You can change a plan anytime in your subscription settings.We always prorate charges depending on the cost of your new plan.',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-11-27 12:58:27',
                'updated_at' => '2020-01-28 12:13:07',
            ),
            2 => 
            array (
                'id' => 3,
                'question' => 'Do you offer reseller packages ?',
                'answer' => 'We are working on this and will hopefully launch this soon.This will allow our resellers to use their own URL & branding.',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-11-27 12:59:17',
                'updated_at' => '2020-01-28 12:13:16',
            ),
            3 => 
            array (
                'id' => 4,
                'question' => 'How can I cancel my subscription?',
                'answer' => 'Simply cancel the next payment in the subscription settings.Your current subscription will last until the end of the paid period.',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-11-27 13:00:41',
                'updated_at' => '2020-01-28 12:14:06',
            ),
            4 => 
            array (
                'id' => 5,
                'question' => 'What happens if i downgrade?',
                'answer' => 'The full list of restrictions you may run into is in the "Downgrading" article in our Help center.',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-11-27 13:01:07',
                'updated_at' => '2020-01-28 12:14:13',
            ),
            5 => 
            array (
                'id' => 6,
                'question' => 'Refunds',
                'answer' => 'If you cancel, previous charges will not be refunded. But, you may continue to use the service until the end of the term you paid for.',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2019-11-27 13:01:24',
                'updated_at' => '2020-01-28 12:14:21',
            ),
        ));
        
        
    }
}