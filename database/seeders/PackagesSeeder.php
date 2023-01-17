<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('packages')->delete();
        \DB::table('packages')->insert(array(
            0 => array('id' => 1, 'title' => 'Trial', 'sub_title' => 'Free For Specific Days', 'icon' => NULL, 'monthly_price' => 0, 'yearly_price' => 0, 'description' => '<ul><li>Max. file size = 1024 MB</li><li>Total allocated space up-to 1024 MB</li><li>Unlimited link transmissions</li><li>Transfers expire after 7 days</li><li>Files are deleted after 14 days</li><li>Re-sending, forwarding or deleting transmissions</li><li>Short link with ned.link<br></li></ul><p><br></p>', 'status' => 0, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            1 => array('id' => 2, 'title' =>  'Personal', 'sub_title' => 'It\'s free and always will be!', 'icon' => NULL, 'monthly_price' => 0, 'yearly_price' => 0, 'description' => '<ul><li>Max. file size upto 2 GB</li><li>Total allocated space upto 2 GB</li><li>Unlimited link transmissions</li><li>Transfers expire after 14 days</li><li>Files are deleted after 21 days</li><li>Re-sending, forwarding or deleting transmissions</li><li>Short link with NED.link<br></li></ul><p><br></p>', 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            2 => array('id' => 3, 'title' => 'Lite', 'sub_title' => 'Try basic features in as low as no cost!', 'icon' => NULL, 'monthly_price' => 0.42, 'yearly_price' => 4.2, 'description' => '<ul><li>Max. file size upto 2.5 GB</li><li>Total allocated space upto 10 GB</li><li>Unlimited link transmissions</li><li>Transfers expire after 31 days</li><li>Files are deleted after 93 days</li><li>Re-sending, forwarding or deleting transmissions</li><li>Password protection for transfers<br></li><li>Short link with NED.link<br></li></ul><p><br></p>', 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            3 => array('id' => 4, 'title' => 'Basic', 'sub_title' => 'Best features and low cost, great combination!', 'icon' => NULL, 'monthly_price' => 0.84, 'yearly_price' => 8.4, 'description' => '<ul><li>Max. file size upto 3 GB</li><li>Total allocated space upto 50 GB</li><li>Unlimited link transmissions</li><li>Decide for yourself when transmissions expire</li><li>Re-sending, forwarding or deleting transmissions</li><li>Password protection for transfers</li><li>Short link with NED.link<br></li></ul><p><br></p>', 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            4 => array('id' => 5, 'title' => 'Premium', 'sub_title' => 'Premium features for premium users!', 'icon' => NULL, 'monthly_price' => 1.68, 'yearly_price' => 16.81, 'description' => '<ul><li>Max. file size upto 5 GB</li><li>Total allocated space upto 100 GB</li><li>Unlimited link transmissions</li><li>Decide for yourself when transmissions expire</li><li>Re-sending, forwarding or deleting transmissions</li><li>Password protection for transfers</li><li>Short link with NED.link<br></li></ul><p><br></p>', 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            5 => array('id' => 6, 'title' => 'Premium Pro', 'sub_title' => 'Get something extra and be a Pro!', 'icon' => NULL, 'monthly_price' => 2.52, 'yearly_price' => 25.2, 'description' => '<ul><li>Max. file size upto 10 GB</li><li>Total allocated space upto 200 GB</li><li>Unlimited link transmissions</li><li>Decide for yourself when transmissions expire</li><li>Re-sending, forwarding or deleting transmissions</li><li>Password protection for transfers</li><li>Short link with NED.link<br></li></ul><p><br></p>', 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            6 => array('id' => 7, 'title' => 'Enterprise', 'sub_title' => 'Get control over your file transfer in minimal price!', 'icon' => NULL, 'monthly_price' => 7.55, 'yearly_price' => 75.55, 'description' => '<ul><li>Max. file size upto 20 GB</li><li>Total allocated space upto 2 TB</li><li>Unlimited link transmissions</li><li>Decide for yourself when transmissions expire</li><li>Re-sending, forwarding or deleting transmissions</li><li>Password protection for transfers</li><li>Short link with NED.link</li></ul><p><br></p>', 'status' => 1, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
            7 => array('id' => 8, 'title' => 'Daily Package', 'sub_title' =>  'Daily Subscription', 'icon' => NULL, 'monthly_price' => 1, 'yearly_price' => 12, 'description' => '<ul><li>Max. file size upto 5 GB</li><li>Total allocated space upto 100 GB</li><li>Unlimited link transmissions</li><li>Decide for yourself when transmissions expire</li><li>Re-sending, forwarding or deleting transmissions</li><li>Password protection for transfers</li><li>Short link with NED.link<br></li></ul><p><br></p>', 'status' => 0, 'deleted_at' => NULL, 'created_at' => '2019-09-26 15:57:00', 'updated_at' => '2020-05-04 00:01:58'),
        ));
    }
}
