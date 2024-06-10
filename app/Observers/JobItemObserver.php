<?php

namespace App\Observers;

use App\Models\JobItem;
use Illuminate\Support\Facades\Cache;

class JobItemObserver
{
    /**
     * Handle the JobItem "updated" event.
     */
    public function updating(JobItem $jobItem): void
    {
        Cache::forget("jobs-{$jobItem->id}");
    }

    /**
     * Handle the JobItem "deleted" event.
     */
    public function deleting(JobItem $jobItem): void
    {
        $jobItem->tags()->detach();

        Cache::forget('jobs');
    }
}
