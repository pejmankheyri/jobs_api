<?php

namespace App\Jobs;

use App\Mail\JobAppliedForCompany;
use App\Mail\JobAppliedForUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyUserAndCompanyJobApplied implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobItem;

    public $user;

    /**
     * Create a new job instance.
     */
    public function __construct($jobItem, $user)
    {
        $this->jobItem = $jobItem;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->user;
        $company = $this->jobItem->company;

        // Notify the user
        // Mail::to($user->email)->send(new JobAppliedForUser($this->jobItem, $user));

        ThrottledMail::dispatch(new JobAppliedForUser($this->jobItem, $user), $user);

        // Notify the company
        // Mail::to($company->user->email)->send(new JobAppliedForCompany($this->jobItem, $user));

        ThrottledMail::dispatch(new JobAppliedForCompany($this->jobItem, $user), $company->user);

    }
}
