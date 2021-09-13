<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wallets;

class walletseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $wallettypeA = ['wallet_type'=>'starter', 'wallet_name'=>'Starter Wallet', 'minimum_balance'=>1000, 'interest_rate'=> 5];
        $wallettypeB = ['wallet_type'=>'basic', 'wallet_name'=>'Basic Wallet', 'minimum_balance'=>2000, 'interest_rate'=> 10];
        $wallettypeC = ['wallet_type'=>'premium', 'wallet_name'=>'Premium Wallet', 'minimum_balance'=>3000, 'interest_rate'=> 15];

        Wallets::create($wallettypeA);
        Wallets::create($wallettypeB);
        Wallets::create($wallettypeC);
    }
}
