<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('original_password',191)->after('password');
            $table->boolean('status')->after('remember_token')->default(0);
            $table->dateTime('last_active_at')->after('status')->nullable()->comment("Check when user was last active");
            $table->dateTime('disabled_at')->after('last_active_at')->nullable()->comment("Check when user was disabled");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('original_password');
            $table->dropColumn('status');
            $table->dropColumn('last_active_at');
            $table->dropColumn('disabled_at');
        });
    }
}