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
        Schema::create('obfuscated_users', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id')->nullable()->index();
            $table->uuid('user_id')->nullable()->index();
            $table->string('email');
            $table->mediumText('data')->nullable();
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
        Schema::dropIfExists('obfuscated_users');
    }
};
