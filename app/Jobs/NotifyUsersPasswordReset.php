<?php

namespace App\Jobs;

use App\Mail\PasswordReset;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NotifyUsersPasswordReset implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $email;

    public $password;

    public $token;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $password, $token)
    {
        $this->email = $email;
        $this->password = $password;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $passwordReset = DB::table('password_resets')
            ->where('token', $this->token)
            ->where('email', $this->email)
            ->first();

        if (! $passwordReset) {
            return response()->json(['message' => __('auth.invalid_token_or_email')], 400);
        }

        $user = User::where('email', $this->email)->first();

        if (! $user) {
            return response()->json(['message' => __('auth.no_user_found_with_email')], 404);
        }

        $user->password = Hash::make($this->password);
        $user->save();

        DB::table('password_resets')->where('email', $this->email)->delete();

        ThrottledMail::dispatch(new PasswordReset(), $user);
    }
}
