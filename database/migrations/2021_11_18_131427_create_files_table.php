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
            $table->foreignUuid('user_id')->nullable();
            $table->string('filename');
            $table->string('original_filename');
            $table->string('extension');
            $table->string('bucket');
            $table->text('url')->nullable();
            $table->string('key');
            $table->integer('size');
            $table->string('permissions')->default('[]');
            $table->boolean('is_public')->default(false);
            $table->string('fileable_type')->nullable(); //polymorphic fields
            $table->string('fileable_id')->nullable(); //polymorphic fields
//            $table->nullableMorphs('fileable');
            $table->boolean('visibility')->default(false);
            $table->boolean('is_hidden')->default(false);
            $table->string('type')->nullable();
            $table->foreignUuid('folder')->nullable();
            $table->index(['client_id', 'user_id']);
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
