<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToEmailCampaignTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_campaign_templates', function (Blueprint $table) {
            $table->boolean('type')->default(1)->comment('1=design, 2=html')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_campaign_templates', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
