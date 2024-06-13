<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CompanyTableSeeder extends Seeder
{
    public $image = 'https://banner2.cleanpng.com/20180423/gkw/kisspng-google-logo-logo-logo-5ade7dc753b015.9317679115245306313428.jpg';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyCount = (int) $this->command->ask(__('message.how_many_companies'), 50);
        $users = User::all();

        Storage::disk('public')->deleteDirectory('logos');

        Company::factory($companyCount)->make()->each(function ($comapny) use ($users) {
            $comapny->user_id = $users->random()->id;
            $comapny->save();

            $logoPath = $this->generateRandomFile('logos', 'jpg', $this->image);

            $comapny->update([
                'logo' => $logoPath,
            ]);

            $comapny->images()->create([
                'path' => $this->generateRandomFile('images', 'jpg', $this->image),
            ]);
            $comapny->images()->create([
                'path' => $this->generateRandomFile('images', 'jpg', $this->image),
            ]);
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
