<?php

namespace App\Policies;

use App\Http\Requests\UsersRelationRequest;
use App\Models\User;
use App\Models\UsersRelation;

class UsersRelationPolicy extends Policy
{

    public function destroy(User $user, UsersRelation $users_relation)
    {
        return $user->isAuthorOf($users_relation);
    }

    public function create(User $user, UsersRelation $usersRelation)
    {
        return $user->id != $usersRelation->follower_id;
    }

}
