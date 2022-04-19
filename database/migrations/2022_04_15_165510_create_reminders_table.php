<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('entity_type');
            $table->string('entity_id');
            $table->integer('user_id');
            $table->string('name');
            $table->string('description')->nullable();
            //minutes before an event this should fire.
            //this is so an event time can change, but we don't have to go update all the reminders
            $table->unsignedInteger('remind_time');
            $table->timestamp('triggered_at')->nullable();
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
        Schema::dropIfExists('reminders');
    }
}
