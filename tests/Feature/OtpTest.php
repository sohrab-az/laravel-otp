<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use SohrabAzinfar\OTP\Events\OtpSentEvent;
use SohrabAzinfar\OTP\Events\OtpVerifiedEvent;
use SohrabAzinfar\OTP\Facades\Otp;
use SohrabAzinfar\OTP\Models\OtpCode;

uses(RefreshDatabase::class);

it('can generate otp', function () {
    $otpData = Otp::generate('web', 'test@example.com');

    expect($otpData)->not->toBeNull();

    $this->assertDatabaseHas('otp_codes', [
        'guard' => 'web',
        'identifier' => 'test@example.com',
    ]);
});

it('can verify otp successfully', function () {

    $otpData = otp::generate('web', 'test@example.com');

    $result = Otp::verify('web', 'test@example.com', $otpData->code);

    expect($result)->toBeTrue();

    $this->assertDatabaseHas('otp_codes', [
        'identifier' => $otpData->identifier,
        'used_at' => now(),
    ]);
});

it('fails verification with wrong code', function () {

    $otpData = otp::generate('web', 'test@example.com');

    $result = Otp::verify('web', 'test@example.com', '000000');

    expect($result)->toBeFalse();
});

it('fails when otp is expired', function () {
    $otp = OtpCode::create([
        'guard' => 'web',
        'identifier' => 'test@example.com',
        'code' => '123456',
        'expires_at' => now()->subMinute(),
    ]);

    $result = Otp::verify('web', 'test@example.com', '123456');

    expect($result)->toBeFalse();
});

it('fails when otp already used', function () {
    $otpData = Otp::generate('web', 'test@example.com');

    Otp::verify('web', 'test@example.com', $otpData->code);

    $result = Otp::verify('web', 'test@example.com', $otpData->code);

    expect($result)->toBeFalse();
});

it('dispatches otp sent event', function () {
    Event::fake();

    Otp::generate('web', 'test@example.com');

    Event::assertDispatched(
        OtpSentEvent::class
    );
});

it('dispatches otp verified event', function () {
    Event::fake();

    $otpData = Otp::generate('web', 'test@example.com');

    Otp::verify('web', 'test@example.com', $otpData->code);

    Event::assertDispatched(
        OtpVerifiedEvent::class
    );
});

it('isolates otp by guard', function () {
    $otpData1 = Otp::generate('web', 'test@example.com');
    $otpData2 = Otp::generate('api', 'test@example.com');

    expect($otpData1->guard)->not->toBe($otpData2->guard);
});