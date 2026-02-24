<?php

namespace App\Models;

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
}
