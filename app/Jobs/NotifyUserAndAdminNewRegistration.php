<?php

namespace App\Jobs;

use App\Mail\UserRegisteredForAdmin;
use App\Mail\UserRegisteredForUser;
use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserAndAdminNewRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->user;

        ThrottledMail::dispatch(new UserRegisteredForUser($user), $user);

        $admin = Role::where('name', 'admin')->first()->users->first();

        ThrottledMail::dispatch(new UserRegisteredForAdmin($user), $admin);

    }
}
