<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_register_with_valid_data(): void
    {
        Role::firstOrCreate(['name' => 'masyarakat']);

        $response = $this->postJson('/api/register', [
            'name' => 'Rojali',
            'email' => 'rojali@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonMissingPath('user.password');

        $this->assertDatabaseHas('users', [
            'email' => 'rojali@example.com',
        ]);
    }

    public function test_registered_user_always_gets_masyarakat_role(): void
    {
        $masyarakat = Role::firstOrCreate(['name' => 'masyarakat']);
        Role::firstOrCreate(['name' => 'admin_desa']);

        $this->postJson('/api/register', [
            'name' => 'Rojali',
            'email' => 'rojali2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'rojali2@example.com',
            'role_id' => $masyarakat->id,
        ]);
    }

    public function test_register_fails_with_duplicate_email(): void
    {
        Role::firstOrCreate(['name' => 'masyarakat']);
        User::factory()->create(['email' => 'dup@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_register_fails_when_password_confirmation_mismatch(): void
    {
        Role::firstOrCreate(['name' => 'masyarakat']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('password');
    }
}
