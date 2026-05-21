<?php

namespace SohrabAzinfar\OTP\Managers;

use Illuminate\Support\Facades\Hash;
use SohrabAzinfar\OTP\Data\OtpData;
use SohrabAzinfar\OTP\Events\OtpSentEvent;
use SohrabAzinfar\OTP\Events\OtpVerifiedEvent;
use SohrabAzinfar\OTP\Models\OtpCode;
use SohrabAzinfar\OTP\Services\OtpGenerator;

class OtpManager
{
    public function __construct(
        protected OtpGenerator $generator
    ) {}

    public function send(string $guard, string $identifier): OtpData
    {
        $code = $this->generator->generate(config('otp.length', 6));

        $otp = OtpCode::create([
            'guard' => $guard,
            'identifier' => $identifier,
            'code' => Hash::make($code),
            'expires_at' => now()->addSeconds(config('otp.ttl', 120)),
        ]);

        event(new OtpSentEvent($otp, $code));

        return new OtpData($otp, $code);
    }

    public function verify(string $guard, string $identifier, string $code): bool
    {
        $otp = OtpCode::query()
            ->where('guard', $guard)
            ->where('identifier', $identifier)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $otp) {
            return false;
        }

        if (! Hash::check($code, $otp->code)) {
            return false;
        }

        $otp->update([
            'used_at' => now(),
        ]);

        event(new OtpVerifiedEvent($otp, $code));

        return true;
    }
}