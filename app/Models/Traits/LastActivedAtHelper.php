<?php


namespace App\Models\Traits;


use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

trait LastActivedAtHelper
{
    // 缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        // 缓存哈希表名
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        // 字段名称 user_1
        $field = $this->getHashField();

        // 获取当前时间
        $now = Carbon::now()->toDateTimeString();

        // 写入缓存，如果字段存在会被更新
        Redis::hSet($hash, $field, $now);
    }

    public function getLastActivedAtAttribute($value)
    {
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        $field = $this->getHashField();

        $datetime = Redis::hGet($hash, $field) ? : $value;

        // 如果存在的话，返回时间对应的 Carbon 实体
        if ($datetime) {
            return new Carbon($datetime);
        } else {
            // 使用注册时间
            return $this->created_at;
        }
    }

    public function syncUserActivedAt()
    {
        // 昨天的哈希
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        // 从缓存中获取所有数据
        $dates = Redis::hGetAll($hash);

        foreach ($dates as $user_id => $date) {
            // 将user_id转换为id
            $user_id = str_replace($this->field_prefix, '', $user_id);
            // 只有用户存在时
            if ($user = $this->find($user_id)) {
                // 将时间同步到数据库
                $user->last_actived_at = $date;
                $user->save();
            }
        }
        // 删除昨天的缓存
        Redis::del($hash);
    }

    public function getHashFromDateString($date)
    {
        // Redis 哈希表的命名，如：larabbs_last_actived_at_2017-10-21
        return $this->hash_prefix . $date;
    }

    public function getHashField()
    {
        // 字段名称，如：user_1
        return $this->field_prefix . $this->id;
    }

}
