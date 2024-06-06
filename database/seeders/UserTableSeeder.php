<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            'phone' => '1234567890',
            'password' => Hash::make('password'),
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $admin->roles()->attach($adminRole);

        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'company']);

        // remove all files from storage
        Storage::disk('public')->deleteDirectory('avatars');
        Storage::disk('public')->deleteDirectory('cvs');


        // Create 10 users and assign roles
        User::factory($usersCount)->create()->each(function ($user) {
            $user->roles()->attach(Role::where('name', 'company')->first());

            $avatarPath = $this->generateRandomFile('avatars', 'jpg', 'https://avatars.githubusercontent.com/u/3329008?v=4');
            $cvPath = $this->generateRandomFile('cvs', 'pdf', 'https://www.sbs.ox.ac.uk/sites/default/files/2019-01/cv-template.pdf');

            $user->update([
                'avatar' => $avatarPath,
                'cv' => $cvPath,
            ]);
        });

    }
    private function generateRandomFile($folder, $extension, $url)
    {
        // Create a dummy file in the specified folder and return the path
        $fileName = uniqid() . '.' . $extension;
        Storage::disk('public')->put($folder . '/' . $fileName, file_get_contents($url));

        return $folder . '/' . $fileName;
    }
}
