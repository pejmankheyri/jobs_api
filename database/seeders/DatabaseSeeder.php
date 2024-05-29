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

        if ($this->command->confirm(__('message.refresh_database'))) {
            $this->command->call('migrate:refresh');
            $this->command->info(__('message.database_refreshed'));
        }

        $this->call([
            RoleTableSeeder::class,
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
