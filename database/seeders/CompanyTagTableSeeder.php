<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Tag;
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
            $this->command->info(__('message.no_tags_found'));
        }

        $howManyMin = (int) $this->command->ask(__('message.minimum_tags_on_company'), 0);
        $howManyMax = min((int) $this->command->ask(__('message.maximum_tags_on_company'), $tagCount), $tagCount);

        Company::all()->each(function ($campany) use ($howManyMin, $howManyMax) {
            $tagIds = Tag::inRandomOrder()->limit(rand($howManyMin, $howManyMax))->pluck('id');
            $campany->tags()->sync($tagIds);
        });
    }
}
