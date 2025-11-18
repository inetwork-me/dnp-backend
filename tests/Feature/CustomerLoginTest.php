<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerLoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_requires_email_and_password()
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(200) // validation returns 200
            ->assertJson([
                'result' => false,
            ])
            ->assertJsonStructure(['message']); // array of validation errors
    }

    /** @test */
    public function it_returns_401_if_user_not_found()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'nonexistent@example.com',
            'password' => 'whatever',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'result'  => false,
                'message' => 'User not found',
                'user'    => null,
            ]);
    }

    /** @test */
    public function it_returns_401_if_user_is_banned()
    {
        $user = User::factory()->create([
            'email'    => 'banned@example.com',
            'password' => bcrypt('secret'),
            'banned'   => true,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'result'  => false,
                'message' => 'User is banned',
                'user'    => null,
            ]);
    }

    /** @test */
    public function it_returns_401_if_password_is_invalid()
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('correct-password'),
            'banned'   => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'result'  => false,
                'message' => 'Unauthorized',
                'user'    => null,
            ]);
    }

    /** @test */
    public function it_returns_token_and_user_on_successful_login()
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('correct-password'),
            'banned'   => false,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'result' => true,
            ])
            ->assertJsonStructure([
                'result',
                'token',
                'user' => [
                    'id',
                    'email',
                    // ... whatever fields you return
                ],
            ]);
    }
}
