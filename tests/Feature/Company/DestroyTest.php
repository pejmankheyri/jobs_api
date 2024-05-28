<?php

namespace Tests\Feature\Company;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_company(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->deleteJson(route('api.v1.companies.destroy', $company->id));

        // Assert
        $response->assertStatus(200);
    }

    public function test_users_can_not_delete_non_existing_company(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson(route('api.v1.companies.destroy', 1000));

        // Assert
        $response->assertStatus(404);
    }

    public function test_users_can_not_delete_other_users_company(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');
        $otherUser = $this->createUserWithRole('user');

        $company = $this->createCompany($user);

        // Act
        $response = $this->actingAs($otherUser, 'sanctum')->deleteJson(route('api.v1.companies.destroy', $company->id));

        // Assert
        $response->assertStatus(404);
    }

    public function test_guests_can_not_delete_company(): void
    {
        // Act
        $response = $this->deleteJson(route('api.v1.companies.destroy', 1));

        // Assert
        $response->assertStatus(401);
    }
}
