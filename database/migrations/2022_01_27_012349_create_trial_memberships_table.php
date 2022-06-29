<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrialMembershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trial_memberships', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id');
            $table->uuid('type_id');
            $table->uuid('lead_id');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->uuid('location_id');
            $table->boolean('active')->default(0);
            $table->index(['client_id', 'type_id']);
            $table->index(['client_id', 'lead_id']);
            $table->index(['client_id', 'location_id']);
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
        Schema::dropIfExists('trial_memberships');
    }
}
