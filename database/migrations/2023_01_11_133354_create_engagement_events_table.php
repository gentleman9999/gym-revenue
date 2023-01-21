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
        Schema::create('engagement_events', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('entity');
            $table->string('operation');
            $table->uuid('stored-event-id');
            $table->uuid('aggregate_uuid')->nullable();
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
        Schema::dropIfExists('engagement_events');
    }
};
