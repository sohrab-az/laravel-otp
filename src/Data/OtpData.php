<?php

namespace SohrabAzinfar\OTP\Data;

use DateTimeInterface;
use SohrabAzinfar\OTP\Models\OtpCode;

class OtpData
{
    public function __construct(
        public string $identifier,
        public string $code,
        public string $guard,
        public DateTimeInterface $expiresAt,
    ) {}
}