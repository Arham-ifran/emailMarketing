<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSendingFeildsToSmsCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->bigInteger('send_to_group')->unsigned()->after('type');
            // $table->boolean('recursive')->default(0)->after('track_clicks');
            // $table->boolean('scheduled')->default(0)->after('recursive');
            $table->dateTime('sending_started_at')->nullable()->after('sender_number');
            $table->dateTime('sending_completed_at')->nullable()->after('sending_started_at');
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
            $table->dropColumn('send_to_group');
            // $table->dropColumn('recursive');
            // $table->dropColumn('scheduled');
            $table->dropColumn('sending_started_at');
            $table->dropColumn('sending_completed_at');
        });
    }
}
