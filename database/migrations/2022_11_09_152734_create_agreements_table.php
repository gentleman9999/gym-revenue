<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agreements', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->uuid('client_id')->index();
            $table->uuid('agreement_category_id');
            $table->foreign('agreement_category_id')
                        ->references('id')
                        ->on('agreement_categories')
                        ->cascadeOnDelete();
            $table->string('gr_location_id')->nullable();
            $table->uuid('created_by');
            $table->uuid('end_user_id');
            $table->uuid('agreement_template_id');
            $table->uuid('contract_id')->nullable();
            $table->foreign('contract_id')
                        ->references('id')
                        ->on('contracts')
                        ->cascadeOnDelete();
            $table->boolean('active')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agreements');
    }
};
