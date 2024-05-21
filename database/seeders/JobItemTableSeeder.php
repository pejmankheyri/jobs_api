<?php

namespace Database\Seeders;

use App\Models\JobItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobCount = (int)$this->command->ask('How many jobs would you like?', 50);
        $users = User::all();

        JobItem::factory($jobCount)->make()->each(function($jobItem) use ($users){
            $jobItem->user_id = $users->random()->id;
            $jobItem->save();
        });
    }
}
