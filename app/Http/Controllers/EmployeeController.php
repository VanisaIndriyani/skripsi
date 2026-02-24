<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        return view('employee.dashboard', compact('attendance'));
    }

    public function history()
    {
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();
            
        return view('employee.history', compact('attendances'));
    }
}
