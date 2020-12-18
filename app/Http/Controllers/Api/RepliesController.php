<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Topic;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    //发布回复
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->get('content');
        $reply->topic()->associate($topic);
        $reply->user()->associate($request->user());
        $reply->save();

        return new ReplyResource($reply);
    }

    public function destroy(Topic $topic, Reply $reply)
    {
        if ($reply->topic_id != $topic->id) {
            return abort(404,'该回复不存在');
        }
        $this->authorize('destroy', $reply);
        $reply->delete();

        return response(null, 204);
    }
}
