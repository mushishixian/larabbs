<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\VerificationCodeRequest;
use App\Service\Api\SmsService;
use Illuminate\Http\Request;

class VerificationCodesController extends Controller
{
    //
    public function store(VerificationCodeRequest $request, SmsService $service)
    {
        $captchaData = \Cache::get($request->captcha_key);

        if (!$captchaData) {
            return $this->response->error('图片验证码已失效', 422);
        }

        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            // 验证错误就清除缓存
            \Cache::forget($request->captcha_key);
            return $this->response->errorUnauthorized('验证码错误');
        }

        $phone = $captchaData['phone'];

        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            $service->handleSendPhoneCode(18825159814, $code);
        }
        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinute(1<<10);

        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
