<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampaignTemplatesLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_email_campaign_template_labels', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('email_campaign_template_id')->unsigned();
            $table->string('label', 255);
            $table->text('value', 255);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('public_email_campaign_template_labels');
    }
}
