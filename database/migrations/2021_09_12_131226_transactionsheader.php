<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transactionsheader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactionsheader', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_number');
            $table->string('transaction_type');
            $table->timestamp('transaction_date');
            $table->decimal('total_amount', 19, 4);
            $table->string('description');
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
        //
    }
}
