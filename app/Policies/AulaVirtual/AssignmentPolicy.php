<?php

namespace App\Policies\AulaVirtual;

use App\Models\AulaVirtual\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Constants\Roles;
use App\Models\AulaVirtual\Classroom;

class AssignmentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if($user->role === Roles::ADMIN){
            return true;
        }
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Classroom $classroom): Response
    {
        return $classroom->members()->wherePivot('user_id',$user->id)->exists()
             ? Response::allow()
             : Response::deny('No tiene accesso a esta aula.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assignment $assignment): Response
    {
        return $user->id === $assignment->classroom->user_id
            || $assignment->classroom->members->contains($user)
            ?  Response::allow()
            :  Response::deny('No tienes los permisos para realizar esta acción');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Classroom $classroom): Response
    {
        return $user->role === Roles::PROFESSOR
            && $classroom->user_id === $user->id
            ? Response::allow()
            : Response::deny('No tienes los permisos para realizar esta acción.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assignment $assignment): Response
    {
        return $user->role === Roles::PROFESSOR 
            && $assignment->classroom->user_id === $user->id 
            ?  Response::allow()
            :  Response::deny('No tienes los permisos para realizar esta acción.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assignment $assignment): Response
    {
        return $user->role === Roles::PROFESSOR 
            && $user->id === $assignment->classroom->user_id
            ?  Response::allow()
            :  Response::deny('No tiene los permisos para realizar esta acción.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assignment $assignment): bool
    {
        return false;
    }
}
