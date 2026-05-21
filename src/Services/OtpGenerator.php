<?php

namespace SohrabAzinfar\OTP\Services;

class OtpGenerator
{
    public function generate(int $length = 6): string
    {
        if ($length < 2) {
            throw new \InvalidArgumentException('OTP length must be at least 1.');
        }

        $min = $length === 1 ? 0 : (int) pow(10, $length - 1);
        $max = (int) pow(10, $length) - 1;

        return (string) random_int($min, $max);
    }
}