<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDraftingAndContactsToSmsCampaigns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_campaigns', function (Blueprint $table) {
            $table->tinyInteger('sending_to')->comment('1=group, 2=contacts, 3=both')->default(1)->after('message');
            $table->boolean('status')->comment('1=draft, 2=sending, 3=sent, 4=deleted')->default(1)->after('message')->change();
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
            $table->dropColumn('sending_to');
            $table->boolean('status')->change();
        });
    }
}
