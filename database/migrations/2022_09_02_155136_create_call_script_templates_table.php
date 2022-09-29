<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_script_templates', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->string('name');
            $table->longText('script')->nullable();
            $table->longText('json')->nullable();
            $table->json('thumbnail')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('use_once')->default(0);
            $table->uuid('client_id')->nullable()->index();
            $table->uuid('team_id')->nullable();
            $table->string('created_by_user_id');
            //$table->unique(['client_id','team_id']);
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
        Schema::dropIfExists('callscript_templates');
    }
};
