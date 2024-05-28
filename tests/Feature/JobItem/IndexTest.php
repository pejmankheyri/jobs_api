<?php

namespace Tests\Feature\JobItem;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test if admin can see job items list
     */
    public function test_admin_can_see_job_items_list_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($admin, 'sanctum')->getJson(route('api.v1.jobs.index'));

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
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
                        'create_dates' => [
                            'created_at_human',
                            'created_at'
                        ],
                        'update_dates' => [
                            'updated_at_human',
                            'updated_at'
                        ]
                    ],
                ]
            ]
        ]);
    }

    public function test_other_users_can_see_job_items_list_with_correct_response(): void
    {
        // Arrange
        $user = $this->createUserWithRole('user');

        // Act
        $response = $this->actingAs($user, 'sanctum')->getJson(route('api.v1.jobs.index'));

        // Assertions
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
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
                        'create_dates' => [
                            'created_at_human',
                            'created_at'
                        ],
                        'update_dates' => [
                            'updated_at_human',
                            'updated_at'
                        ]
                    ],
                ]
            ]
        ]);
    }

    private function createUserWithRole($role): User
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        $user = User::factory()->create();

        $userRole = Role::where('name', $role)->first();

        $user->roles()->attach($userRole);

        return $user;
    }
}
