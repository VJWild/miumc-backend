<?php

namespace App\Policies\AulaVirtual;

use App\Constants\Roles;
use App\Models\AulaVirtual\Post;
use App\Models\AulaVirtual\Classroom;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use PhpParser\Builder\Class_;

class PostPolicy
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
    public function viewAny(User $user,Classroom $classroom): Response
    {
        return $classroom->isMember($user)
            || $user->id === $classroom->user_id
             ? Response::allow()
             : Response::deny('No eres miembro de esta aula.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): Response
    {
        return $post->classroom->isMember($user)
            || $post->classroom->user_id === $user->id
             ? Response::allow()
             : Response::deny('No tienes acceso a este contenido.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user,Classroom $classroom): Response
    {
        return $classroom->isMember($user)
            || $classroom->user_id === $user->id
             ? Response::allow()
             : Response::deny('No tiene los permisos para realizar esta acción');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): Response
    {
        return $user->id === $post->user_id 
            || ($user->isProffesor() && $user->id === $post->classroom->user_id)
             ? Response::allow() 
             : Response::deny('No tienes los permisos para realizar esta acción.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): Response
    {
        return $post->user_id === $user->id
            || $post->classroom->user_id === $user->id
             ? Response::allow()
             : Response::deny('No tiene los permisos para realizar esta acción.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }
}
