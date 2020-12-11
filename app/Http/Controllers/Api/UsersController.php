<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Requests\Request;
use App\Http\Resources\UserResource;
use Cassandra\Exception\AuthenticationException;
use App\Models\User;

class UsersController extends Controller
{
    //
    public function store(UserRequest $userRequest)
    {
        $verifyData = \Cache::get($userRequest->verification_key);
        if (!$verifyData) {
            abort(403,'验证码已失效');
        }
        // 时间恒等 判断字符串是否相等
        if (!hash_equals(strval($verifyData['code']), strval($userRequest->verification_code))) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }
        $user = User::create([
            'name' => $userRequest->name,
            'phone' => $verifyData['phone'],
            'password' => $userRequest->password,
        ]);

        // 清除验证码缓存
        \Cache::forget($userRequest->verification_key);

        return new UserResource($user);
    }
    // 查看某个用户信息
    public function show(User $user, Request $request) {
        return new UserResource($user);
    }

    // 当前登录用户信息
    public function me(Request $request) {
        return (new UserResource($request->user()))->showSensitiveFields();
    }
}
