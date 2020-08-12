<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the profile.
     *
     * @param  \App\User  $signedInUser
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $signedInUser, User $user)
    {
        //owns could also be possible
        return $signedInUser->id === $user->id;
    }
}
