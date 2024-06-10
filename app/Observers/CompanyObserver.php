<?php

namespace App\Observers;

use App\Models\Company;
use Illuminate\Support\Facades\File;

class CompanyObserver
{
    /**
     * Handle the Company "deleted" event.
     */
    public function deleting(Company $company): void
    {
        $company->images->each->delete();
        $company->location->each->delete();
        $company->jobItem->each->delete();

        $company->tags()->detach();

        $directory = public_path('images/companies/'.$company->id);
        if (File::exists($directory)) {
            File::deleteDirectory($directory);
        }
    }
}
