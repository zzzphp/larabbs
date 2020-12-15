<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TopicRequest;
use App\Http\Requests\Request;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

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

    public function index(Request $request, Topic $topic)
    {
        $query = $topic->query();
        if ($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }
//        $topics = $query->with('user', 'category')
//                        ->WithOrder($request->order)
//                        ->paginate();
        $topics = QueryBuilder::for(Topic::class)
                    ->allowedIncludes('user', 'category')
                    ->allowedFilters([
                        'title',
                        AllowedFilter::exact('category_id'),
                        AllowedFilter::scope('withOrder')->default('recentReplied'),
                    ])
                    ->paginate();

        return TopicResource::collection($topics);
    }



}
