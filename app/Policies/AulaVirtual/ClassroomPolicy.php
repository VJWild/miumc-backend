<?php

namespace App\Policies\AulaVirtual;

use App\Constants\Roles;
use App\Models\AulaVirtual\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassroomPolicy
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
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Classroom $classroom): Response
    {
        return $classroom->user_id === $user->id
            || $classroom->members()->wherePivot('user_id',$user->id)->exists()
            ? Response::allow()
            : Response::deny('No tienes los permisos para acceder a este recurso.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->role === Roles::PROFESSOR
            ? Response::allow()
            : Response::deny('No tienes los permisos para realizar esta acción');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Classroom $classroom): Response
    {
        return $classroom->user_id === $user->id
            ? Response::allow()
            : Response::deny('No tienes los permisos para realizar esta acción');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Classroom $classroom): Response
    {
        return $classroom->user_id === $user->id
            ? Response::allow()
            : Response::deny('No tienes los permisos para realizar esta acción');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Classroom $classroom): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Classroom $classroom): bool
    {
        return false;
    }
}
