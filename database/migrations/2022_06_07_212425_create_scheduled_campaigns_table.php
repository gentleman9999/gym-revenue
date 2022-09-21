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
        Schema::create('scheduled_campaigns', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('client_id');
            $table->uuid('audience_id');
            $table->string('name');
            $table->string('email_template_id')->nullable();
            $table->string('sms_template_id')->nullable();
            $table->string('client_call_script')->nullable();
            $table->timestamp('send_at');
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('scheduled_campaigns');
    }
};
