<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\NotifyUserAndAdminNewRegistration;

class NotifyUsersAboutRegistration
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event): void
    {
        NotifyUserAndAdminNewRegistration::dispatch($event->user);
    }
}
