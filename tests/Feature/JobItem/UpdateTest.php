<?php

namespace Tests\Feature\JobItem;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if admin can update job item
     */
    public function test_admin_can_update_job_item_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);
        $jobItem = $this->createJobItem($company);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->putJson(route('api.v1.jobs.update', $jobItem->id), [
            'title' => 'New title',
            'description' => 'New description',
            'tags' => ['tag1', 'tag2'],
            'company_id' => $company->id,
        ]);

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'create_dates',
                'update_dates',
                'tags' => [
                    '*' => [
                        'id',
                        'name',
                        'create_dates' => [
                            'created_at_human',
                            'created_at',
                        ],
                        'update_dates' => [
                            'updated_at_human',
                            'updated_at',
                        ],
                    ],
                ],
                'company' => [
                    'id',
                    'title',
                    'description',
                    'rating',
                    'website',
                    'employes',
                    'create_dates' => [
                        'created_at_human',
                        'created_at',
                    ],
                    'update_dates' => [
                        'updated_at_human',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Test if admin can not update job item with invalid data
     */
    public function test_admin_can_not_update_job_item_with_invalid_data(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);
        $jobItem = $this->createJobItem($company);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->putJson(route('api.v1.jobs.update', $jobItem->id), [
            'title' => '',
            'description' => '',
            'tags' => '',
            'company_id' => '',
        ]);

        // Assertions
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'title',
                'description',
                'company_id',
            ],
        ]);
    }
}
