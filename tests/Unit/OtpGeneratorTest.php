<?php

use SohrabAzinfar\OTP\Services\OtpGenerator;

it('can generate otp code', function () {

    $generator = new OtpGenerator();

    $code = $generator->generate();

    expect($code)
        ->toBeString()
        ->toHaveLength(6);

});