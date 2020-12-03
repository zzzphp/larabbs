<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UsersRelation;
use Illuminate\Http\Request;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }

    //
    public function show(User $user, UsersRelation $usersRelation)
    {
        $follow = $usersRelation->isFollow($user->id);
        return view('users.show', compact('user', 'follow', 'usersRelation'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $userRequest, User $user, ImageUploadHandler $uploadHandler)
    {
        $this->authorize('update', $user);
        $data = $userRequest->all();
        if ($userRequest->avatar) {
            $result = $uploadHandler->save($userRequest->avatar, 'avatars', $user->id, 416);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
