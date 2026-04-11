<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Spot;
use App\Models\SportsAndTag;

class SpotSportsAndTagSeeder extends Seeder
{
    public function run(): void
    {
        $tagIds = SportsAndTag::pluck('id')->toArray();

        foreach (Spot::all() as $spot) {
            $randomCount = rand(0, 5);

            if ($randomCount === 0) {
                continue;
            }

            $randomTags = collect($tagIds)
                ->shuffle()
                ->take($randomCount)
                ->toArray();

            $spot->sportsAndTags()->sync($randomTags);
        }
    }
}
