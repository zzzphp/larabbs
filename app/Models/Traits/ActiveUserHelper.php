<?php

namespace App\Models\Traits;



use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ActiveUserHelper
{
    // 用户存放临时用户数据
    protected $users = [];

    // 配置信息
    protected $topic_weight = 4; // 话题权重
    protected $reply_weight = 1; // 回复权重
    protected $pass_days = 7; // 多少天内发表过内容
    protected $user_number = 6; // 取出来多少用户

    // 缓存相关配置
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65 * 60;

    public function getActiveUsers()
    {
        // 尝试从缓存中取出cache_key 对应的数据。如果能取到，直接返回数据
        // 否则运行匿名函数中的代码来取出活跃用户数据，返回的同时做缓存
//        Cache::pull($this->cache_key);
        return Cache::remember($this->cache_key, $this->cache_expire_in_seconds, function (){
            return $this->calculateAndCacheActiveUsers();
        });

    }

    public function calculateAndCacheActiveUsers()
    {
        $active_users = $this->calculateActiveUsers();

        $this->cacheActiveUsers($active_users);

        return $active_users;
    }

    private function calculateActiveUsers()
    {
        // 取得活跃用户列表
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        // 数组按照分数排序
        $users = Arr::sort($this->users, function ($user){
            return $user['score'];
        });

        // 数组倒序
        $users = array_reverse($users, true);
        // 获取我们想要的数据
        $users = array_slice($users,0, $this->user_number, true);
        // 新建一个空集合
        $active_users = collect();
        foreach ($users as $user_id => $user) {
            // 找寻下是否可以找到用户
            $user = $this->find($user_id);

            // 如果数据库里有该用户的话
            if ($user) {
                // 将此用户实体放入集合的末尾
                $active_users->push($user);
            }
        }
        // 返回数据
        return $active_users;
    }

    /**
     * 计算话题
     */
    private function calculateTopicScore()
    {
        // 从话题数据表里取出限定时间范围内，有发表过话题的用户
        // 并且同时取出用户此段时间内发布话题的数量
        $topic_users = Topic::query()->select(DB::raw('user_id, count(*) as topic_count'))
                                    ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                                    ->groupBy('user_id')
                                    ->get();
        // 根据话题数量计算分数
        foreach ($topic_users as $value) {
            $this->users[$value->user_id]['score'] = $value->topic_count * $this->topic_weight;
         }
    }

    /**
     * 计算回复
     */
    private function calculateReplyScore()
    {
        // 从评论表取出限定时间范围内，评论的用户
        // 并取出用户此段时间内发布的话题数量
        $reply_users = Reply::query()->select(DB::raw('user_id, count(*) as reply_count'))
                                    ->where('created_at', '>=', Carbon::now()->subDays($this->pass_days))
                                    ->groupBy('user_id')
                                    ->get();
        // 根据评论计算分数
        foreach ($reply_users as $value)
        {
            $reply_score = $value['reply_count'] * $this->reply_weight;
            if (isset($this->users[$value['user_id']])) {
                $this->users[$value->user_id]['score'] += $reply_score;
            } else {
                $this->users[$value->user_id]['score'] = $reply_score;
            }
        }
    }

    /**
     * 存入缓存
     * @param $active_users
     */
    private function cacheActiveUsers($active_users)
    {
        // 将数据存入缓存中
        Cache::put($this->cache_key, $active_users, $this->cache_expire_in_seconds);
    }

}
