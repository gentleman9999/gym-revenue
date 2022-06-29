<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('alternate_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('access_token')->nullable();
            $table->string('password')->nullable();
            $table->string('job_title')->nullable();
            $table->uuid('home_location_id')->nullable()->index();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('termination_date')->nullable();
            $table->uuid('classification_id')->nullable()->index();
            $table->string('manager')->nullable();
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable()->index();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->unique(['client_id', 'email']);
            $table->index(['client_id', 'home_location_id']);
            $table->index(['client_id', 'classification_id']);
            $table->index(['client_id', 'current_team_id']);
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
        Schema::dropIfExists('users');
    }
}
