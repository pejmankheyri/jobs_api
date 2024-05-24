<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyCount = (int)$this->command->ask(__('message.how_many_companies'), 10);
        $users = User::all();

        Company::factory($companyCount)->make()->each(function($comapny) use ($users){
            $comapny->user_id = $users->random()->id;
            $comapny->save();
        });

    }
}
