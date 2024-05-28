<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_users_can_be_created_by_admin(): void
    {
        $admin = $this->createUserWithAdminRole();

        // get user role
        $role = Role::where('name', 'user')->first();

        $userData = [
            'name' => 'John Doe',
            'email' => 'admin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role_id' => $role->id,
        ];

        $response = $this->actingAs($admin, 'sanctum')->postJson(route('api.v1.users.store'), $userData);

        $response->assertStatus(201);

        // Assert the response contains the user's name and email
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role'
            ]
        ]);

        // Assert the user is in the database
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);
    }

    public function test_users_can_not_be_created_with_invalid_data(): void
    {
        // Define various sets of invalid user data
        $invalidUserDataSets = [
            // Empty data
            [],
            // Missing name
            ['email' => 'johndoe@example.com', 'password' => 'password', 'password_confirmation' => 'password'],
            // Invalid email
            ['name' => 'John Doe', 'email' => 'not-an-email', 'password' => 'password', 'password_confirmation' => 'password'],
            // Passwords do not match
            ['name' => 'John Doe', 'email' => 'johndoe@example.com', 'password' => 'password', 'password_confirmation' => 'differentpassword'],
            // Password too short
            ['name' => 'John Doe', 'email' => 'johndoe@example.com', 'password' => 'short', 'password_confirmation' => 'short'],
        ];

        // Define the user data
        $user = $this->createUserWithAdminRole();

        $response = $this->actingAs($user, 'sanctum')->getJson(route('api.v1.users.store'));

        foreach ($invalidUserDataSets as $invalidUserData) {
            // Make a POST request to the /api/users endpoint
            $response = $this->postJson(route('api.v1.users.store'), $invalidUserData);

            // Assert the status is 422 (Unprocessable Entity)
            $response->assertStatus(422);
        }
    }

    public function test_users_can_not_be_created_by_unauthenticated_users(): void
    {
        // Arrange
        $this->createUserWithAdminRole();

        // Act
        $response = $this->getJson(route('api.v1.users.store'));

        // Assert
        $response->assertStatus(401);
    }

    private function createUserWithAdminRole() {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        $user = User::create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => Hash::make('password'),
        ]);

        $userRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($userRole);

        return $user;
    }
}
