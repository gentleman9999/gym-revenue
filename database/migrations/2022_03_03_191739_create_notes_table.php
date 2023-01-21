<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->uuid('entity_id')->nullable()->index();
            $table->string('entity_type')->nullable();
            $table->mediumText('title');
            $table->mediumText('note')->nullable();
            $table->string('category')->nullable();
            $table->foreignUuid('created_by_user_id')->index();
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('notes');
    }
}
