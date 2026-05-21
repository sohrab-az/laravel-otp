<?php

namespace SohrabAzinfar\OTP\Events;

use SohrabAzinfar\OTP\Models\OtpCode;

class OtpVerifiedEvent
{
    public function __construct(
        public OtpCode $otp,
        public string $code
    ) {}
}