<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id()->unsigned();
			$table->bigInteger('role_id')->unsigned()->index('role_id');
			$table->string('name', 65);
			$table->string('email', 65)->unique();
			$table->string('password', 191);
			$table->string('original_password', 191);
			$table->string('profile_image', 255)->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->string('password_reset_token', 100)->nullable();
			$table->timestamp('password_reset_token_date')->nullable();
			$table->boolean('status')->nullable()->default(0);
			$table->softDeletes();
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
        Schema::dropIfExists('admins');
    }
}