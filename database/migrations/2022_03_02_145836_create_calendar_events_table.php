<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('client_id');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->boolean('full_day_event');
            $table->dateTime('start');
            $table->dateTime('end')->nullable();
            $table->string('color')->nullable();
            $table->json('options')->nullable();
            $table->foreignUuid('event_type_id');
            $table->integer('owner_id')->nullable()->index();
            $table->dateTime('event_completion')->nullable();
            $table->uuid('location_id')->nullable();
            $table->index(['client_id', 'event_type_id']);
            $table->boolean('editable')->default(true);
            $table->boolean('call_task')->default(false);
            $table->softDeletes();
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
        Schema::dropIfExists('calendar_events');
    }
}
