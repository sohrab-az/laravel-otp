<?php

namespace SohrabAzinfar\OTP\Data;

use SohrabAzinfar\OTP\Models\OtpCode;

class OtpData
{
    public function __construct(
        public OtpCode $otp,
        public string $code,
    ) {}
}