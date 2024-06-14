<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\JobItemResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->roles->first()->name !== 'admin') {
            return response()->json([
                'message' => __('message.unauthorized'),
            ], 401);
        }
        $users = User::orderByIdDesc();

        return UserResource::collection($users);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user();
        $userRole = Auth::user()->roles->first()->name;
        if ($userRole !== 'admin') {
            Gate::authorize('view', $user);
        }

        switch ($userRole) {
            case 'admin':
                return new UserResource($user);
                break;
            case 'company':
                return new UserResource($user->load(['companies']));
                break;
            case 'user':
                return new UserResource($user);
                break;
            default:
                return response()->json([
                    'message' => __('message.role_not_found'),
                ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request)
    {
        $user = Auth::user();

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
                'id' => $user->id,
            ]),
        ]);
    }

    public function jobs(Request $request)
    {
        $user = Auth::user();
        Gate::authorize('jobs', $user);

        $jobs = User::appledJobs($request);

        return JobItemResource::collection($jobs);
    }

    public function companies(Request $request)
    {
        $user = Auth::user();
        Gate::authorize('companies', $user);

        $companies = User::appledCompanies($request);

        return CompanyResource::collection($companies);
    }
}
