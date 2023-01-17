<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name', 35)->nullable();
            $table->string('last_name', 35)->nullable();
            $table->boolean('for_sms')->default('0');
            $table->boolean('for_email')->default('0');
            $table->string('country_code', 5)->nullable()->comment('for sms');
            $table->string('number', 15)->nullable()->comment('for sms');
            $table->string('email', 65)->nullable()->comment('for email');
            $table->boolean('subscribed')->default(1);
            $table->dateTime('unsubscribed_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->boolean('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
