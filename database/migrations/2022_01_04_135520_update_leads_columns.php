<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeadsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->renameColumn('mobile_phone', 'primary_phone');
            $table->renameColumn('home_phone', 'alternate_phone');
            $table->dropColumn('lead_type');
            $table->integer('lead_type_id')->after('ip_address')->nullable();
            $table->uuid('lead_source_id')->after('ip_address')->nullable();
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
            $table->renameColumn('primary_phone', 'mobile_phone');
            $table->renameColumn( 'alternate_phone', 'home_phone');
        });
    }
}
