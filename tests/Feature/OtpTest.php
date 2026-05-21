<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can generate otp', function () {
    $otpData = app(\SohrabAzinfar\OTP\Managers\OtpManager::class)
        ->generate('web', 'test@example.com');

    expect($otpData->otp)->not->toBeNull();

    $this->assertDatabaseHas('otp_codes', [
        'guard' => 'web',
        'identifier' => 'test@example.com',
    ]);
});

it('can verify otp successfully', function () {
    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $otpData = $manager->generate('web', 'test@example.com');

    $result = $manager->verify('web', 'test@example.com', $otpData->code);

    expect($result)->toBeTrue();

    $this->assertDatabaseHas('otp_codes', [
        'id' => $otpData->otp->id,
        'used_at' => now(),
    ]);
});

it('fails verification with wrong code', function () {
    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $otpData = $manager->generate('web', 'test@example.com');

    $result = $manager->verify('web', 'test@example.com', '000000');

    expect($result)->toBeFalse();
});

it('fails when otp is expired', function () {
    $otp = \SohrabAzinfar\OTP\Models\OtpCode::create([
        'guard' => 'web',
        'identifier' => 'test@example.com',
        'code' => '123456',
        'expires_at' => now()->subMinute(),
    ]);

    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $result = $manager->verify('web', 'test@example.com', '123456');

    expect($result)->toBeFalse();
});

it('fails when otp already used', function () {
    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $otpData = $manager->generate('web', 'test@example.com');

    $manager->verify('web', 'test@example.com', $otpData->code);

    $result = $manager->verify('web', 'test@example.com', $otpData->code);

    expect($result)->toBeFalse();
});

it('dispatches otp sent event', function () {
    \Illuminate\Support\Facades\Event::fake();

    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $manager->generate('web', 'test@example.com');

    \Illuminate\Support\Facades\Event::assertDispatched(
        \SohrabAzinfar\OTP\Events\OtpSentEvent::class
    );
});

it('dispatches otp verified event', function () {
    \Illuminate\Support\Facades\Event::fake();

    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $otpData = $manager->generate('web', 'test@example.com');

    $manager->verify('web', 'test@example.com', $otpData->code);

    \Illuminate\Support\Facades\Event::assertDispatched(
        \SohrabAzinfar\OTP\Events\OtpVerifiedEvent::class
    );
});

it('isolates otp by guard', function () {
    $manager = app(\SohrabAzinfar\OTP\Managers\OtpManager::class);

    $otpData1 = $manager->generate('web', 'test@example.com');
    $otpData2 = $manager->generate('api', 'test@example.com');

    expect($otpData1->otp->guard)->not->toBe($otpData2->otp->guard);
});