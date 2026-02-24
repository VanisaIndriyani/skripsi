<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    // Public Kiosk View
    public function kiosk()
    {
        // Get active session
        // We look for any session that is marked 'is_active' = true
        // regardless of time constraints for now, to be more flexible.
        // Or we can keep time constraint but ensure timezone is correct.
        
        $activeSession = WorkSession::where('is_active', true)
            ->whereDate('date', Carbon::today())
            ->first();

        // Get all employees
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('attendance.kiosk', compact('activeSession', 'employees'));
    }

    // Public Store (No Auth required, user_id from request)
    public function storePublic(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'photo' => 'required',
        ]);

        // Find active session
        $session = WorkSession::where('is_active', true)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$session) {
            return redirect()->back()->with('error', 'Tidak ada sesi absensi yang aktif saat ini.');
        }

        // Check if already attended this session
        $existing = Attendance::where('user_id', $request->user_id)
            ->where('work_session_id', $session->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah melakukan absensi untuk sesi ini.');
        }

        // Process Photo
        $photoPath = null;
        if ($request->has('photo') && $request->photo) {
            $image = $request->photo;
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'attendance/' . $request->user_id . '_' . time() . '_in.jpg';
            
            Storage::disk('public')->put($imageName, base64_decode($image));
            $photoPath = $imageName;
        }

        Attendance::create([
            'user_id' => $request->user_id,
            'work_session_id' => $session->id,
            'date' => Carbon::today(),
            'time_in' => Carbon::now()->toTimeString(),
            'status' => 'present',
            'location_in' => $request->location,
            'photo_in' => $photoPath
        ]);

        return redirect()->back()->with('success', 'Absensi Berhasil! Selamat Bekerja.');
    }

    // Keep original store/update for backward compatibility if needed, 
    // but effectively the kiosk replaces the individual login flow.
    public function store(Request $request)
    {
        // ... (previous implementation)
    }

    public function update(Request $request, Attendance $attendance)
    {
        // ... (previous implementation)
    }
}
