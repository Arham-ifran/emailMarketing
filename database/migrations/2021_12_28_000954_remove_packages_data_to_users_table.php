<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePackagesDataToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('package_subscription_id');
            $table->dropColumn('package_id');
            $table->dropColumn('payment_id');
            $table->dropColumn('on_hold_package_id');
            $table->dropColumn('prev_package_subscription_id');
            $table->dropColumn('voucher_id');
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
            $table->unsignedBigInteger('package_id')->nullable()->after('status');
            $table->unsignedBigInteger('payment_id')->nullable()->after('status');
            $table->unsignedBigInteger('on_hold_package_id')->nullable()->after('status');
            $table->unsignedBigInteger('prev_package_subscription_id')->nullable()->after('status');
            $table->unsignedBigInteger('voucher_id')->nullable()->after('status');
            $table->unsignedBigInteger('package_subscription_id')->default('0')->after('email');
        });
    }
}
