<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('package_id');
            $table->double('price')->nullable();
            $table->text('features')->nullable();
            $table->text('description')->nullable();
            $table->boolean('type')->nullable();
            $table->bigInteger('start_date')->nullable();
            $table->bigInteger('end_date')->nullable();
            $table->integer('repetition')->nullable()->default(1);
            $table->tinyInteger('payment_option')->nullable()->default(2)->comment('1 = Free, 2 = Paid');
            $table->boolean('is_active')->nullable()->default(1);
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
        Schema::dropIfExists('package_subscriptions');
    }
}
