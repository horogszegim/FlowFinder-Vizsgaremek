<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_200()
    {
        $this->getJson('/api/users')
            ->assertStatus(200);
    }

    public function test_index_returns_users()
    {
        User::create([
            'username' => 'tesztuser',
            'email' => 'teszt@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $this->getJson('/api/users')
            ->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'teszt@test.hu'
            ]);
    }

    public function test_show_returns_single_user()
    {
        $user = User::create([
            'username' => 'singleuser',
            'email' => 'single@test.hu',
            'password' => bcrypt('Password123!')
        ]);

        $this->getJson("/api/users/{$user->id}")
            ->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'single@test.hu'
            ]);
    }

    public function test_show_returns_404()
    {
        $this->getJson('/api/users/999999')
            ->assertStatus(404);
    }
}