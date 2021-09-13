<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class userseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userA = ['name'=>'Ibrahim', 'email'=>'highbee4u@gmail.com', 'password'=>'12345'];
        $userB = ['name'=>'John', 'email'=>'john@gmail.com', 'password'=>'12345'];

        User::create($userA);
        User::create($userB);
    }
}
