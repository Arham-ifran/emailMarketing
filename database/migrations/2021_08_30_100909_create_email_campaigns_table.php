<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('name', 255);
            $table->string('subject', 255);
            $table->string('sender_name', 65);
            $table->string('sender_email', 65);
            $table->string('reply_to_email', 65);
            $table->boolean('track_opens')->nullable()->default(0);
            $table->boolean('track_clicks')->nullable()->default(0);
            $table->bigInteger('template_id');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_campaigns');
    }
}
