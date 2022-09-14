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
        Schema::table('client_communication_preferences', function (Blueprint $table) {
            $table->boolean('voice')->default(false)->after('email');
            $table->boolean('conversation')->default(false)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_communication_preferences', function (Blueprint $table) {
            $table->dropColumn('voice');
            $table->dropColumn('conversation');
        });
    }
};
