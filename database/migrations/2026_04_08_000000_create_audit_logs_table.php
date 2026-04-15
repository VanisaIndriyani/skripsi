<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $header) {
            $header->id();
            $header->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $header->string('activity');
            $header->text('description')->nullable();
            $header->string('ip_address')->nullable();
            $header->string('user_agent')->nullable();
            $header->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
