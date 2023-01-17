<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaygpaymentsToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->boolean('from_other_table')->default(0)->comment('0=no, 1=from PayAsYouGo')->after('status');
            $table->bigInteger('pay_as_you_go_payment_id')->unsigned()->nullable()->after('from_other_table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('from_other_table');
            $table->dropColumn('pay_as_you_go_payment_id');
        });
    }
}
