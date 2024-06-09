<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = collect(['PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React.js', 'Node.js', 'Python', 'Java', 'Swift', 'TypeScript', 'HTML', 'CSS']);

        $tags->each(function ($tag) {
            Tag::create([
                'name' => $tag,
            ]);
        });
    }
}
