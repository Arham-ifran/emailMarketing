<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitsToPackageSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_subscriptions', function (Blueprint $table) {
            $table->text('email_limit')->nullable()->default(0)->after('end_date');
            $table->text('email_used')->nullable()->default(0)->after('email_limit');
            $table->text('sms_limit')->nullable()->default(0)->after('email_used');
            $table->text('sms_used')->nullable()->default(0)->after('sms_limit');
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
            $table->dropColumn('email_used');
            $table->dropColumn('sms_used');
            $table->dropColumn('email_limit');
            $table->dropColumn('sms_limit');
        });
    }
}
