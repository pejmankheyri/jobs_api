<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_update_their_own_user(): void
    {

        $user = $this->createUserWithRole('user');

        // Define the updated user data
        $updatedUserData = [
            'name' => 'New Name',
            'email' => 'newemail@example.com',
            'phone' => '08123456789',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'role_id' => Role::where('name', 'user')->first()->id,
        ];

        // Act as the user and make a PUT request to the /api/users/{id} endpoint
        $response = $this->actingAs($user, 'sanctum')->putJson('/api/v1/users', $updatedUserData);

        // Assert the status is 200 (OK)
        $response->assertStatus(200);

        // Assert the response contains the updated user's name and email
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
            ],
        ]);

    }
}
