<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_user()
    {
        $response = $this->postJson('/api/registration', [
            'username' => 'valaki',
            'email' => 'valaki@test.hu',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'valaki@test.hu'
        ]);
    }

    public function test_registration_requires_fields()
    {
        $response = $this->postJson('/api/registration', []);

        $response->assertStatus(422);
    }

    public function test_login_success()
    {
        User::create([
            'username' => 'loginuser',
            'email' => 'login@test.hu',
            'password' => bcrypt('12345678')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@test.hu',
            'password' => '12345678'
        ]);

        $response->assertStatus(200);
    }

    public function test_login_wrong_password()
    {
        User::create([
            'username' => 'wronguser',
            'email' => 'wrong@test.hu',
            'password' => bcrypt('12345678')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@test.hu',
            'password' => 'rosszjelszo'
        ]);

        $response->assertStatus(401);
    }

    public function test_login_unknown_email()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nincs@test.hu',
            'password' => '12345678'
        ]);

        $response->assertStatus(401);
    }
}