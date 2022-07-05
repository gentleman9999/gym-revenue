<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadAndMembersTables extends Migration
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
            $table->integer('lead_type_id');
            $table->uuid('lead_source_id');
            $table->timestamp('converted_at')->nullable()->default(null);
            $table->uuid('member_id')->nullable()->default(null);
            $table->string('opportunity')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('members', function (Blueprint $table) {
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
        $table->string('agreement_number');
        $table->integer('membership_type_id')->nullable();
        $table->string('profile_picture')->nullable();
        $table->string('external_id')->nullable();
        $table->boolean('unsubscribed_comms')->default(false);
        $table->jsonb('misc')->nullable();
        $table->unique(['client_id', 'email']);
        $table->boolean('unsubscribed_email')->default(false);
        $table->boolean('unsubscribed_sms')->default(false);
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
    }
}
