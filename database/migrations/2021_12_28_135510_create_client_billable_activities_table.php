<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientBillableActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_billable_activities', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->index();
            $table->string('desc')->nullable();
            $table->string('entity_type');
            $table->string('entity_id');
            $table->integer('units');
            $table->longText('misc');
            $table->string('triggered_by_user_id');
            $table->index(['client_id', 'entity_id']);

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
        Schema::dropIfExists('client_billable_activities');
    }
}
