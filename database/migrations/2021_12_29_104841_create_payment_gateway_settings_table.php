<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_settings', function (Blueprint $table) {
            $table->id();
            $table->string('paypal_sandbox_api_username')->nullable();
            $table->string('paypal_sandbox_api_password')->nullable();
            $table->string('paypal_sandbox_api_secret')->nullable();
            $table->string('paypal_sandbox_api_base_url')->nullable();
            $table->string('paypal_live_api_username')->nullable();
            $table->string('paypal_live_api_password')->nullable();
            $table->string('paypal_live_api_secret')->nullable();
            $table->string('paypal_live_api_base_url')->nullable();
            $table->string('paypal_mode')->default('sandbox');
            $table->tinyInteger('paypal_status')->nullable()->default(1);
            $table->tinyInteger('mollie_status')->nullable();
            $table->string('mollie_mode', 10)->nullable();
            $table->string('mollie_live_api_key')->nullable();
            $table->string('mollie_sandbox_api_key')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('payment_gateway_settings');
    }
}
