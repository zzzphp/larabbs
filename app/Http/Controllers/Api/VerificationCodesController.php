<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        // 验证图形验证码
        $captchaData = Cache::get($request->captcha_key);
        if (!$captchaData) {
            abort('403', '验证码已失效');
        }
        // abort('403', $captchaData['code']);
        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证码错误就清除
            Cache::forget($request->captcha_key);//AuthenticationException
            throw new AuthenticationException('验证码错误');
        }

        $phone = $request->phone;
        if (!app()->environment('production')) {
            // 测试环境
            $code = 1234;
        } else {
            // 生成4位随机整数，左侧补零
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            try {
                $result = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.template.register'),
                    'data' => [
                        'code' => $code
                    ],
                ]);
            } catch (NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                abort(500, $message ?: '短信发送异常');
            }
        }
        // 存储验证码
        $key = 'VerificationCodes' . \Illuminate\Support\Str::random(15);
        $expiredAt = now()->addMinutes(5); // 过期时间5分钟
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        // 清除图形验证码缓存
        Cache::forget($request->captcha_key);

        return response()->json([
            'code' => 0,
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString()
        ])->setStatusCode(201);
    }
}
