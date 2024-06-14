<?php

namespace App\Listeners;

use App\Events\PasswordReset;
use App\Jobs\NotifyUsersPasswordReset;

class NotifyUsersAboutPasswordReset
{
    /**
     * Handle the event.
     */
    public function handle(PasswordReset $event): void
    {
        NotifyUsersPasswordReset::dispatch($event->email, $event->password, $event->token);
    }
}
