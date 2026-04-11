<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'username' => 'testadmin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('Admin123!'),
                'remember_token' => null,
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'username' => 'testuser',
                'email' => 'user@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('User123!'),
                'remember_token' => null,
                'role' => 'user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
