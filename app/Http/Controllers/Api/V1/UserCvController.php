<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCvRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class UserCvController extends Controller
{
    public function uploadCV(StoreCvRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();
        Gate::authorize('uploadCV', $user);

        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $filename = time().'.'.$cv->getClientOriginalExtension();
            $path = $cv->storeAs('cvs', $filename, 'public');

            // Delete old cv if exists
            if ($user->cv) {
                Storage::disk('public')->delete($user->cv);
            }

            // Update user's cv path
            $user->cv = $path;
            $user->save();
        }

        return response()->json(['message' => 'CV uploaded successfully', 'cv' => Storage::url($user->cv)], 200);
    }
}
