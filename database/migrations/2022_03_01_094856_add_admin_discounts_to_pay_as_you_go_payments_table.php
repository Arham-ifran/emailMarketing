<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminDiscountsToPayAsYouGoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_as_you_go_payments', function (Blueprint $table) {
            $table->boolean('discount_percentage')->default(0)->nullable()->after('vat_country_code');
            $table->float('discount_amount')->nullable()->default(0)->after('discount_percentage');
            $table->string('payment_method')->default('mollie')->after('total_amount_charged');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pay_as_you_go_payments', function (Blueprint $table) {
            $table->dropColumn('discount_percentage');
            $table->dropColumn('discount_amount');
            $table->dropColumn('payment_method');
        });
    }
}
