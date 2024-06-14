<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordReset
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $email;

    public $password;

    public $token;

    /**
     * Create a new event instance.
     */
    public function __construct($email, $password, $token)
    {
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }
}
