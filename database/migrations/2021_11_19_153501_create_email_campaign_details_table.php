<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailCampaignDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_campaign_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id')->nullable();
            $table->uuid('email_campaign_id');
            $table->string('detail');
            $table->longText('value')->nullable();
            $table->longText('misc')->nullable();
            $table->boolean('active')->default(1);
            $table->index(['client_id', 'email_campaign_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_campaign_details');
    }
}
