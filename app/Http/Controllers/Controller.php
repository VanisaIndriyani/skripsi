<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function logActivity($activity, $description = null)
    {
        try {
            \App\Models\AuditLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'activity' => $activity,
                'description' => $description,
                'ip_address' => request()->ip(),
                'user_agent' => \Illuminate\Support\Str::limit(request()->userAgent(), 255)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Audit Log Error: ' . $e->getMessage());
        }
    }
}
