<?php

namespace Tests\Feature\Company;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_company_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($admin, 'sanctum')->postJson(route('api.v1.companies.store'), [
            'title' => 'Company Title',
            'description' => 'Company Description',
            'website' => 'https://company.com',
            'employes' => 100,
            'rating' => 5,
        ]);

        // Assertions
        $response->assertStatus(201);
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

    public function test_admin_can_not_create_company_with_invalid_data(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($admin, 'sanctum')->postJson(route('api.v1.companies.store'), []);

        // Assertions
        $response->assertStatus(422);
    }
}
