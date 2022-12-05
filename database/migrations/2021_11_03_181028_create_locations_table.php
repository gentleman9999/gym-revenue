<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('client_id');
            $table->string('name');
            $table->string('gymrevenue_id')->nullable();
            $table->string('location_no')->nullable();
            $table->string('city')->nullable();
            $table->char('state', 2)->nullable();
            $table->string('zip', 5)->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->double('latitude', 8, 6)->nullable();
            $table->double('longitude', 9, 6)->nullable();
            $table->boolean('active')->default(1);
            $table->string('phone')->nullable();
            $table->uuid('default_team_id')->nullable()->index();
            $table->string('location_type');
            $table->dateTime('open_date')->nullable();
            $table->dateTime('close_date')->nullable();
            $table->index(['client_id', 'gymrevenue_id']);
            $table->index(['client_id', 'location_no']);
            $table->unique(['client_id', 'name']);
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
        Schema::dropIfExists('locations');
    }
}
