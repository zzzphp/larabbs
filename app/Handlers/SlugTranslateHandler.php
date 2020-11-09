<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        // 实例化HTTP客户端
        $http = new Client;

        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }
        // 生成签名
        $sign = md5($appid . $text . $salt . $key);

        // 构建请求参数
        $query = http_build_query([
            "q" => $text,
            "from" => "zh",
            "to" => "en",
            "appid" => $appid,
            "salt" => $salt,
            "sign" => $sign,
        ]);
        // 发送HTTP GET请求
        $response = $http->get($api.$query);
        $result = json_decode($response->getBody(), true);
        if (isset($result['trans_result'][0]['dst'])) {
            return \Str::slug($result['trans_result'][0]['dst']);
        } else {
            // 没有翻译结果，使用备用方案
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return \Str::slug(app(Pinyin::class)->permalink($text));
    }
}
