<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFiedsInEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->tinyInteger('campaign_type')->comment('1=immediate,2=Schedule Once,3=Recursive')->after("template_id");
            $table->timestamp('schedule_date')->comment('Schedule Once if set')->nullable()->after("campaign_type");
            $table->tinyInteger('recursive_campaign_type')->comment('1=weekly,2=Monthly,3=Yearly')->nullable()->after("schedule_date");
            $table->tinyInteger('day_of_week')->comment('weekly recursive option')->nullable()->after("recursive_campaign_type");
            $table->tinyInteger('day_of_month')->comment('monthly recursive option')->nullable()->after("day_of_week");
            $table->tinyInteger('month_of_year')->comment('yearly recursive option')->nullable()->after("day_of_month");
            $table->tinyInteger('day_of_week_year')->comment('yearly recursive option')->nullable()->after("month_of_year");
            $table->tinyInteger('no_of_time')->comment('weekly / monthly / yearly')->nullable()->after("day_of_week_year");
            $table->unsignedBigInteger('group_id')->nullable()->after("no_of_time");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn('campaign_type');
            $table->dropColumn('schedule_date');
            $table->dropColumn('recursive_campaign_type');
            $table->dropColumn('day_of_week');
            $table->dropColumn('day_of_month');
            $table->dropColumn('month_of_year');
            $table->dropColumn('day_of_week_year');
            $table->dropColumn('no_of_time');
            $table->dropColumn('group_id');
        });
    }
}