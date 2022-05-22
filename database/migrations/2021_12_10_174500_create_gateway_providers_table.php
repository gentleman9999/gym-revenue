<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewayProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_providers', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name');
            $table->string('slug');
            $table->text('desc')->nullable();
            $table->string('vendor')->nullable();
            $table->uuid('provider_type');
            $table->string('profile_class')->nullable();
            $table->float('provider_rate', 8, 2)->default(0.00);
            $table->float('provider_bulk_rate', 8, 2)->default(0.00);
            $table->float('gr_commission_rate', 8, 2)->default(0.00);
            $table->float('gr_commission_bulk_rate', 8, 2)->default(0.00);

            $table->longText('misc')->nullable();
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('gateway_providers');
    }
}
