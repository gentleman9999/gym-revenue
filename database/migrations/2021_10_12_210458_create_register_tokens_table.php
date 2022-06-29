<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('register_tokens', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('name');
            $table->string('client_id')->nullable()->index(); //NULL == 'Cape & Bay'
            $table->string('role')->nullable()->index(); // NULL == 'Account Owner' or 'Admin'
            $table->string('team_id')->nullable()->index(); // Null == default Account Owner or Admin Team

            $table->integer('uses')->default(-1); // -1 Means Unlimited
            $table->boolean('active')->default(1); // When no active, nothing else matters.
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
        Schema::dropIfExists('register_tokens');
    }
}
