<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrySourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_sources', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->string('source')->nullable();
            $table->boolean('ui')->default(1);
            $table->longText('misc')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('is_default_entry_source')->default(0);
            $table->unique(['client_id', 'name']);
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
        Schema::dropIfExists('entry_sources');
    }
}
