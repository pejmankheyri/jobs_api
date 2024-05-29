<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersCount = max((int)$this->command->ask(__('message.how_many_users'), 10), 1);

        // $admin = User::factory()->state([
        //     'name' => 'Pejman',
        //     'email' => 'pejman@gmail.com',
        // ])->create();

        // Create a user and assign the admin role
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole);

        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        // Create 10 users and assign roles
        User::factory($usersCount)->create()->each(function ($user) {
            $user->roles()->attach(Role::where('name', 'company')->first());
        });

    }
}
