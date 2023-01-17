<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->text('info')->nullable();
            $table->integer('module')->nullable()->default(1)->unsigned()->comment("1=contacts,2=emails,3=sms");
            $table->integer('start_range')->default(0);
            $table->integer('end_range')->nullable()->default(0);
            $table->float('price_without_vat')->default(0);
            $table->float('price_with_vat')->default(0);
            $table->tinyInteger('is_voucher_setting')->default(0);
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('package_settings');
    }
}
