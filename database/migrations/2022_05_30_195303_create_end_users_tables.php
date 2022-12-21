<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEndUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $this->getSharedFields($table);
            $table->timestamp('converted_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('members', function (Blueprint $table) {
            $this->getSharedFields($table);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('customers', function (Blueprint $table) {
            $this->getSharedFields($table);
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('end_users', function (Blueprint $table) {
            $this->getSharedFields($table);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    //fields that exist on both Members and Leads.
    public function getSharedFields($table)
    {
        $table->uuid('id')->primary()->unique();
        $table->uuid('client_id')->index();
        $table->string('gr_location_id')->nullable();
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->string('email');
        $table->string('primary_phone');
        $table->string('alternate_phone')->nullable();
        $table->string('gender');
        $table->datetime('date_of_birth');
        $table->string('opportunity')->nullable();
        $table->integer('owner_user_id')->nullable();
        $table->integer('membership_type_id')->nullable();
        $table->longText('profile_picture_file_id')->nullable();
        $table->string('external_id')->nullable();
        $table->jsonb('misc')->nullable();
        $table->unique(['client_id', 'email']);
        $table->boolean('unsubscribed_email')->default(false);
        $table->boolean('unsubscribed_sms')->default(false);
        $table->string('ip_address')->nullable();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
        Schema::dropIfExists('members');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('end_users');
    }
}
