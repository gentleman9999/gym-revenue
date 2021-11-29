<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id')->unique()->primary();
            $table->foreignUuid('client_id');
            $table->string('filename');
            $table->string('original_filename');
            $table->string('extension');
            $table->string('bucket');
            $table->text('url');
            $table->string('key');
            $table->integer('size');
//            $table->string('isPublic');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
