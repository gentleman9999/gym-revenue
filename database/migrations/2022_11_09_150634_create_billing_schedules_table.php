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
        Schema::create('billing_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('client_id')->index();
            $table->string('type');
            $table->boolean('is_open_ended')->nullable();
            $table->boolean('is_renewable')->nullable();
            $table->boolean('should_renew_automatically')->nullable();
            $table->string('term_length')->nullable();
            $table->integer('min_terms')->nullable();
            $table->unsignedDecimal('amount');
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
        Schema::dropIfExists('billing_schedules');
    }
};
