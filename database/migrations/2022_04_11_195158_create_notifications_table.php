<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->integer('user_id')->index();
            $table->string('state')->default('info');
            $table->string('text')->nullable();
            $table->string('entity_type')->nullable();
            $table->string('entity_id')->nullable()->index();
            $table->json('entity')->nullable();
            $table->json('misc')->nullable();
            $table->timestamp('dismissed_at')->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
