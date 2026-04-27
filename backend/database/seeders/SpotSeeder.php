<?php

namespace Database\Seeders;

use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpotSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = User::query()->orderBy('id')->pluck('id')->all();

        if (empty($userIds)) {
            return;
        }

        $createdByIds = [];

        foreach ($userIds as $userId) {
            $createdByIds[] = $userId;
            $createdByIds[] = $userId;
        }

        while (count($createdByIds) < 550) {
            $createdByIds[] = $userIds[array_rand($userIds)];
        }

        shuffle($createdByIds);

        foreach ($createdByIds as $createdById) {
            Spot::factory()->create([
                'created_by' => $createdById,
            ]);
        }
    }
}
