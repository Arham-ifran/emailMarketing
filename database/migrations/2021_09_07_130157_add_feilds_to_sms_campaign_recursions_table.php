<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeildsToSmsCampaignRecursionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_campaign_recursions', function (Blueprint $table) {
            $table->smallInteger('month_number')->nullable();
            $table->smallInteger('month_date')->nullable();
            $table->smallInteger('week_day')->nullable();
            $table->smallInteger('times_sent')->default(0);
            $table->smallInteger('times_to_send')->default(1);
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
            $table->dropColumn('month_number');
            $table->dropColumn('month_date');
            $table->dropColumn('week_day');
            $table->dropColumn('times_sent');
            $table->dropColumn('times_to_send');
        });
    }
}
