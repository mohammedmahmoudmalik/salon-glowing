<?php

namespace Tests\Feature\Auth;

use App\Domains\Auth\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    // ─── Registration ────────────────────────────────────────────────────────

    public function test_customer_can_register_with_email_only(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data' => ['user', 'token']])
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertTrue(User::where('email', 'test@example.com')->first()->hasRole('customer'));
    }

    public function test_customer_can_register_with_phone_only(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'phone' => '+966500000001',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['phone' => '+966500000001']);
    }

    public function test_registration_fails_without_email_or_phone(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['email', 'phone']]);
    }

    public function test_registration_creates_customer_record(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'customer@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $user = User::where('email', 'customer@example.com')->first();

        $this->assertNotNull($user);
        $this->assertDatabaseHas('customers', ['user_id' => $user->id]);
        $this->assertNotNull($user->customer);
    }

    // ─── Login ───────────────────────────────────────────────────────────────

    public function test_user_can_login_with_email(): void
    {
        $user = User::factory()->create(['password' => 'Password@123']);
        $user->assignRole('customer');

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $user->email,
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_user_can_login_with_phone(): void
    {
        $user = User::factory()->create([
            'phone' => '+966500000002',
            'password' => 'Password@123',
        ]);
        $user->assignRole('customer');

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => '+966500000002',
            'password' => 'Password@123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['token']]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => 'Password@123']);

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $user->email,
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->inactive()->create(['password' => 'Password@123']);

        $response = $this->postJson('/api/v1/auth/login', [
            'login' => $user->email,
            'password' => 'Password@123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    // ─── Profile ─────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/profile');

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'roles']]);
    }

    public function test_unauthenticated_user_cannot_view_profile(): void
    {
        $this->getJson('/api/v1/profile')->assertStatus(401);
    }

    public function test_user_can_update_name(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/v1/profile', [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_password_update_does_not_double_hash(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword@123']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/profile', [
            'current_password' => 'OldPassword@123',
            'password' => 'NewPassword@123',
            'password_confirmation' => 'NewPassword@123',
        ])->assertStatus(200);

        // Must succeed with new password — proves no double-hashing
        $this->postJson('/api/v1/auth/login', [
            'login' => $user->email,
            'password' => 'NewPassword@123',
        ])->assertStatus(200);
    }

    public function test_profile_update_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword@123']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/profile', [
            'current_password' => 'WrongPassword',
            'password' => 'NewPassword@123',
            'password_confirmation' => 'NewPassword@123',
        ])->assertStatus(422);
    }
}
