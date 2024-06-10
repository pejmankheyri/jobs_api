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
use Illuminate\Support\Facades\Mail;

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

        Mail::to($user->email)->send(new UserRegisteredForUser($user));

        $admin = Role::where('name', 'admin')->first()->users->first();
        Mail::to($admin->email)->send(new UserRegisteredForAdmin($user));
    }
}
