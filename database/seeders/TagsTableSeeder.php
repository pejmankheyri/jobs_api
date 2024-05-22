<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = collect(['PHP', 'Laravel', 'JavaScript', 'Vue.js', 'React.js', 'Angular.js', 'Node.js', 'Python', 'Ruby', 'Java', 'C#', 'C++', 'C', 'Swift', 'Kotlin', 'Go', 'Rust', 'Dart', 'TypeScript', 'HTML', 'CSS', 'Sass', 'Less' ]);

        $tags->each(function ($tag) {
            Tag::create([
                'name' => $tag,
            ]);
        });
    }
}
