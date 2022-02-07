<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->isAccountOwner() || $user->isCapeAndBayUser() || $user->can('delete-users', $user->currentTeam()->first());
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
//    public function create(User $user, User $model)
//TODO: for some reason, adding in the $model to any of these policy functions causing the app to crash,
//TODO:  and I don't know why.  If we had access to the model, we could actually check and make sure the team
// is correct.
    public function create(User $user)
    {
        return $user->isAccountOwner() || $user->isCapeAndBayUser() || $user->can('create-new-users', $user->currentTeam()->first());
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
//    public function update(User $user, User $model)
    public function update(User $user)
    {
        return $user->isAccountOwner() || $user->isCapeAndBayUser() || $user->can('update-users', $user->currentTeam()->first());
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
//    public function delete(User $user, User $model)
    public function delete(User $user)
    {
        return $user->isAccountOwner() || $user->isCapeAndBayUser() || $user->can('delete-users', $user->currentTeam()->first());
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
//    public function restore(User $user, User $model)
    public function restore(User $user)
    {
        return $user->isAccountOwner() || $user->isCapeAndBayUser() || $user->can('delete-users', $user->currentTeam()->first());
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->isAccountOwner() || $user->isCapeAndBayUser() || $user->can('delete-users', $user->currentTeam()->first());
    }

    /**
     * Determine whether the user can upload files.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function uploadFiles(User $user)
    {
        return true;
    }
}
