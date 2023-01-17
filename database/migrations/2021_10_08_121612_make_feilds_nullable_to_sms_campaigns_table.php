<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeFeildsNullableToSmsCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('message')->nullable()->change();
            $table->string('sender_name', 65)->nullable()->change();
            $table->string('sender_number', 65)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->change();
            $table->string('message', 255)->nullable(false)->change();
            $table->string('sender_name', 65)->nullable(false)->change();
            $table->string('sender_number', 65)->nullable(false)->change();
        });
    }
}
