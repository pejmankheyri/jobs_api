<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
    public function show($id)
    {
        $user = User::findOrFail($id);
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
        $user->email = $validated['email'];
        $user->password = $validated['password'];

        $user->save();

        // update role
        $user->roles()->attach($validated['role_id']);
        return new UserResource($user);

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
}
