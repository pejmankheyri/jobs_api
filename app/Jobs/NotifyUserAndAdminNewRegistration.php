<?php

namespace App\Jobs;

use App\Mail\UserRegisteredForAdmin;
use App\Mail\UserRegisteredForUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserAndAdminNewRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $admin;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $admin)
    {
        $this->user = $user;
        $this->admin = $admin;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ThrottledMail::dispatch(new UserRegisteredForUser($this->user), $this->user);

        ThrottledMail::dispatch(new UserRegisteredForAdmin($this->user), $this->admin);

    }
}
