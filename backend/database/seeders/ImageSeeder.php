<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Image;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $availableImages = [
            'seed/images/1.png',
            'seed/images/2.png',
            'seed/images/3.png',
            'seed/images/4.png',
            'seed/images/5.png',
            'seed/images/6.png',
            'seed/images/7.png',
            'seed/images/8.png',
            'seed/images/9.png',
            'seed/images/10.png',
        ];

        for ($spotId = 1; $spotId <= 10; $spotId++) {

            $imageCount = 10 - $spotId;

            if ($imageCount <= 0) continue;

            $shuffled = collect($availableImages)->shuffle()->take($imageCount);

            foreach ($shuffled as $path) {
                Image::create([
                    'spot_id' => $spotId,
                    'path' => $path,
                ]);
            }
        }
    }
}
