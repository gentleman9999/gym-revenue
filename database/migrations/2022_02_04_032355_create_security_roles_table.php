<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecurityRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { /*
        Schema::create('security_roles', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id');
            $table->integer('role_id');
            $table->string('security_role');
            $table->longText('ability_ids');
            $table->boolean('active')->default(1);
            $table->longText('misc')->nullable();
            $table->timestamps();
            $table->softDeletes();
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('security_roles');
    }
}
