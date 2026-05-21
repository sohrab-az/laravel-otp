<?php

namespace SohrabAzinfar\OTP\Commands;

use Illuminate\Console\Command;
use SohrabAzinfar\OTP\Models\OtpCode;

class CleanExpiredOtps extends Command
{
    protected $signature = 'otp:clean';
    protected $description = 'Delete expired OTP records';

    public function handle(): int
    {
        $deleted = OtpCode::where('expires_at', '<', now()->subDay())
            ->delete();

        $this->info("Deleted {$deleted} expired OTPs");

        return self::SUCCESS;
    }
}