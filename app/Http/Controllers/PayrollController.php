<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        // Order by start_date if available, otherwise fallback to year/month for old data
        $payrolls = Payroll::with('user')
            ->orderBy('start_date', 'desc')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
            
        return view('admin.payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        return view('admin.payrolls.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // For backward compatibility or sorting, we can still set month/year based on end_date
        $month = $endDate->month;
        $year = $endDate->year;

        $employees = User::where('role', 'employee')->get();
        $count = 0;

        foreach ($employees as $employee) {
            // Check if payroll exists for this exact period
            $exists = Payroll::where('user_id', $employee->id)
                ->where('start_date', $request->start_date)
                ->where('end_date', $request->end_date)
                ->exists();
            
            if (!$exists) {
                // Calculate Total Wages from Sessions in this Date Range
                $attendances = Attendance::with('workSession')
                    ->where('user_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->where('status', 'present')
                    ->whereNotNull('work_session_id')
                    ->get();

                $totalSessionWage = 0;
                foreach ($attendances as $attendance) {
                    if ($attendance->workSession) {
                        $totalSessionWage += $attendance->workSession->wage;
                    }
                }

                $total = $totalSessionWage; 

                if ($total >= 0) {
                    Payroll::create([
                        'user_id' => $employee->id,
                        'start_date' => $request->start_date,
                        'end_date' => $request->end_date,
                        'month' => $month, // Optional, for reference
                        'year' => $year,   // Optional, for reference
                        'base_salary' => 0,
                        'allowances' => $totalSessionWage,
                        'deductions' => 0, 
                        'total_salary' => $total,
                        'status' => 'pending'
                    ]);
                    $count++;
                }
            }
        }

        return redirect()->route('admin.payrolls')->with('success', "Generated payroll for $count employees (Period: " . $startDate->format('d M') . " - " . $endDate->format('d M Y') . ").");
    }

    public function print(Payroll $payroll)
    {
        $pdf = Pdf::loadView('admin.payrolls.print', compact('payroll'));
        return $pdf->download('slip-gaji-' . $payroll->user->name . '-' . $payroll->id . '.pdf');
    }
}
