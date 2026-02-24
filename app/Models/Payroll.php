<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'base_salary',
        'allowances',
        'deductions',
        'total_salary',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
