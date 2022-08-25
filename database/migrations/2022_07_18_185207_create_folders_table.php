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
            $table->json('team_ids')->nullable();
            $table->json('location_ids')->nullable();
            $table->json('department_ids')->nullable();
            $table->json('position_ids')->nullable();
            $table->json('user_ids')->nullable();
            $table->json('role_ids')->nullable();
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
