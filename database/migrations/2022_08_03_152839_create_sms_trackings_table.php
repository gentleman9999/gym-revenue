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
        Schema::create('sms_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('SmsSid');
            $table->string('SmsStatus');
            $table->string('MessageStatus');
            $table->string('To');
            $table->string('MessageSid');
            $table->string('AccountSid');
            $table->string('From');
            $table->string('ApiVersion');
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
        Schema::dropIfExists('sms_trackings');
    }
};
