<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateTest extends TestCase
{

    use RefreshDatabase;

    public function test_users_can_update_their_own_user(): void
    {

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        // Create a user
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'oldemail@example.com',
            'password' => bcrypt('password'), // Assuming bcrypt is used for hashing passwords
        ]);

        $userRole = Role::where('name', 'user')->first();
        $user->roles()->attach($userRole);

        // Define the updated user data
        $updatedUserData = [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'role_id' => Role::where('name', 'user')->first()->id,
        ];

        // Act as the user and make a PUT request to the /api/users/{id} endpoint
        $response = $this->actingAs($user, 'sanctum')->putJson("/api/v1/users/{$user->id}", $updatedUserData);

        // Assert the status is 200 (OK)
        $response->assertStatus(200);

        // Assert the response contains the updated user's name and email
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role'
            ]
        ]);

        // Assert the user is updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com',
        ]);
    }

    public function test_users_can_not_update_other_users(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        // Create two users
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $userRole = Role::where('name', 'user')->first();
        $user->roles()->attach($userRole);
        $otherUser->roles()->attach($userRole);

        // Define the updated user data
        $updatedUserData = [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'role_id' => Role::where('name', 'user')->first()->id,
        ];

        // Act as the other user and make a PUT request to the /api/users/{id} endpoint
        $response = $this->actingAs($otherUser, 'sanctum')->putJson("/api/v1/users/{$user->id}", $updatedUserData);

        // Assert the status is 404 (Not Found)
        $response->assertStatus(404);

        // Assert the user is not updated in the database
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'newemail@example.com',
        ]);
    }
}
