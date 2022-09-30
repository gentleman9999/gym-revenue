<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drip_campaign_days', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('drip_campaign_id');
            $table->unsignedInteger('day_of_campaign');
            $table->string('email_template_id')->nullable();
            $table->string('sms_template_id')->nullable();
            $table->string('call_template_id')->nullable();
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
        Schema::dropIfExists('drip_campaign_days');
    }
};
