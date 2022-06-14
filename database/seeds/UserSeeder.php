<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'emp_id' => '000856',
            'password' => bcrypt('password'),
            'role' => 1,
            'email' => 'benpadz08@gmail.com'
        ]);
    }
}
