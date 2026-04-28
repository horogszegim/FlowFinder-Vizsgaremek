<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Spot;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpotTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_200()
    {
        $this->getJson('/api/spots')
            ->assertStatus(200);
    }

    public function test_index_returns_spots()
    {
        $user = User::create([
            'username' => 'spotuser',
            'email' => 'spot@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        Spot::create([
            'title' => 'Teszt Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $user->id
        ]);

        $this->getJson('/api/spots')
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Teszt Spot'
            ]);
    }

    public function test_show_returns_single_spot()
    {
        $user = User::create([
            'username' => 'singleuser',
            'email' => 'single@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $spot = Spot::create([
            'title' => 'Single Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $user->id
        ]);

        $this->getJson("/api/spots/{$spot->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Single Spot'
            ]);
    }

    public function test_show_returns_404()
    {
        $this->getJson('/api/spots/999999')
            ->assertStatus(404);
    }

    public function test_store_requires_auth()
    {
        $this->postJson('/api/spots', [])
            ->assertStatus(401);
    }

    public function test_store_creates_spot()
    {
        $user = User::create([
            'username' => 'authuser',
            'email' => 'auth@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/spots', [
            'title' => 'Uj Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1'
        ])->assertStatus(201);

        $this->assertDatabaseHas('spots', [
            'title' => 'Uj Spot'
        ]);
    }

    public function test_destroy_requires_auth()
    {
        $this->deleteJson('/api/spots/1')
            ->assertStatus(401);
    }

    public function test_destroy_deletes_spot()
    {
        $user = User::create([
            'username' => 'deleteuser',
            'email' => 'delete@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        Sanctum::actingAs($user);

        $spot = Spot::create([
            'title' => 'Delete Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $user->id
        ]);

        $this->deleteJson("/api/spots/{$spot->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('spots', [
            'id' => $spot->id
        ]);
    }
}