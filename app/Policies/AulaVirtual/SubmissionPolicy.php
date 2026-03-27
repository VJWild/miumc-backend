<?php

namespace App\Policies\AulaVirtual;

use App\Constants\Roles;
use App\Models\AulaVirtual\Assignment;
use App\Models\AulaVirtual\Submission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmissionPolicy
{
    public function before(User $user,string $ability) : bool
    {
        return $user->role === Roles::ADMIN
             ? true
             : null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user,Assignment $assignment): Response
    {
        return $user->role === Roles::PROFESSOR
            && $assignment->classroom->user_id === $user->id
            ?  Response::allow()
            :  Response::deny('No tienes los permisos para realizar esta acción.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Submission $submission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user , Assignment $assignment): bool
    {
        return $assignment->classroom()->members()->where('user_id',$user->id)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Submission $submission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Submission $submission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Submission $submission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Submission $submission): bool
    {
        return false;
    }
}
