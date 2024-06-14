<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreAvatarRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class UserAvatarController extends Controller
{
    public function uploadAvatar(StoreAvatarRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        Gate::authorize('uploadAvatar', $user);

        // Handle the user upload of avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time().'.'.$avatar->getClientOriginalExtension();
            $path = $avatar->storeAs('avatars', $filename, 'public');

            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Update user's avatar path
            $user->avatar = $path;
            $user->save();
        }

        return response()->json(['message' => 'Avatar uploaded successfully', 'avatar' => Storage::url($user->avatar)], 200);
    }

    public function getAvatar($id)
    {
        $user = User::findOrFail($id);

        if (! $user->avatar) {
            return response()->json(['message' => 'No avatar found'], 404);
        }

        return response()->json(['avatar' => Storage::url($user->avatar)], 200);
    }
}
