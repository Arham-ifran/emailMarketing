<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguageModulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('language_modules')->delete();

        \DB::table('language_modules')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Package Features',
                'table' => 'package_features',
                'columns' => 'name,info',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:57',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Packages',
                'table' => 'packages',
                'columns' => 'title,sub_title',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'FAQs',
                'table' => 'faqs',
                'columns' => 'question,answer',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Email Templates',
                'table' => 'email_templates',
                'columns' => 'subject',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'CMS Pages',
                'table' => 'cms_pages',
                'columns' => 'title',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Features',
                'table' => 'features',
                'columns' => 'name,description',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Services',
                'table' => 'services',
                'columns' => 'name,description',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Home Contents',
                'table' => 'home_contents',
                'columns' => 'name',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'CMS Page Lables',
                'table' => 'cms_page_labels',
                'columns' => 'value',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Email Template Labels',
                'table' => 'email_template_labels',
                'columns' => 'value',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Home Content Labels',
                'table' => 'home_content_labels',
                'columns' => 'value',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'About Us Content Labels',
                'table' => 'about_us_content_labels',
                'columns' => 'value',
                'status' => 1,
                'deleted_at' => NULL,
                'created_at' => '2020-10-07 15:40:55',
                'updated_at' => '2020-10-07 15:40:55',
            ),
        ));
    }
}
