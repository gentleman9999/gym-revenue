<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAudienceMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audience_members', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id')->nullable();
            $table->uuid('audience_id')->nullable();
            $table->uuid('entity_id')->nullable();
            $table->uuid('entity_type')->nullable();
            $table->boolean('subscribed')->default(1);
            $table->longText('misc')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('dnc')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audience_members');
    }
}
