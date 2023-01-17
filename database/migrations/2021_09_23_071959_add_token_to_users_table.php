<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('api_status')->comment('1=Active,2=disabled')->default(2)->after('original_password');
            $table->string('endpoint_url')->nullable()->after('original_password');
            $table->string('secret_key')->nullable()->after('original_password');
            $table->longText('api_token')->nullable()->after('original_password');
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
            $table->dropColumn('api_status');
            $table->dropColumn('endpoint_url');
            $table->dropColumn('secret_key');
            $table->dropColumn('api_token');
        });
    }
}
