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
        Schema::create('mailgun_callbacks', function (Blueprint $table) {
            $table->id();
            $table->string('MailgunId');
            $table->string('event');
            $table->timestamp('timestamp');
            $table->string('MessageId');
            $table->string('recipient')->nullable();
            $table->string('recipient-domain')->nullable();
            $table->string('IpAddress')->nullable();
            $table->string('sender')->nullable();
            $table->string('SenderIpAddress')->nullable();
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
        Schema::dropIfExists('mailgun_callbacks');
    }
};
