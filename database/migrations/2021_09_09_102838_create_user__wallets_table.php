<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user__wallets', function (Blueprint $table) {
            $table->id();
            $table->string('user_email');
            $table->string('wallet_type');
            $table->string('wallet_address');
            $table->decimal('wallet_balance', 19, 0)->default(0.00);
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
        Schema::dropIfExists('user__wallets');
    }
}
