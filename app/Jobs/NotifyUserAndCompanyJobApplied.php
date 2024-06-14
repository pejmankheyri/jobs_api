<?php

namespace App\Jobs;

use App\Mail\JobAppliedForCompany;
use App\Mail\JobAppliedForUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserAndCompanyJobApplied implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobItem;

    public $user;

    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct($jobItem, $user, $message)
    {
        $this->jobItem = $jobItem;
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $user = $this->user;
        $company = $this->jobItem->company;

        if ($this->jobItem->users()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => __('message.already_applied')], 400);
        }

        $this->jobItem->users()->attach($user->id);
        $this->jobItem->users()->updateExistingPivot($user->id, ['message' => $this->message]);

        // Notify the user
        ThrottledMail::dispatch(new JobAppliedForUser($this->jobItem, $user), $user);

        // Notify the company
        ThrottledMail::dispatch(new JobAppliedForCompany($this->jobItem, $user), $company->user);

    }
}
