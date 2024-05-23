<?php

namespace App\Policies;

use App\Models\JobItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobItem $jobItem): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobItem $jobItem): Response
    {
        return $user->id === $jobItem->user_id
            ? Response::allow()
            : Response::deny('You do not own this job item.');
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobItem $jobItem): Response
    {
        return $user->id === $jobItem->user_id
            ? Response::allow()
            : Response::deny('You do not own this job item.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobItem $jobItem): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobItem $jobItem): bool
    {
        return false;
    }
}