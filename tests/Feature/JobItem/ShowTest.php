<?php

namespace Tests\Feature\JobItem;

use App\Models\Company;
use App\Models\JobItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if admin can see job item
     */
    public function test_admin_can_see_job_item_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);
        $jobItem = $this->createJobItem($company);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->getJson(route('api.v1.jobs.show', $jobItem->id));

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
                            'created_at'
                        ],
                        'update_dates' => [
                            'updated_at_human',
                            'updated_at'
                        ]
                    ]
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
                        'created_at'
                    ],
                    'update_dates' => [
                        'updated_at_human',
                        'updated_at'
                    ]
                ]
            ]
        ]);
    }

    public function test_other_users_can_see_job_item_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);
        $jobItem = $this->createJobItem($company);
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson(route('api.v1.jobs.show', $jobItem->id));

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
                            'created_at'
                        ],
                        'update_dates' => [
                            'updated_at_human',
                            'updated_at'
                        ]
                    ]
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
                        'created_at'
                    ],
                    'update_dates' => [
                        'updated_at_human',
                        'updated_at'
                    ]
                ]
            ]
        ]);
    }
}
