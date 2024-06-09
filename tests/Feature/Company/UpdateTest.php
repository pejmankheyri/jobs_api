<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_company_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->putJson(route('api.v1.companies.update', $company->id), [
            'title' => 'New title',
            'description' => 'New description',
            'rating' => 5,
            'website' => 'https://new-website.com',
            'employes' => 100,
        ]);

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'rating',
                'website',
                'employes',
                'create_dates',
                'update_dates',
                'jobs' => [
                    '*' => [
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
                            'create_dates',
                            'update_dates',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_admin_can_not_update_company_with_invalid_data(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->putJson(route('api.v1.companies.update', $company->id), [
            'title' => '',
            'description' => '',
            'rating' => 0,
            'website' => '',
            'employes' => 0,
        ]);

        // Assertions
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'description', 'rating', 'website', 'employes']);
    }
}
