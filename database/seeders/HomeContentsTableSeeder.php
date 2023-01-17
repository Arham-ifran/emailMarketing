<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HomeContentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('home_contents')->delete();

        \DB::table('home_contents')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Complete Cloud Solution',
                'description' => '<p>Your strategic partner to meet all your data transfer needs from one to another cloud storage, specifically GDrive, One Drive, Dropbox, and Hi Drive.</p>
<ul class="list-unstyled">
<li><span class="dot"></span>Migrate any data at lightning speed</li>
<li><span class="dot"></span>Easy-to-use, cost-effective solution</li>
<li><span class="dot"></span>Avoid migration errors or complex issues</li>
</ul>',
                'image' => 'home-content-creativityimg.png',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-23 10:27:43',
                'updated_at' => '2020-07-23 10:27:43',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Managed Migration Services',
                'description' => '<p>Your dedicated support to perform error-free migration of any large-scale information, ensuring a smooth and successful move. It serves any data transfer scenario.</p>
<ul class="list-unstyled">
<li><span class="dot"></span>Move cloud or email-based data</li>
<li><span class="dot"></span>Single interface to manage all migrations</li>
<li><span class="dot"></span>Get your mailbox virus-free before migration</li>
</ul>',
                'image' => 'home-content-creativityimg.png',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-07-23 10:28:58',
                'updated_at' => '2020-07-23 10:28:58',
            ),
        ));
    }
}
