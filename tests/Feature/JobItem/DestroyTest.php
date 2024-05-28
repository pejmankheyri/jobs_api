<?php

namespace Tests\Feature\JobItem;

use App\Models\Company;
use App\Models\JobItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

    private function createJobItem($company)
    {
        $jobItem = JobItem::factory()->make();
        $jobItem->company_id = $company->id;
        $jobItem->save();

        return $jobItem;
    }

    private function createCompany($admin)
    {
        $company = Company::factory()->make();
        $company->user_id = $admin->id;
        $company->save();

        return $company;
    }

    private function createUserWithRole($role): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        $user = User::factory()->create();

        $user->roles()->attach(Role::where('name', $role)->first());

        return $user;
    }
}
