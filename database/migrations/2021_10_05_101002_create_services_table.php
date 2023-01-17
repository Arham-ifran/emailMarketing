<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE TABLE `services` (
            `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(250) COLLATE utf8mb4_unicode_ci NOT NULL,
            `slug` VARCHAR(500) COLLATE utf8mb4_unicode_ci NOT NULL,
            `description` TEXT COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `image` VARCHAR(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `status` TINYINT(1) DEFAULT NULL,
            `deleted_at` TIMESTAMP NULL DEFAULT NULL,
            `created_at` TIMESTAMP NULL DEFAULT NULL,
            `updated_at` TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
