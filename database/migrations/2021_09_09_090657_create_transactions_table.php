<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactionsdetail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_number');
            $table->string('email');
            $table->string('wallet_address');
            $table->decimal('dramount', 19,4);
            $table->decimal('cramount', 19,4);
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
        Schema::dropIfExists('transactions');
    }
}
