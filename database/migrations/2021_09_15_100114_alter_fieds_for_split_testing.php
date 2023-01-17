<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFiedsForSplitTesting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->renameColumn('split_subject_line', 'split_subject_line_1');
            $table->renameColumn('split_email_content', 'split_email_content_1');
        });

        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('split_subject_line_1', 255)->change();
            $table->string('split_subject_line_2', 255)->nullable()->after('split_subject_line_1');
            $table->unsignedBigInteger('split_email_content_1')->nullable()->change();
            $table->unsignedBigInteger('split_email_content_2')->nullable()->after('split_email_content_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->renameColumn('split_subject_line_1', 'split_subject_line');
            $table->renameColumn('split_email_content_1', 'split_email_content');
        });
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->text('split_subject_line')->change();
            $table->string('split_email_content')->change();
            // $table->dropColumn('split_subject_line_2');
            // $table->dropColumn('split_email_content_2');
        });
    }
}
