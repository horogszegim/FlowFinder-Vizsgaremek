<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [];

        $testUsers = [
            'flow',
            'finder',
            'ride',
            'radar',
            'street',
            'spotter',
            'roll',
            'orbit',
            'marker',
            'explorer',
        ];

        foreach ($testUsers as $index => $word) {
            $number = $index + 1;

            $users[] = [
                'username' => $word . '_testuser_' . $number,
                'email' => 'testuser.' . $number . '@flowfinder.hu',
                'email_verified_at' => null,
                'password' => Hash::make('TestUser' . $number . '!'),
                'remember_token' => null,
                'role' => 'user',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        $testAdmins = [
            'atlas',
            'compass',
            'map',
        ];

        foreach ($testAdmins as $index => $word) {
            $number = $index + 1;

            $users[] = [
                'username' => $word . '_testadmin_' . $number,
                'email' => 'testadmin.' . $number . '@flowfinder.hu',
                'email_verified_at' => null,
                'password' => Hash::make('TestAdmin' . $number . '!'),
                'remember_token' => null,
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('users')->insert($users);
    }
}
