<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobItem;
use Illuminate\Database\Seeder;

class JobItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobCount = (int) $this->command->ask(__('message.how_many_jobs'), 100);
        $company = Company::all();

        JobItem::factory($jobCount)->make()->each(function ($jobItem) use ($company) {
            $jobItem->company_id = $company->random()->id;
            $jobItem->save();
        });
    }
}
