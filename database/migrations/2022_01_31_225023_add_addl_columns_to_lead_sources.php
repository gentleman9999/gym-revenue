<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddlColumnsToLeadSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lead_sources', function (Blueprint $table) {
            $table->string('source')->nullable()->after('client_id');
            $table->boolean('ui')->default(1)->after('source');
            $table->longText('misc')->after('ui')->nullable();
            $table->boolean('active')->default(1)->after('misc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // These are required columns so dropping this won't remove the columns. Sorry (not sorry)!
    }
}
