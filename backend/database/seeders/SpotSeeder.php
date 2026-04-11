<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SpotSeeder extends Seeder
{
    public function run(): void
    {
        $spots = [];

        for ($i = 1; $i <= 10; $i++) {
            $spots[] = [
                'title' => "Spot {$i} (Teszt)",
                'description' => "Ez a szöveg egy meglehetősen hosszú, igazán részletes, valamint felettébb menő és érdekfelkeltő tesztleírás, amely az egyik tesztelési Spothoz készült. Remélem, hogy elegendően kielégítőnek találod, mert ha nem, akkor irgum-burgum. A teszt Spot amihez készült a leírás: {$i}",
                'latitude' => mt_rand(45500000, 48500000) / 1000000,
                'longitude' => mt_rand(16000000, 23000000) / 1000000,
                'created_by' => $i <= 5 ? 1 : 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('spots')->insert($spots);
    }
}
