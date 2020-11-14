<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'desc');
    }

}
