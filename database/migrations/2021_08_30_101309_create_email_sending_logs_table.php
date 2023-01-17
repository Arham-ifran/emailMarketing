<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailSendingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_sending_logs', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('campaign_id')->unsigned();
            $table->bigInteger('contact_id')->unsigned();
            $table->dateTime('sent_at');
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('bounced_at')->nullable();
            $table->dateTime('unsubscribed_at')->nullable();
            $table->dateTime('failed_at')->nullable();
            $table->text('failed_reason')->nullable();
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
        Schema::dropIfExists('email_sending_logs');
    }
}
