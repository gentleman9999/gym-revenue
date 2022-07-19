<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadAndMemberDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_details', function (Blueprint $table) {
            $this->getSharedFields($table);
            $table->string('lead_id')->index();
        });
        Schema::create('member_details', function (Blueprint $table) {
            $this->getSharedFields($table);
            $table->string('member_id')->index();
        });
    }

    //fields that exist on both LeadDetails and MemberDetails.
    public function getSharedFields($table)
    {
        $table->uuid('id')->primary()->unique();
        $table->string('client_id')->index();
        $table->string('field');
        $table->string('value')->nullable();
        $table->mediumText('misc')->nullable();
        $table->boolean('active')->default(1);
        $table->timestamps();
        $table->softDeletes();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lead_details');
        Schema::dropIfExists('member_details');
    }
}
