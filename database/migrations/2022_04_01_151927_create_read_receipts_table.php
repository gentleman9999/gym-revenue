<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('read_receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid('note_id');
            $table->integer('read_by_user_id');
            $table->timestamps();
            $table->unique(
                ['note_id', 'read_by_user_id'],
                'note_receipt_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('read_receipts');
    }
}
