<?php

namespace NotificationChannels\QuanQiuSMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class QuanQiuSMS
{
    public const API_URL = 'http://api.quanqiusms.com';

    public $appkey;

    public $secretkey;

    /**
     * @param  string  $appkey  會員帳號 或 API Key
     * @param  string  $secretkey  會員密碼 或 API Key
     */
    public function __construct(string $appkey, string $secretkey)
    {
        $this->appkey = $appkey;
        $this->secretkey = $secretkey;
    }

    /**
     * 發送簡訊
     *
     * @param  string  $phone  接收簡訊的手機號碼，例如大陸: 8613681912700
     * @param  string  $content  簡訊內容
     * @param  array  $options  可選參數：
     * @return string 回傳服務器的響應內容
     *
     * @throws GuzzleException 如果 HTTP 請求失敗
     */
    public function sendSMS(string $phone, string $content, array $options = []): string
    {
        $params = array_merge([
            'appkey' => $this->appkey,
            'secretkey' => $this->secretkey,
            'phone' => $phone,
            'content' => urlencode($content),
        ], $options);

        $response = $this->httpClient()->post(self::API_URL.'/api/sms/mtsend', [
            'form_params' => $params,
        ]);

        return $response->getBody()->getContents();
    }

    private function httpClient(): Client
    {
        return new Client([
            'connect_timeout' => 20,
        ]);
    }
}
