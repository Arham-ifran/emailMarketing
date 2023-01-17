<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeildsToCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->integer('vat')->nullable()->after('name');
            $table->tinyInteger('apply_default_vat')->default(0)->after('vat');
            $table->tinyInteger('status')->default(0)->after('apply_default_vat');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('vat');
            $table->dropColumn('apply_default_vat');
            $table->dropColumn('status');
        });
    }
}
