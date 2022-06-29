<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientGatewayIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_gateway_integrations', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('gateway_id');
            $table->string('gateway_slug');
            $table->uuid('client_id')->nullable();
            $table->uuid('provider_type')->nullable();
            $table->string('nickname');
            $table->boolean('active')->default(1);
            $table->index(['client_id', 'gateway_id']);
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
        Schema::dropIfExists('client_gateway_integrations');
    }
}
