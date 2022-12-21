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
            $table->uuid('client_id')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('alternate_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('obfuscated_at')->nullable();
            $table->string('phone')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->char('state', 2)->nullable();
            $table->char('zip', 5)->nullable();
            $table->string('access_token')->nullable();
            $table->string('password')->nullable();
            $table->uuid('home_location_id')->nullable()->index();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamp('termination_date')->nullable();
            $table->string('manager')->nullable();
            $table->boolean('is_cape_and_bay_user')->default(false);
            $table->rememberToken();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->unique(['client_id', 'email']);
            $table->index(['client_id', 'home_location_id']);
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
