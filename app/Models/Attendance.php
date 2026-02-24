<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_session_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'photo_in',
        'photo_out',
        'location_in',
        'location_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workSession()
    {
        return $this->belongsTo(WorkSession::class);
    }
}
