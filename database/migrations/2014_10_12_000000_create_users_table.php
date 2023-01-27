<?php

use App\Enums\UserGenderEnum;
use App\Enums\UserTypesEnum;
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
            /** Get all shared fields */
            $this->getSharedFields($table);
            $table->boolean('is_cape_and_bay_user')->default(false);
            /** DB constraints */
            $table->unique(['client_id', 'email']);
            $table->index(['client_id', 'home_location_id']);
        });

        Schema::create('leads', function (Blueprint $table) {
            /** Get all shared fields */
            $table->foreignUuid('user_id');
            $this->getSharedFields($table);
        });

        Schema::create('customers', function (Blueprint $table) {
            /** Get all shared fields */
            $table->foreignUuid('user_id');
            $this->getSharedFields($table);
            $table->uuid('agreement_id')->nullable();
        });

        Schema::create('members', function (Blueprint $table) {
            /** Get all shared fields */
            $table->foreignUuid('user_id');
            $this->getSharedFields($table);
            $table->uuid('agreement_id')->nullable();
        });

        Schema::create('employees', function (Blueprint $table) {
            /** Get all shared fields */
            $table->foreignUuid('user_id');
            $this->getSharedFields($table);
        });
    }

    /** All common fields described in a single location */
    public function getSharedFields($table)
    {
        $table->uuid('id')->primary()->unique();
        $table->uuid('client_id')->nullable()->index();

        /** Columns for user info */
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->timestamp('email_verified_at')->nullable();

        /** Email(s) */
        $table->string('email');
        $table->json('alternate_emails')->nullable();

        /** Phone Numbers(s) */
        $table->string('phone')->nullable();
        $table->string('alternate_phone')->nullable();

        /** Personal/Sensitive */
        $table->timestamp('date_of_birth')->nullable();
        $table->enum('gender', array_column(UserGenderEnum::cases(), 'value'))->nullable();
        $table->string('drivers_license_number')->nullable();
        $table->string('profile_photo_path', 2048)->nullable();

        /** Columns for user occupation */
        $table->string('occupation')->nullable();
        $table->string('employer')->nullable();
        $table->string('barcode')->nullable();

        /** Columns for address */
        $table->string('address1')->nullable();
        $table->string('address2')->nullable();
        $table->string('city')->nullable();
        $table->char('state', 2)->nullable();
        $table->string('zip', 5)->nullable();

        /** Columns for campaigns */
        $table->boolean('unsubscribed_email')->default(false);
        $table->boolean('unsubscribed_sms')->default(false);

        /** Columns for auth/access */
        $table->string('password')->nullable();
        $table->text('two_factor_secret')->nullable();
        $table->text('two_factor_recovery_codes')->nullable();
        $table->string('access_token')->nullable();
        $table->rememberToken();

        /** Columns for system specific user info */
        $table->enum('user_type', array_column(UserTypesEnum::cases(), 'value'))->default('lead');
        $table->json('entry_source')->nullable();
        $table->uuid('home_location_id')->nullable()->index();
        $table->string('manager')->nullable();
        $table->unsignedTinyInteger('opportunity')->nullable();
        $table->string('external_id')->nullable();
        $table->jsonb('misc')->nullable();
        $table->jsonb('details')->nullable();
        $table->boolean('is_previous')->default(false);

        /** Columns for timestamps */
        $table->timestamps();
        $table->timestamp('started_at')->nullable();
        $table->timestamp('ended_at')->nullable();
        $table->softDeletes('terminated_at');
        $table->timestamp('obfuscated_at')->nullable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('members');
        Schema::dropIfExists('employees');
    }
}
