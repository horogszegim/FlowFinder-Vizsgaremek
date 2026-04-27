<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(SportsAndTagSeeder::class);
        $this->call(SpotSeeder::class);
        $this->call(ImageSeeder::class);
        $this->call(SpotSportsAndTagSeeder::class);
        $this->call(SavedSpotSeeder::class);
    }
}
