<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            if(!Schema::hasTable('user_member_groups')) {
                Schema::create('user_member_groups', function (Blueprint $table) {
                    $table->uuid('id')->primary()->unique();
                    $table->uuid('client_id')->index();
                    $table->uuid('member_group_id');
                    $table->unsignedBigInteger('user_id');
                    $table->boolean('is_primary')->default(false);
                    $table->timestamps();

                    $table->foreign('member_group_id')->references('id')->on('member_groups')->cascadeOnDelete()->cascadeOnUpdate();
                    $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
                });
            }
        } catch (\Exception $e) {
            Log::info($e);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_member_groups');
    }
};
