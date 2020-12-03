<?php

namespace App\Observers;

use App\Models\UsersRelation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UsersRelationObserver
{
    public function creating(UsersRelation $users_relation)
    {
        //
    }

    public function created(UsersRelation $usersRelation)
    {

    }

    public function updating(UsersRelation $users_relation)
    {

    }

    public function deleted(UsersRelation $users_relation)
    {

    }
}
