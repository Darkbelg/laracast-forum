<?php

namespace App\Policies;

use App\Reply;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

//check authserviceprovider to add the policy

class ReplyPolicy
{
    use HandlesAuthorization;

    public function update(User $user,Reply $reply)
    {
        return $reply->user_id == $user->id;
    }
}
