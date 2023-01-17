<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSplitTestingFieldsInEmailCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->boolean('is_split_testing')->nullable()->default(0)->comment("1=split testing,0=no split testing")->after('group_id');
            $table->text('split_subject_line')->nullable()->comment("split testing field")->after('is_split_testing');
            $table->string('split_email_content',30)->nullable()->comment("split testing field - template id")->after('split_subject_line');
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
            $table->dropColumn('is_split_testing');
            $table->dropColumn('split_subject_line');
            $table->dropColumn('split_email_content');
        });
    }
}
