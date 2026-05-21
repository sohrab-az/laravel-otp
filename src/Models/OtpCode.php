<?php

namespace SohrabAzinfar\OTP\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $table = 'otp_codes';

    public $timestamps = true;

    protected $fillable = [
        'guard',
        'identifier',
        'code',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function scopeForIdentifier(Builder $query, string $guard, string $identifier)
    {
        return $query->where('guard', $guard)
                     ->where('identifier', $identifier);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return ! is_null($this->used_at);
    }
}