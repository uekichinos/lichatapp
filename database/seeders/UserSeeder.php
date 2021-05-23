<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([[
            'name' => 'Administrator',
            'email' => 'admin@localhost.com',
            'password' => Hash::make('admin123'),
        ],[
            'name' => 'Tester1',
            'email' => 'tester1@localhost.com',
            'password' => Hash::make('tester1'),
        ],[
            'name' => 'Tester2',
            'email' => 'tester2@localhost.com',
            'password' => Hash::make('tester2'),
        ]]);
    }
}
