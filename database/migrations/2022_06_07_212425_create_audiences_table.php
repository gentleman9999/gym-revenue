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
        Schema::create('audiences', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('client_id');
            $table->string('name');
            $table->string('entity');
            $table->json('filters')->nullable();
            $table->boolean('editable')->default(true);
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
        Schema::dropIfExists('audiences');
    }
};
