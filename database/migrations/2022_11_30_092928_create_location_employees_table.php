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
        Schema::create('location_employees', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('client_id');
            $table->foreignUuid('location_id')->nullable();
            $table->foreignUuid('department_id');
            $table->foreignUuid('position_id');
            $table->foreignUuid('user_id');
            $table->foreignUuid('primary_supervisor_user_id')->nullable();
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
        Schema::dropIfExists('location_employees');
    }
};
