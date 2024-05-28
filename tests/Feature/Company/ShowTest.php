<?php

namespace Tests\Feature\Company;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_company_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->getJson(route('api.v1.companies.show', $company->id));

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
                            'create_dates',
                            'update_dates'
                        ]
                    ]
                ]
            ]
        ]);
    }
}
