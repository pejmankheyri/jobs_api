<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordLinkSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $email;

    public $locale;

    /**
     * Create a new event instance.
     */
    public function __construct($email, $locale)
    {
        $this->email = $email;
        $this->locale = $locale;
    }
}
