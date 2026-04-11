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

            'grind',
            'gap',
            'stairs',
            'rail',
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
            'street spot',
            'plaza',
            'erdő',
            'trail',
            'dirt jump',

            'kezdőbarát',
            'amatőr',
            'haladó',

            'szabadtéri',
            'fedett',
        ];

        foreach ($tags as $tag) {
            SportsAndTag::firstOrCreate([
                'name' => $tag
            ]);
        }
    }
}
