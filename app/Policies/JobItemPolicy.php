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
        return $user->id === $jobItem->company->user_id
            ? Response::allow()
            : Response::deny(__('message.you_not_own_this_job_item'));
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobItem $jobItem): Response
    {
        return $user->id === $jobItem->company->user_id
            ? Response::allow()
            : Response::deny(__('message.you_not_own_this_job_item'));
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

    public function apply(User $user, JobItem $jobItem): bool
    {
        return $user->id !== $jobItem->company->user_id;
    }

    public function viewApplicants(User $user, JobItem $jobItem): bool
    {
        return $user->id === $jobItem->company->user_id;
    }
}
