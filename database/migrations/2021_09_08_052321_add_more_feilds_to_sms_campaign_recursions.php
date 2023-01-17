<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFeildsToSmsCampaignRecursions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_campaign_recursions', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1=weekly, 2=monthly, 3=yearly')->default(1)->after('sms_campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_campaign_recursions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
