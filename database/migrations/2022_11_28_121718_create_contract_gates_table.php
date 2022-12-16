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
        Schema::create('contract_gates', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->foreignUuid('client_id')->nullable()->index();
            $table->foreignUuid('contract_id');
            $table->uuid('entity_id');
            $table->string('entity_type');
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
        Schema::dropIfExists('contract_gates');
    }
};
