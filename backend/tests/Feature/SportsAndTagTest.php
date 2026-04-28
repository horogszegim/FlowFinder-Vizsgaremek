<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\SportsAndTag;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SportsAndTagTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_200()
    {
        $response = $this->getJson('/api/sports-and-tags');

        $response->assertStatus(200);
    }

    public function test_index_returns_tags()
    {
        SportsAndTag::create([
            'name' => 'BMX'
        ]);

        $response = $this->getJson('/api/sports-and-tags');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'BMX'
                 ]);
    }

    public function test_show_returns_single_tag()
    {
        $tag = SportsAndTag::create([
            'name' => 'Skate'
        ]);

        $response = $this->getJson("/api/sports-and-tags/{$tag->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => 'Skate'
                 ]);
    }

    public function test_show_returns_404()
    {
        $response = $this->getJson('/api/sports-and-tags/999999');

        $response->assertStatus(404);
    }
}