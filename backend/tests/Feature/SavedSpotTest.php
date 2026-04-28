<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Spot;
use App\Models\SavedSpot;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SavedSpotTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_auth()
    {
        $response = $this->getJson('/api/saved-spots');

        $response->assertStatus(401);
    }

    public function test_index_returns_saved_spots()
    {
        $user = User::create([
            'username' => 'saveuser',
            'email' => 'save@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $owner = User::create([
            'username' => 'owneruser',
            'email' => 'owner@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $spot = Spot::create([
            'title' => 'Mentett Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $owner->id
        ]);

        SavedSpot::create([
            'user_id' => $user->id,
            'spot_id' => $spot->id
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/saved-spots');

        $response->assertStatus(200);
    }

    public function test_store_requires_auth()
    {
        $response = $this->postJson('/api/saved-spots', []);

        $response->assertStatus(401);
    }

    public function test_store_creates_saved_spot()
    {
        $user = User::create([
            'username' => 'saveuser2',
            'email' => 'save2@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $owner = User::create([
            'username' => 'owner2',
            'email' => 'owner2@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $spot = Spot::create([
            'title' => 'Spot',
            'description' => 'Leiras',
            'latitude' => '47.1',
            'longitude' => '19.1',
            'created_by' => $owner->id
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/saved-spots', [
            'spot_id' => $spot->id
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('saved_spots', [
            'user_id' => $user->id,
            'spot_id' => $spot->id
        ]);
    }

    public function test_destroy_requires_auth()
    {
        $response = $this->deleteJson('/api/saved-spots/1');

        $response->assertStatus(401);
    }
}