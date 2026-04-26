<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpotSeeder extends Seeder
{
    public function run(): void
    {
        $spots = [];

        for ($i = 1; $i <= 1000; $i++) {

            $prefix = $i . '. ';
            $maxRandomTitleLength = 60 - strlen($prefix);
            $randomTitleLength = rand(1, max(1, $maxRandomTitleLength));

            $spots[] = [
                'created_by' => rand(1, 2),

                'title' => $prefix . Str::random($randomTitleLength),

                'description' => $i . '. ' . Str::random(rand(1, 2048)),

                'latitude' => (string) (mt_rand(45500000, 48500000) / 1000000),
                'longitude' => (string) (mt_rand(16000000, 23000000) / 1000000),

                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('spots')->insert($spots);
    }
}
