<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name');
            $table->longText('markup')->nullable();
            $table->boolean('active')->default(1);
            $table->uuid('client_id')->nullable()->index();
            $table->uuid('team_id')->nullable();
            $table->string('created_by_user_id');
            $table->jsonb('details')->nullable();
            $table->index(['client_id', 'team_id']);
            $table->index(['client_id', 'created_by_user_id']);
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
        Schema::dropIfExists('sms_templates');
    }
}
