<?php

namespace Tests\Feature\JobItem;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_job_item(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);
        $jobItem = $this->createJobItem($company);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->deleteJson(route('api.v1.jobs.destroy', $jobItem->id));

        // Assert
        $response->assertStatus(200);
    }

    public function test_users_can_not_delete_non_existing_job_item(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->deleteJson(route('api.v1.jobs.destroy', 1000));

        // Assert
        $response->assertStatus(404);
    }

    public function test_users_can_not_delete_other_users_job_item(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');
        $otherUser = $this->createUserWithRole('user');

        $company = $this->createCompany($user);
        $jobItem = $this->createJobItem($company);

        // Act
        $response = $this->actingAs($otherUser, 'sanctum')->deleteJson(route('api.v1.jobs.destroy', $jobItem->id));

        // Assert
        $response->assertStatus(404);
    }

    public function test_guests_can_not_delete_job_item(): void
    {
        // Act
        $response = $this->deleteJson(route('api.v1.jobs.destroy', 1));

        // Assert
        $response->assertStatus(401);
    }
}
