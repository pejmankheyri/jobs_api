<?php

namespace App\Listeners;

use App\Events\JobApplied;
use App\Jobs\NotifyUserAndCompanyJobApplied;

class NotifyUsersAboutJobApplication
{
    /**
     * Handle the event.
     */
    public function handle(JobApplied $event): void
    {
        NotifyUserAndCompanyJobApplied::dispatch($event->jobItem, $event->user, $event->message);

    }
}
