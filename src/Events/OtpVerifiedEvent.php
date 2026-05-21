<?php

namespace SohrabAzinfar\OTP\Events;

use SohrabAzinfar\OTP\Data\OtpData;
use SohrabAzinfar\OTP\Models\OtpCode;

class OtpVerifiedEvent
{
    public function __construct(
        public OtpData $otpData,
    ) {}
}