<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlatformToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('platform')->unsigned()->nullable()->default(1)->after('original_password')->comment('1- Web, 2- Mobile, 3- Thunderbird, 4- Outlook, 5- Transfer Immunity , 6- Ned Link, 7- aikQ, 8- Inbox, 9- Overmail, 10- Maili, 11- Product Immunity 12- QR Code 13- TIMmunity 14- Move Immunity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('platform');
        });
    }
}
