<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_codes', function ($table) {
            $table->id();

            $table->string('guard');
            $table->string('identifier');

            $table->string('code');

            $table->timestamp('expires_at')->index();
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index('used_at');
            $table->index(['guard', 'identifier']);
            $table->index(['identifier', 'code']);
        });
    }

    public function down(): void
    {
        Schema::drop('otp_codes');
    }
};