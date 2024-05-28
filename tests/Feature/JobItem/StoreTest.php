<?php

namespace Tests\Feature\JobItem;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if admin can create job item
     */
    public function test_admin_can_create_job_item_with_correct_response(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');
        $company = $this->createCompany($admin);

        // Act
        $response = $this->actingAs($admin, 'sanctum')->postJson(route('api.v1.jobs.store'), [
            'title' => 'Job Title',
            'description' => 'Job Description',
            'tags' => ['tag1','tag2'],
            'company_id' => $company->id,
        ]);

        // Assertions
        $response->assertStatus(201);
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

    public function test_admin_can_not_create_job_item_with_invalid_data(): void
    {
        // Arrange
        $admin = $this->createUserWithRole('admin');

        // Act
        $response = $this->actingAs($admin, 'sanctum')->postJson(route('api.v1.jobs.store'), []);

        // Assertions
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'title',
            'description',
            'tags',
            'company_id',
        ]);
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
