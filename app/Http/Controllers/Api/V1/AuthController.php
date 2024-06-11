<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token, 'user' => new UserResource($user)], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => __('auth.logged_out')], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreRequest $request)
    {
        $validated = $request->validated();

        $IsNotAdmin = Role::findOrFail($validated['role_id'])->name !== 'admin';

        if ($IsNotAdmin) {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->password = $validated['password'];

            $user->save();

            // save role
            $user->roles()->attach($validated['role_id']);

            $admin = Role::where('name', 'admin')->first()->users->first();

            event(new UserRegistered($user, $admin));

            return new UserResource($user);

        } else {
            return response()->json([
                'message' => __('message.role_not_found'),
            ], 404);
        }
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $token = Str::random(60);
        $email = $request->email;
        $locale = $request->locale ?? 'en';

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $localeVar = $request->locale === 'en' ? '' : '/'.$locale;

        $resetLink = url(config('app.frontend_url').$localeVar.'/reset-password?token='.$token.'&email='.urlencode($email));

        Mail::send('emails.password_reset', ['resetLink' => $resetLink], function ($message) use ($email) {
            $message->to($email)->subject('Reset Password');
        });

        return response()->json(['message' => 'Password reset link sent.'], 200);
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $passwordReset = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (! $passwordReset) {
            return response()->json(['message' => 'Invalid token or email.'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['message' => 'No user found with this email.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset.'], 200);
    }
}
