<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    //
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $userRequest, User $user, ImageUploadHandler $uploadHandler)
    {
        $data = $userRequest->all();
        if ($userRequest->avatar) {
            $result = $uploadHandler->save($userRequest->avatar, 'avatar', $user->id);
            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
