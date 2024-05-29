<?php

namespace Tests\Feature\Company;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_companies_list_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($admin, 'sanctum')->getJson(route('api.v1.companies.index'));

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
                    ],
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                    ],
                    'jobs' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'create_dates' => [
                                'created_at_human',
                                'created_at'
                            ],
                            'update_dates' => [
                                'updated_at_human',
                                'updated_at'
                            ],
                        ]
                    ],
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
                    'location' => [
                        'id',
                        'address',
                        'city',
                        'state',
                        'country',
                        'zip_code',
                    ]
                ]
            ]
        ]);
    }
}
