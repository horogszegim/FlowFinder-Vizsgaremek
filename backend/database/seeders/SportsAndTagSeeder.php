<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SportsAndTag;

class SportsAndTagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'gördeszka',
            'roller',
            'BMX',
            'MTB',
            'kosárlabda',
            'foci',
            'frizbi',

            'grind',
            'gap',
            'stairs',
            'rail',
            'curb',
            'ledge',
            'manual pad',
            'quarter pipe',
            'half pipe',
            'funbox',
            'bank',
            'hubba',
            'kicker',
            'pump track',

            'skatepark',
            'utcai spot',
            'pláza',
            'erdő',
            'trail',
            'dirt jump',

            'kezdőbarát',
            'amatőr',
            'haladó',

            'szabadtéri',
            'beltéri',
            'fedett kültéri',
        ];

        foreach ($tags as $tag) {
            SportsAndTag::firstOrCreate([
                'name' => $tag
            ]);
        }
    }
}
