<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeFeildsNullableToEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('name', 255)->nullable()->change();
            $table->string('sender_name', 65)->nullable()->change();
            $table->string('sender_email', 65)->nullable()->change();
            $table->string('reply_to_email', 65)->nullable()->change();
            $table->bigInteger('template_id')->nullable()->change();
            // $table->tinyInteger('campaign_type')->nullable()->change();
        });
        DB::statement('ALTER TABLE `email_campaigns` MODIFY `campaign_type` TINYINT(4) COMMENT "1=immediate,2=Schedule Once,3=Recursive";');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('name', 255)->nullable(false)->change();
            $table->string('sender_name', 65)->nullable(false)->change();
            $table->string('sender_email', 65)->nullable(false)->change();
            $table->string('reply_to_email', 65)->nullable(false)->change();
            $table->bigInteger('template_id')->nullable(false)->change();
            // $table->tinyInteger('campaign_type')->nullable(false)->change();
        });
        DB::statement('ALTER TABLE `email_campaigns` MODIFY `campaign_type` TINYINT COMMENT "1=immediate,2=Schedule Once,3=Recursive" NOT NULL ;');
    }
}
