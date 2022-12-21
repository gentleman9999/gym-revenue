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
            if(!Schema::hasTable('member_groups')) {
                Schema::create('member_groups', function (Blueprint $table) {
                    $table->uuid('id')->primary()->unique();
                    $table->uuid('client_id')->index();
                    $table->string('type');
                    $table->string('poc_name')->nullable();
                    $table->string('poc_phone')->nullable();
                    $table->string('poc_email')->nullable();
                    $table->softDeletes();
                    $table->timestamps();
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
        Schema::dropIfExists('member_groups');
    }
};
