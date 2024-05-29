<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_see_their_own_user(): void
    {
        // Arrange
        $user = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson(route('api.v1.users.show', $user->id));

        // Assert
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    public function test_users_can_not_see_other_users(): void
    {
        $user = $this->createUserWithRole('user');
        $otherUser = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($otherUser, 'sanctum')->getJson(route('api.v1.users.show', $user->id));

        // Assert
        $response->assertStatus(404);
    }

    public function test_guest_can_not_see_users(): void
    {
        $user = User::factory()->create();
        $this->getJson(route('api.v1.users.show', $user->id))
            ->assertUnauthorized();
    }
}
