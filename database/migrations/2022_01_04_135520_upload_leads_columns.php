<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UploadLeadsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('lead_type');
            $table->integer('lead_type_id')->after('ip_address')->nullable();
            $table->integer('lead_source_id')->after('ip_address')->nullable();
            $table->integer('membership_type_id')->after('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('lead_type')->nullable()->after('ip_address');
            $table->dropColumn('lead_type_id');
            $table->dropColumn('lead_source_id');
            $table->dropColumn('membership_type_id');
        });
    }
}
