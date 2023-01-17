<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHistoryIdToEmailCampaignOpensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_campaign_opens', function (Blueprint $table) {
            $table->unsignedBigInteger('history_id')->after('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_campaign_opens', function (Blueprint $table) {
            $table->dropColumn('history_id');
        });
    }
}
