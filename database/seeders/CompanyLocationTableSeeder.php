<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        $companies->each(function ($company) {
            Location::factory(rand(1,3))->create([
                'company_id' => $company->id,
            ]);
        });
    }
}
