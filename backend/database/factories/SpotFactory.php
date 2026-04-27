<?php

namespace Database\Factories;

use App\Models\Spot;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpotFactory extends Factory
{
    protected $model = Spot::class;

    private array $words = [
        'beton',
        'korlát',
        'lépcső',
        'pálya',
        'fal',
        'tér',
        'park',
        'híd',
        'rámpa',
        'padka',
        'erdő',
        'ösvény',
        'kanyar',
        'ugrató',
        'aszfalt',
        'placc',
        'aluljáró',
        'sarok',
        'domb',
        'udvar',
        'garázs',
        'járda',
        'csúszás',
        'lendület',
        'árnyék',
        'napfény',
        'város',
        'peron',
        'liget',
        'kapu',
        'zóna',
        'vonal',
        'kő',
        'tégla',
        'fém',
        'fa',
        'csatorna',
        'part',
        'tető',
        'bejárat',
        'sziget',
        'folyosó',
        'terasz',
        'pihenő',
        'ív',
        'szint',
        'átjáró',
        'mező',
        'rakpart',
        'kavics',
    ];

    public function definition(): array
    {
        return [
            'title' => $this->makeTitle(),
            'description' => $this->makeDescription(),
            'latitude' => (string) (mt_rand(45500000, 48500000) / 1000000),
            'longitude' => (string) (mt_rand(16000000, 23000000) / 1000000),
        ];
    }

    private function makeTitle(): string
    {
        $count = random_int(2, 6);
        $words = $this->randomWords($count);
        $title = implode(' ', $words);

        while (mb_strlen($title) > 60 && count($words) > 2) {
            array_pop($words);
            $title = implode(' ', $words);
        }

        return mb_substr($title, 0, 60);
    }

    private function makeDescription(): string
    {
        $count = random_int(10, 80);
        $words = $this->randomWords($count);
        $description = implode(' ', $words) . '.';

        while (mb_strlen($description) > 2048 && count($words) > 10) {
            array_pop($words);
            $description = implode(' ', $words) . '.';
        }

        return mb_substr($description, 0, 2048);
    }

    private function randomWords(int $count): array
    {
        $items = [];

        for ($i = 0; $i < $count; $i++) {
            $items[] = $this->words[array_rand($this->words)];
        }

        return $items;
    }
}
