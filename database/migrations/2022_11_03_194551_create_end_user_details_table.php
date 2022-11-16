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
        Schema::create('end_user_details', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('client_id')->index();
            $table->uuid('end_user_id')->index();
            $table->string('field');
            $table->string('value')->nullable();
            $table->mediumText('misc')->nullable();
            $table->string('entity_id')->nullable();
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('end_user_details');
    }
};
