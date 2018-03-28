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
        $phone = $request->phone;
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);
            $service->handleSendPhoneCode(18825159814, $code);
        }
        $key = 'verificationCode_' . str_random(15);
        $expiredAt = now()->addMinute(10);

        \Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
