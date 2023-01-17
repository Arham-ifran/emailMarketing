<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToPaymentFeildsToPackageSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_subscriptions', function (Blueprint $table) {
            $table->float('emails_to_pay')->default(0)->after('sms_used');
            $table->float('sms_to_pay')->default(0)->after('emails_to_pay');
            $table->float('contacts_to_pay')->default(0)->after('sms_to_pay');
            $table->integer('emails_paying_for')->default(0)->after('contacts_to_pay');
            $table->integer('sms_paying_for')->default(0)->after('emails_paying_for');
            $table->integer('contacts_paying_for')->default(0)->after('sms_paying_for');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_subscriptions', function (Blueprint $table) {
            $table->dropColumn('emails_to_pay');
            $table->dropColumn('sms_to_pay');
            $table->dropColumn('contacts_to_pay');
            $table->dropColumn('emails_paying_for');
            $table->dropColumn('sms_paying_for');
            $table->dropColumn('contacts_paying_for');
        });
    }
}
