<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsSendingLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_sending_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_campaign_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('sms_sending_logs');
    }
}
