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
        Schema::create('gym_amenities', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('location_id')->index();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->unsignedTinyInteger('capacity')->nullable();
            $table->unsignedTinyInteger('working_hour')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
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
        Schema::dropIfExists('amenities');
    }
};
