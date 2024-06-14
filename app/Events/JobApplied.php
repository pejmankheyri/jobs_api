<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobApplied
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobItem;

    public $user;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($jobItem, $user, $message)
    {
        $this->jobItem = $jobItem;
        $this->user = $user;
        $this->message = $message;
    }
}
