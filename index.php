<?php

include_once "Utils.php";


class wechat {
    protected $client;
    protected $appId;
    protected $secret;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $baseUrl = 'https://api.weixin.qq.com';
        $this->client = new Utils();
        $this->client->setBaseUrl($baseUrl);
        $this->appId = getenv('app_id');
        $this->secret = getenv('app_secret');
        if (!$this->appId) {
            throw new Exception('appid 获取失败');
        }
        if (!$this->secret) {
            throw new Exception('appid 获取失败');
        }
    }

    /**
     * @description 获取access_token
     * @return string
     * @throws Exception
     */
    protected function getAccessToken(): string
    {
        $response = $this->client->get('cgi-bin/token', [
            'grant_type' => 'client_credential',
            'appid' => $this->appId,
            'secret' => $this->secret,
        ]);
        if (isset($response['error'])) {
            throw new Exception($response['error']);
        }
        if (isset($response['errcode'])) {
            throw new Exception($response['errmsg']);
        }
        return $response['access_token'];
    }

    /**
     * @description 发送消息通知
     * @return void
     * @throws Exception
     */
    public function sendTemplate()
    {
        try {
            $accessToken = $this->getAccessToken();
            $openId = getenv('open_id');
            $this->client->post('cgi-bin/message/template/send', [
                'touser' => $openId,
                'template_id' => getenv('template_id'),
                'data' => [
                    'today' => [
                        'value' => date('Y-m-d'),
                    ],
                    'weather' => [
                        'value' => '天气好冷',
                    ],
                ],
            ], [
                'access_token' => $accessToken,
            ]);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

(new wechat())->sendTemplate();

echo "success";
