<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackagesDataToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone', 100)->nullable()->after('original_password');
            $table->string('language', 10)->nullable()->after('timezone');
            $table->boolean('is_expired')->nullable()->default(0)->after('language');
            $table->boolean('expired_package_disclaimer')->nullable()->default(0)->after('is_expired');
            $table->string('payment_method')->nullable()->default(0)->after('expired_package_disclaimer');
            $table->integer('package_id')->nullable()->after('payment_method')->unsigned();
            $table->timestamp('last_quota_revised')->nullable()->after('package_id')->comment('Revised quota date of user every month when user subscribe anuual package');
            $table->integer('on_hold_package_id')->nullable()->after('last_quota_revised')->unsigned();
            $table->bigInteger('prev_package_subscription_id')->nullable()->after('on_hold_package_id')->unsigned();
            $table->bigInteger('package_subscription_id')->nullable()->after('prev_package_subscription_id')->unsigned();
            $table->boolean('package_recurring_flag')->nullable()->default(1)->after('package_subscription_id');
            $table->bigInteger('payment_id')->nullable()->after('package_recurring_flag')->unsigned();
            $table->string('mollie_customer_id', 100)->nullable()->after('payment_id');
            $table->text('login_location')->nullable()->after('mollie_customer_id');
            $table->string('ip_address', 20)->nullable()->after('login_location');
            $table->boolean('on_trial')->nullable()->default(1)->after('ip_address');
            $table->boolean('switch_to_paid_package')->nullable()->default(0)->after('on_trial');
            $table->boolean('package_updated_by_admin')->nullable()->default(0)->after('switch_to_paid_package');
            $table->boolean('unpaid_package_email_by_admin')->nullable()->default(0)->after('package_updated_by_admin');
            // $table->timestamp('disabled_at')->nullable()->after('last_active_at');
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
            $table->dropColumn('timezone');
            $table->dropColumn('language');
            $table->dropColumn('is_expired');
            $table->dropColumn('expired_package_disclaimer');
            $table->dropColumn('payment_method');
            $table->dropColumn('package_id');
            $table->dropColumn('last_quota_revised');
            $table->dropColumn('on_hold_package_id');
            $table->dropColumn('prev_package_subscription_id');
            $table->dropColumn('package_subscription_id');
            $table->dropColumn('package_recurring_flag');
            $table->dropColumn('payment_id');
            $table->dropColumn('mollie_customer_id');
            $table->dropColumn('login_location');
            $table->dropColumn('ip_address');
            $table->dropColumn('on_trial');
            $table->dropColumn('switch_to_paid_package');
            $table->dropColumn('package_updated_by_admin');
            $table->dropColumn('unpaid_package_email_by_admin');
            // $table->dropColumn('disabled_at');
        });
    }
}
