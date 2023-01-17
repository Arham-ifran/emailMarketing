<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTablenameToSmsCampaignExcludesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('sms_campaign_excludes', 'campaign_excludes');
        Schema::table('campaign_excludes', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1=sms, 2=email')->default(1)->after('user_id');
            $table->dropForeign('sms_campaign_excludes_sms_campaign_id_foreign');
            $table->dropColumn('sms_campaign_id');
            $table->unsignedBigInteger('campaign_id')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('campaign_excludes', 'sms_campaign_excludes');
        Schema::table('sms_campaign_excludes', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->foreignId('sms_campaign_id')->constrained()->onDelete('cascade')->after('user_id');
            $table->dropColumn('campaign_id');
        });
    }
}
