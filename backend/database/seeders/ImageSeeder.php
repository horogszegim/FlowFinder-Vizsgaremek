<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\Spot;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $availableImages = [
            'https://i.ibb.co/rRjPPPZz/1.png',
            'https://i.ibb.co/m5rTn0Sq/2.png',
            'https://i.ibb.co/ynbqYxnN/3.png',
            'https://i.ibb.co/sd2JCKJK/4.png',
            'https://i.ibb.co/Nddq8HJx/5.png',
            'https://i.ibb.co/JWSpH0Fm/6.png',
            'https://i.ibb.co/CNPT1yf/7.png',
            'https://i.ibb.co/LD06c7pd/8.png',
            'https://i.ibb.co/wFtYywK7/9.png',
            'https://i.ibb.co/0NbKvg7/10.png',
            'https://i.ibb.co/1Y3tmnnL/11.png',
            'https://i.ibb.co/TMP1RFkf/12.png',
            'https://i.ibb.co/d4k6DQtW/13.png',
            'https://i.ibb.co/XktGd2X5/14.png',
            'https://i.ibb.co/ZpsDZK9W/15.png',
        ];

        foreach (Spot::query()->orderBy('id')->get() as $spot) {
            $imageCount = rand(1, 10);

            $randomImages = $availableImages;
            shuffle($randomImages);

            $selectedImages = array_slice($randomImages, 0, $imageCount);

            foreach ($selectedImages as $index => $imagePath) {
                Image::create([
                    'spot_id' => $spot->id,
                    'path' => $imagePath,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
}
