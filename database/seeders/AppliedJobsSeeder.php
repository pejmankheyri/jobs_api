<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppliedJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobItems = \App\Models\JobItem::all();
        $users = \App\Models\User::all();

        $jobItems->each(function ($jobItem) use ($users) {
            $jobItem->users()->attach($users->random(5), ['message' => 'I am interested in this job'.$jobItem->id]);
        });
    }
}
