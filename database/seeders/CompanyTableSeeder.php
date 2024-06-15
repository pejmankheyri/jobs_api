<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CompanyTableSeeder extends Seeder
{
    public $images = [
        'https://images.pexels.com/photos/380768/pexels-photo-380768.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'https://images.pexels.com/photos/245240/pexels-photo-245240.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'https://images.pexels.com/photos/380769/pexels-photo-380769.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'https://images.pexels.com/photos/37347/office-sitting-room-executive-sitting.jpg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'https://images.pexels.com/photos/1595385/pexels-photo-1595385.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'https://images.pexels.com/photos/936722/pexels-photo-936722.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
        'https://images.pexels.com/photos/3184357/pexels-photo-3184357.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1',
    ];

    public $logos = [
        'https://www.zarla.com/images/apple-logo-2400x2400-20220512-1.png',
        'https://www.zarla.com/images/twitter-logo-2400x2400-20220512.png',
        'https://www.zarla.com/images/facebook-logo-2400x2400-20220518-2.png',
        'https://www.zarla.com/images/fedex-logo-2400x2400-20223105.png',
        'https://www.zarla.com/images/coca-cola-logo-2400x2400-20220513.png',
        'https://www.zarla.com/images/mcdonalds-logo-2400x2400-20220513-1.png',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyCount = (int) $this->command->ask(__('message.how_many_companies'), 50);
        $users = User::all();

        Storage::disk('public')->deleteDirectory('logos');
        Storage::disk('public')->deleteDirectory('images');

        Company::factory($companyCount)->make()->each(function ($company) use ($users) {
            $company->user_id = $users->random()->id;
            $company->save();

            $logoPath = $this->generateRandomFile('logos', 'jpg', $this->logos[array_rand($this->logos)]);

            $company->update([
                'logo' => $logoPath,
            ]);

            for ($i = 0; $i < 3; $i++) {
                $company->images()->create([
                    'path' => $this->generateRandomFile('images/company/'.$company->id, 'jpg', $this->images[array_rand($this->images)]),
                ]);
            }

        });
    }

    private function generateRandomFile($folder, $extension, $url)
    {
        // Create a dummy file in the specified folder and return the path
        $fileName = uniqid().'.'.$extension;
        Storage::disk('public')->put($folder.'/'.$fileName, file_get_contents($url));

        return $folder.'/'.$fileName;
    }
}
