<?php

namespace App\Service\Api;
use GuzzleHttp\Client;

class SmsService
{
    const SMS_HOST = 'https://api-sms.readboy.com';
    public function handleSendPhoneCode($phone, $code)
    {
        $client = new Client([
            'base_uri' => 'https://www.baidu.com',
            'timeout'  => 10.0,
        ]);
        $url = self::SMS_HOST . '/index.php?s=/Sms/Api/send';
        $authKey = $this->getAuthKey($code);

        $result = ['code' => $code];
        $response = $client->request('POST', $url, [
            'form_params' => [
                'authKey'       => $authKey,
                'appName'       => 'care.readboy.com',
                'templateCode'  => 'SMS_69985149',
                'phoneNumber'   => $phone,
                'templateParam' => json_encode($result),
            ],
        ]);
        $response = json_decode($response->getBody());
        return $response->code;
    }

    private function getAuthKey($code)
    {
        $authKey = time() . '-' . $code . '-' . md5(time() . '-' . $code . '-' . env('SMS_KEY'));

        return $authKey;
    }
}