<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\PasswordReset;
use App\Events\ResetPasswordLinkSent;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\User\ChangePassRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
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

        $email = $request->email;
        $locale = $request->locale;

        event(new ResetPasswordLinkSent($email, $locale));

        return response()->json(['message' => __('auth.reset_password_link_sent')], 200);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $validated = $request->validated();

        $email = $validated['email'];
        $password = $validated['password'];
        $token = $validated['token'];

        event(new PasswordReset($email, $password, $token));

        return response()->json(['message' => __('auth.password_changed_successfully')], 200);
    }

    public function changePassword(ChangePassRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        Gate::authorize('changePass', $user);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }

        $user->password = Hash::make($request['new_password']);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }
}
