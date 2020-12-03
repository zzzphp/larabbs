<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;

class UsersRelation extends Model
{
    protected $fillable = ['user_id', 'follower_id', 'relation_type'];


    public function isFollow($follower_id)
    {
        $follow = $this->where(['user_id' => Auth::id(), 'follower_id' => $follower_id])->first();
        if ($follow) {
            return $follow;
        }
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
