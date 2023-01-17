<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('subscription_id')->unsigned();
            $table->string('item', 500)->nullable();
            $table->string('payment_method')->default(1);
            $table->float('amount', 10, 0)->nullable()->default(0);
            $table->boolean('vat_percentage')->nullable();
            $table->float('vat_amount', 10, 0)->nullable();
            $table->string('vat_country_code', 2)->nullable();
            $table->boolean('discount_percentage')->default(0)->nullable();
            $table->float('discount_amount')->nullable()->default(0);
            $table->string('voucher', 50)->nullable();
            $table->float('total_amount', 10, 0)->nullable();
            $table->text('payment_mode')->nullable();
            $table->text('reseller')->nullable();
            $table->text('payload', 65535)->nullable();
            $table->text('invoice', 65535)->nullable();
            $table->string('token', 100)->nullable();
            $table->string('txn_id', 100)->nullable();
            $table->string('payer_id', 100)->nullable();
            $table->text('data', 65535)->nullable();
            $table->boolean('payments')->default(1)->comment('1- Live Mode 2- Sandbox/Test Mode');
            $table->string('lang', 10)->nullable()->default('en');
            $table->string('profile_id', 20)->nullable();
            $table->text('profile_data', 65535)->nullable();
            $table->integer('status')->nullable()->default(1)->unsigned()->comment("1=paid,2=open,3=pending,4=failed,5=expired,6=cancel,7=refund,8=chargeback");
            $table->integer('timestamp')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
