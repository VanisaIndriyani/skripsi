<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'attendance');
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $data = [];

        if ($type == 'attendance') {
            $data = Attendance::with(['user', 'workSession'])
                ->whereBetween('date', [$start_date, $end_date])
                ->orderBy('date', 'desc')
                ->get();
        } elseif ($type == 'payroll') {
            // For payrolls with start_date/end_date logic
            $data = Payroll::with('user')
                ->where(function($q) use ($start_date, $end_date) {
                    $q->whereBetween('start_date', [$start_date, $end_date])
                      ->orWhereBetween('end_date', [$start_date, $end_date]);
                })
                ->orderBy('start_date', 'desc')
                ->get();
        }

        return view('admin.reports.index', compact('data', 'type', 'start_date', 'end_date'));
    }

    public function print(Request $request)
    {
        $type = $request->input('type');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if ($type == 'attendance') {
            $attendances = Attendance::with(['user', 'workSession'])
                ->whereBetween('date', [$start_date, $end_date])
                ->orderBy('date', 'asc')
                ->get();

            $pdf = Pdf::loadView('admin.reports.attendance_pdf', compact('attendances', 'start_date', 'end_date'));
            return $pdf->download('laporan-absensi-' . $start_date . '-to-' . $end_date . '.pdf');

        } elseif ($type == 'payroll') {
            $payrolls = Payroll::with('user')
                ->where(function($q) use ($start_date, $end_date) {
                    $q->whereBetween('start_date', [$start_date, $end_date])
                      ->orWhereBetween('end_date', [$start_date, $end_date]);
                })
                ->orderBy('start_date', 'asc')
                ->get();

            $pdf = Pdf::loadView('admin.reports.payroll_pdf', compact('payrolls', 'start_date', 'end_date'));
            return $pdf->download('laporan-penggajian-' . $start_date . '-to-' . $end_date . '.pdf');
        }

        return redirect()->back();
    }
}
