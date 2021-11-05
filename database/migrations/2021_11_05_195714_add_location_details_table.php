<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_details', function (Blueprint $table) {
            $table->string('id')->primary()->unique();

            $table->foreignId('location_id')->nullable()->constrained('locations');
            $table->foreignUuid('client_id')->nullable()->constrained('clients');
            $table->string('field');
            $table->longText('value')->nullable();
            $table->longText('misc')->nullable();

            $table->boolean('active')->default(1);
            $table->string('created_at')->nullable();
            $table->string('updated_at')->nullable();
            $table->string('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_details');
    }
}
