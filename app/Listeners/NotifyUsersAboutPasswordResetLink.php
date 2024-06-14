<?php

namespace App\Listeners;

use App\Events\ResetPasswordLinkSent;
use App\Jobs\NotifyUsersPasswordResetLink;

class NotifyUsersAboutPasswordResetLink
{
    /**
     * Handle the event.
     */
    public function handle(ResetPasswordLinkSent $event): void
    {
        NotifyUsersPasswordResetLink::dispatch($event->email, $event->locale);
    }
}
