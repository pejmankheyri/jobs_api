<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePassRequest;
use App\Http\Requests\User\StoreAvatarRequest;
use App\Http\Requests\User\StoreCvRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\JobItemResource;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->roles->first()->name !== 'admin') {
            return response()->json([
                'message' => __('message.unauthorized')
            ], 401);
        }
        $users = User::orderByIdDesc();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        $IsNotAdmin = Role::findOrFail($validated['role_id'])->name !== 'admin' ;

        if ($IsNotAdmin) {
            $user = new User();
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->phone = $validated['phone'];
            $user->password = $validated['password'];

            $user->save();

            // save role
            $user->roles()->attach($validated['role_id']);
            return new UserResource($user);

        } else {
            return response()->json([
                'message' => __('message.role_not_found')
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {

        $user = Auth::user();
        if (Auth::user()->roles->first()->name !== 'admin') {
            Gate::authorize('view', $user);
        }
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);

        if (Auth::user()->roles->first()->name !== 'admin') {
            Gate::authorize('update', $user);
        }

        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];

        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('delete', $user);
        $user->delete();
        return response()->json([
            'message' => __('message.user_deleted', [
                'email' => $user->email,
                'id' => $user->id
            ])
        ]);
    }

    public function jobs(Request $request, $id)
    {
        $user = User::findOrFail($id);
        Gate::authorize('jobs', $user);

        $perPage = $request->query('per_page', 10);

        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'desc');
        $search = $request->query('q', '');

        if (!in_array($order, ['asc', 'desc'])) {
            return response()->json(['message' => 'Invalid sort parameter'], 400);
        }

        $jobs = $user->jobs()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->orderBy($sort, $order)
            ->paginate($perPage);

        return JobItemResource::collection($jobs);
    }

    public function uploadAvatar(StoreAvatarRequest $request)
    {
        $validated = $request->validated();

        $user = Auth::user();

        Gate::authorize('uploadAvatar', $user);

        // Handle the user upload of avatar
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
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

        if (!$user->avatar) {
            return response()->json(['message' => 'No avatar found'], 404);
        }

        return response()->json(['avatar' => Storage::url($user->avatar)], 200);
    }

    public function changePassword(ChangePassRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        Gate::authorize('changePass', $user);

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.']
            ]);
        }

        $user->password = Hash::make($request['new_password']);
        $user->save();

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function uploadCV(StoreCvRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        Gate::authorize('uploadCV', $user);

        if ($request->hasFile('cv')) {
            $cv = $request->file('cv');
            $filename = time() . '.' . $cv->getClientOriginalExtension();
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
