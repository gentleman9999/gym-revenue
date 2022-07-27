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
        Schema::create('folders', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('client_id');
            $table->string('name');
            $table->boolean('hidden')->default(false);
            $table->foreignUuid('shared_with_client_id')->nullable();
            $table->foreignUuid('shared_with_team_id')->nullable();
            $table->foreignUuid('shared_with_location_id')->nullable();
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
        Schema::dropIfExists('folders');
    }
};
