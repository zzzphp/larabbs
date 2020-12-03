<?php

namespace App\Http\Controllers;

use App\Models\UsersRelation;
use App\Http\Requests\UsersRelationRequest;
use Illuminate\Support\Facades\Auth;

class UsersRelationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function store(UsersRelationRequest $request, UsersRelation $usersRelation)
	{
	    $relation = $usersRelation->where(['user_id' => Auth::id(),
            'follower_id' => $request->follower_id
            ,'relation_type' => 1])->get();
	    if ($relation->isEmpty()) {
	        $usersRelation->user_id = Auth::id();
	        $usersRelation->follower_id = $request->follower_id;
	        $usersRelation->relation_type = 1;
            $this->authorize('create', $usersRelation);
	        $usersRelation->save();
            return redirect()->back()->with('success', '关注成功！');
        } else {
            return redirect()->back()->with('success', '已关注！');
        }
	}

	public function destroy(UsersRelation $users_relation)
	{
		$this->authorize('destroy', $users_relation);
		$users_relation->delete();
		return redirect()->back()->with('success', '取消关注成功！');
	}
}
