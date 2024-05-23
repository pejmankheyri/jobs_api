<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        if ($this->command->confirm('Do you want to refresh the database?')) {
            $this->command->call('migrate:refresh');
            $this->command->info('Database was refreshed');
        }

        $this->call([
            UserTableSeeder::class,
            CompanyTableSeeder::class,
            JobItemTableSeeder::class,
            TagsTableSeeder::class,
            JobItemTagTableSeeder::class,
            CompanyTagTableSeeder::class,
            CompanyLocationTableSeeder::class,
        ]);
    }
}
