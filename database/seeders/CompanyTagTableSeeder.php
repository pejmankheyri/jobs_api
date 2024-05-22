<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanyTagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tagCount = Tag::all()->count();

        if ($tagCount === 0) {
            $this->command->info('No tags found, skipping assigning tags to companies');
        }

        $howManyMin = (int) $this->command->ask('Minimum tags on company?', 0);
        $howManyMax = min((int) $this->command->ask('Maximum tags on company?', $tagCount), $tagCount);

        Company::all()->each(function ($campany) use ($howManyMin, $howManyMax) {
            $tagIds = Tag::inRandomOrder()->limit(rand($howManyMin, $howManyMax))->pluck('id');
            $campany->tags()->sync($tagIds);
        });
    }
}
