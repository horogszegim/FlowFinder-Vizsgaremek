<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Spot;
use App\Models\Image;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_200()
    {
        $response = $this->getJson('/api/images');

        $response->assertStatus(200);
    }

    public function test_index_returns_images()
    {
        $user = User::create([
            'username' => 'imguser',
            'email' => 'img@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $spot = Spot::create([
            'title' => 'Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $user->id
        ]);

        Image::create([
            'spot_id' => $spot->id,
            'path' => 'test.jpg'
        ]);

        $response = $this->getJson('/api/images');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'path' => 'test.jpg'
                 ]);
    }

    public function test_show_returns_single_image()
    {
        $user = User::create([
            'username' => 'singleimg',
            'email' => 'singleimg@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $spot = Spot::create([
            'title' => 'Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $user->id
        ]);

        $image = Image::create([
            'spot_id' => $spot->id,
            'path' => 'single.jpg'
        ]);

        $response = $this->getJson("/api/images/{$image->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'path' => 'single.jpg'
                 ]);
    }

    public function test_show_returns_404()
    {
        $response = $this->getJson('/api/images/999999');

        $response->assertStatus(404);
    }

    public function test_store_requires_auth()
    {
        $response = $this->postJson('/api/images', []);

        $response->assertStatus(401);
    }

    public function test_destroy_requires_auth()
    {
        $response = $this->deleteJson('/api/images/1');

        $response->assertStatus(401);
    }
}