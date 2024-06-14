<?php

namespace App\Jobs;

use App\Mail\PasswordResetLink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotifyUsersPasswordResetLink implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    public $locale;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $locale)
    {

        $this->email = $email;
        $this->locale = $locale;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = Str::random(60);

        $email = $this->email;

        $locale = $this->locale ?? 'en';
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $localeVar = $this->locale === 'en' ? '' : '/'.$locale;

        $resetLink = url(config('app.frontend_url').$localeVar.'/reset-password?token='.$token.'&email='.urlencode($email));

        $user = User::where('email', $email)->first();

        ThrottledMail::dispatch(new PasswordResetLink($resetLink), $user);

    }
}
