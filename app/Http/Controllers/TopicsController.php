<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Models\Category;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic)
	{
        $topics = $topic->withOrder($request->order)
            ->with('user', 'category')  // 预加载防止 N+1 问题
            ->paginate(20);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
	    $topic->save();
		return redirect()->route('topics.show', $topic->id)->with('message', '帖子创建成功');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());
		return redirect()->route('topics.show', $topic->id)->with('message', '话题更新成功！');

	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();
		return redirect()->route('topics.index')->with('message', '删除成功！');
	}

	public function uploadImage(Request $request, ImageUploadHandler $uploadHandler)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有文件上传
        if ($request->upload_file) {
            // 保存图片到本地
            $result = $uploadHandler->save($request->upload_file, 'topics',Auth::id(), 1024);
            if ($result) {
                // 图片保存成功
                $data['success'] = true;
                $data['msg'] = '上传成功';
                $data['file_path'] = $result['path'];
            }
        }

        return $data;
    }
}
