<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayAsYouGoPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_as_you_go_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->bigInteger('package_subscription_id')->unsigned();
            $table->text('link')->nullable();
            $table->integer('charging_for_emails')->default(0);
            $table->integer('charging_for_sms')->default(0);
            $table->integer('charging_for_contacts')->default(0);
            $table->float('price_for_emails_charged')->default(0);
            $table->float('price_for_sms_charged')->default(0);
            $table->float('price_for_contacts_charged')->default(0);
            $table->float('amount')->default(0);
            $table->float('vat_percentage')->default(0);
            $table->float('vat_amount')->default(0);
            $table->integer('vat_country_code')->default(0);
            $table->float('total_amount_charged')->default(0);

            $table->boolean('payment_mode')->default(1)->comment('1- Live Mode 2- Sandbox/Test Mode');
            $table->text('payload', 65535)->nullable();
            $table->text('invoice', 65535)->nullable();
            $table->string('token', 100)->nullable();
            $table->string('txn_id', 100)->nullable();
            $table->string('payer_id', 100)->nullable();
            $table->text('data', 65535)->nullable();
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
        Schema::dropIfExists('pay_as_you_go_payments');
    }
}
