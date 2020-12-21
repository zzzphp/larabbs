<?php

namespace App\Observers;

use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{

    public function saving(Topic $topic)
    {
        // 生成摘录
        $topic->excerpt = make_excerpt($topic->body);
        // XSS 过滤
        $topic->body = clean($topic->body, 'user_topic_body');
        //
    }

    public function saved(Topic $topic)
    {
        if (! $topic->slug) {
            // 调用百度API进行翻译
            //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            // 推送到任务队列
            dispatch(new TranslateSlug($topic));
        }
    }

    public function deleted(Topic $topic)
    {
        // 话题被删除后删除话题所属的回复
        DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}
