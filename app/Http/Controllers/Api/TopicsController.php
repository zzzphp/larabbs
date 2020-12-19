<?php

namespace App\Http\Controllers\Api;

use App\Http\Queries\TopicQuery;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Requests\Request;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use App\Models\User;

class TopicsController extends Controller
{
    // 添加话题
    public function store(Topic $topic, TopicRequest $request)
    {
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();
        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->all());
        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();
        return response(null, 204);
    }
    // 话题列表
    public function index(Request $request, TopicQuery $query)
    {
//        $query = $topic->query();
//        if ($categoryId = $request->category_id) {
//            $query->where('category_id', $categoryId);
//        }
//        $topics = $query->with('user', 'category')
//                        ->WithOrder($request->order)
//                        ->paginate();
//        $topics = QueryBuilder::for(Topic::class)
//                    ->allowedIncludes('user', 'category')
//                    ->allowedFilters([
//                        'title',
//                        AllowedFilter::exact('category_id'),
//                        AllowedFilter::scope('withOrder')->default('recentReplied'),
//                    ])
//                    ->paginate();
        $topics = $query->paginate();
        return TopicResource::collection($topics);
    }

    public function userIndex(User $user, TopicQuery $query)
    {
//        $query = $user->topics()->getQuery();
//
//        $topics = QueryBuilder::for($query)
//            ->allowedIncludes('user', 'category')
//            ->allowedFilters([
//                'title',
//                AllowedFilter::exact('category_id'),
//                AllowedFilter::scope('withOrder')->default('recentReplied'),
//            ])
//            ->paginate();
        $topics = $query->where('user_id', $user->id)->paginate();
        return TopicResource::collection($topics);
    }

    // 话题详情
    public function show($topicId, TopicQuery $query)
    {
//        $topic = QueryBuilder::for(Topic::class)
//            ->allowedIncludes('user', 'category')
//            ->findOrFail($topicId);
        $topic = $query->findOrFail($topicId);
        return (new TopicResource($topic))->showSensitiveFields();
    }



}
