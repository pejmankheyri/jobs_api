<?php

namespace Tests\Feature\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if admin can see users list
     */
    public function test_admin_can_see_users_list_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');

        User::factory(10)->create()->each(function ($user) {
            $user->roles()->attach(Role::where('name', 'company')->first());
        });

        // Act
        $response = $this->actingAs($admin, 'sanctum')->getJson(route('api.v1.users.index'));

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role'
                ]
            ]
        ]);
    }

    public function test_users_can_not_see_all_users_list(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson(route('api.v1.users.index'));

        // Assertion
        $response->assertStatus(401);
    }

    public function test_guest_can_not_see_users_list(): void
    {
        // Act
        $response = $this->getJson(route('api.v1.users.index'));
        // Assertion
        $response->assertStatus(401);
    }
}
