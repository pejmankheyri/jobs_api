<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobItemTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagCount = Tag::all()->count();

        if ($tagCount === 0) {
            $this->command->info('No tags found, skipping assigning tags to job items');
        }

        $howManyMin = (int) $this->command->ask('Minimum tags on job item?', 0);
        $howManyMax = min((int) $this->command->ask('Maximum tags on job item?', $tagCount), $tagCount);

        \App\Models\JobItem::all()->each(function ($jobItem) use ($howManyMin, $howManyMax) {
            $tagIds = Tag::inRandomOrder()->limit(rand($howManyMin, $howManyMax))->pluck('id');
            $jobItem->tags()->sync($tagIds);
        });
    }
}
