<?php

namespace Tests;

use App\Models\Company;
use App\Models\JobItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createJobItem($company)
    {
        $jobItem = JobItem::factory()->make();
        $jobItem->company_id = $company->id;
        $jobItem->save();

        return $jobItem;
    }

    protected function createCompany($admin)
    {
        $company = Company::factory()->make();
        $company->user_id = $admin->id;
        $company->save();

        return $company;
    }

    protected function createUserWithRole($role): User
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
