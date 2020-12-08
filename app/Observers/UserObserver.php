<?php

namespace App\Observers;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        //
    }

    public function updating(User $user)
    {
        //
    }

    public function deleted(User $user)
    {
        // 删除用户所发表的话题
        // 删除用户话题下的回复
        // 删除用户发表的回复
        $topics = DB::table('topics')->where('user_id', $user->id)->pluck('id')->toArray();
        DB::table('replies')->whereIn('topic_id', $topics)->orWhere('user_id', $user->id)->delete();
        DB::table('topics')->where('user_id', $user->id)->delete();
    }

    public function saving(User $user)
    {
        if (empty($user->avatar)) {
            $user->avatar = '202011/26/1_1606367737_kfHwT1SVEY.jpg';
        }
        if (empty($user->introduction)) {
            $user->introduction = '上善若水,水善利万物而不争,处众人之所恶,故几于道';
        }
    }
}
