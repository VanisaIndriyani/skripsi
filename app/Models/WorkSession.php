<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'start_time',
        'end_time',
        'is_active',
        'wage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date' => 'date',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function expireEndedSessions(?Carbon $now = null): int
    {
        $now = $now ?: now();
        $today = $now->toDateString();
        $time = $now->format('H:i:s');

        return (int) static::query()
            ->where('is_active', true)
            ->where(function ($q) use ($today, $time) {
                $q->whereDate('date', '<', $today)
                    ->orWhere(function ($q2) use ($today, $time) {
                        $q2->whereDate('date', '=', $today)
                            ->whereTime('end_time', '<=', $time);
                    });
            })
            ->update(['is_active' => false]);
    }

    public function isActiveNow(?Carbon $now = null): bool
    {
        $now = $now ?: now();
        if (!$this->is_active) return false;
        if (!$this->date) return false;
        if (!$now->isSameDay($this->date)) return false;

        $start = Carbon::parse($this->date->toDateString() . ' ' . $this->start_time, config('app.timezone'));
        $end = Carbon::parse($this->date->toDateString() . ' ' . $this->end_time, config('app.timezone'));

        return $now->betweenIncluded($start, $end);
    }
}
