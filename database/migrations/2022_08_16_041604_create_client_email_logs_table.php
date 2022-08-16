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
        Schema::create('client_email_logs', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('client_id');
            $table->foreignUuid('gateway_id');
            $table->string('message_id')->nullable();
            $table->string('campaign_id')->nullable();
            $table->string('email_template_id')->nullable();
            $table->string('recipient_type')->nullable();
            $table->string('recipient_id')->nullable();
            $table->string('recipient_email')->nullable();
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
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
        Schema::dropIfExists('client_email_logs');
    }
};
