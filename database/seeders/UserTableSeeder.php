<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersCount = max((int)$this->command->ask(__('message.how_many_users'), 10), 1);

        User::factory()->state([
            'name' => 'Pejman',
            'email' => 'pejman@gmail.com',
        ])->create();

        User::factory($usersCount)->create();
    }
}
