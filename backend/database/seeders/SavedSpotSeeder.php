<?php

namespace Database\Seeders;

use App\Models\SavedSpot;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Seeder;

class SavedSpotSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->orderBy('id')->get();
        $creatorIds = $users->pluck('id');

        foreach ($users as $user) {
            foreach ($creatorIds as $creatorId) {
                $spotIds = Spot::query()
                    ->where('created_by', $creatorId)
                    ->inRandomOrder()
                    ->limit(2)
                    ->pluck('id');

                foreach ($spotIds as $spotId) {
                    SavedSpot::firstOrCreate([
                        'user_id' => $user->id,
                        'spot_id' => $spotId,
                    ]);
                }
            }
        }
    }
}
