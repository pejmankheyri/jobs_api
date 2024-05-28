<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_delete_their_own_user(): void
    {
        // Arrange
        $user = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson(route('api.v1.users.destroy', $user->id));

        // Assert
        $response->assertStatus(404);
    }

    public function test_users_can_not_delete_other_users(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');
        $otherUser = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($otherUser, 'sanctum')->deleteJson(route('api.v1.users.destroy', $user->id));

        // Assert
        $response->assertStatus(401);
    }

    public function test_admin_can_delete_users(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($admin, 'sanctum')->deleteJson(route('api.v1.users.destroy', $user->id));

        // Assert
        $response->assertStatus(200);
    }

    public function test_users_can_not_delete_non_existing_users(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson(route('api.v1.users.destroy', 1000));

        // Assert
        $response->assertStatus(401);
    }

    public function test_guests_can_not_delete_users(): void
    {
        // Act
        $response = $this->deleteJson(route('api.v1.users.destroy', 1));

        // Assert
        $response->assertStatus(401);
    }

    public function test_users_can_not_delete_users_with_invalid_id(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson(route('api.v1.users.destroy', 'invalid-id'));

        // Assert
        $response->assertStatus(401);
    }

    private function createUserWithRole($role): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        $user = User::factory()->create();

        $userRole = Role::where('name', $role)->first();

        $user->roles()->attach($userRole);

        return $user;
    }
}
