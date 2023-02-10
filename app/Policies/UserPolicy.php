<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Support\CurrentInfoRetriever;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     */
    public function viewAny(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->isAccountOwner() || $user->isAdmin() || $user->can('users.view', User::class);
    }

    /**
     * Determine whether the user can view the model.
     *
     */
    public function view(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     */
//    public function create(User $user, User $model)
    //TODO: for some reason, adding in the $model to any of these policy functions causing the app to crash,
    //TODO:  and I don't know why.  If we had access to the model, we could actually check and make sure the team
    // is correct.
    public function create(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->isAccountOwner() || $user->isAdmin() || $user->can('users.create', User::class);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Users\Models\User $model
     */
//    public function update(User $user, User $model)
    public function update(User $user): \Illuminate\Auth\Access\Response|bool
    {
        $team = CurrentInfoRetriever::getCurrentTeam();

        return $user->isAccountOwner() || $user->isAdmin() || $user->can('users.update', $team);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Users\Models\User $model
     */
//    public function delete(User $user, User $model)
    public function delete(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->isAccountOwner() || $user->isAdmin() || $user->can('users.trash', User::class);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Domain\Users\Models\User $model
     */
//    public function restore(User $user, User $model)
    public function restore(User $user): \Illuminate\Auth\Access\Response|bool
    {
        return $user->isAccountOwner() || $user->isAdmin() || $user->can('users.restore', User::class);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     */
    public function forceDelete(User $user, User $model): \Illuminate\Auth\Access\Response|bool
    {
        return $user->isAccountOwner() || $user->isAdmin() || $user->can('users.delete', User::class);
    }

    /**
     * Determine whether the user can upload files.
     *
     */
    public function uploadFiles(User $user): mixed
    {
        return true;
    }
}
