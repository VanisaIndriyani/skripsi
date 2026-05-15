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
    private function getPayrollTotalsByEmployee(string $start_date, string $end_date)
    {
        return Payroll::query()
            ->selectRaw('user_id, SUM(total_salary) as total_salary')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->whereDate('start_date', '<=', $end_date)
            ->whereDate('end_date', '>=', $start_date)
            ->groupBy('user_id')
            ->with('user')
            ->orderByDesc('total_salary')
            ->get();
    }

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
            $data = $this->getPayrollTotalsByEmployee($start_date, $end_date);
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
            $payrolls = $this->getPayrollTotalsByEmployee($start_date, $end_date);

            $pdf = Pdf::loadView('admin.reports.payroll_pdf', compact('payrolls', 'start_date', 'end_date'));
            return $pdf->download('laporan-penggajian-' . $start_date . '-to-' . $end_date . '.pdf');
        }

        return redirect()->back();
    }

    public function exportCSV(Request $request)
    {
        $type = $request->input('type');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        $fileName = 'laporan-' . $type . '-' . $start_date . '-to-' . $end_date . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        if ($type == 'attendance') {
            $data = Attendance::with(['user', 'workSession'])
                ->whereBetween('date', [$start_date, $end_date])
                ->orderBy('date', 'asc')
                ->get();

            $columns = array('Tanggal', 'Waktu', 'Nama Karyawan', 'Sesi', 'Status');

            $callback = function() use($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($data as $item) {
                    fputcsv($file, array(
                        $item->date->format('d/m/Y'),
                        $item->time_in,
                        $item->user->name,
                        $item->workSession->title,
                        $item->status
                    ));
                }
                fclose($file);
            };
        } else {
            $data = $this->getPayrollTotalsByEmployee($start_date, $end_date);

            $columns = array('Nama Karyawan', 'Total Gaji');

            $callback = function() use($data, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($data as $item) {
                    fputcsv($file, array(
                        $item->user->name,
                        'Rp ' . number_format($item->total_salary, 0, ',', '.')
                    ));
                }
                fclose($file);
            };
        }

        return response()->stream($callback, 200, $headers);
    }
}
