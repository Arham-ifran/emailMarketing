<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactUsQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_us_queries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 250)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('number', 50)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('message')->nullable();
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('contact_us_queries');
    }
}
