<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->bigInteger('item_id')->unsigned()->nullable();
            $table->tinyInteger('module')->unsigned()->nullable();
            $table->tinyInteger('notification_type')->unsigned()->nullable();
            $table->string('notification_text', 255)->nullable();
            $table->boolean('is_read')->unsigned()->default(0);
            $table->string('redirect_to', 255)->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
