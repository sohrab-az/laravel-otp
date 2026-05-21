<?php

namespace SohrabAzinfar\OTP\Facades;

use Illuminate\Support\Facades\Facade;

class Otp extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'otp';
    }
}