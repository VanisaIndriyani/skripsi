<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSession;
use App\Models\Setting;
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

        $employees = User::select(['id', 'name', 'photo'])
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        $attendedUserIds = [];
        if ($activeSession) {
            $attendedUserIds = Attendance::where('work_session_id', $activeSession->id)
                ->pluck('user_id')
                ->map(fn ($id) => (string) $id)
                ->values()
                ->all();
        }

        return view('attendance.kiosk', compact('activeSession', 'employees', 'attendedUserIds'));
    }

    // Public Store (No Auth required, user_id from request)
    public function storePublic(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'photo' => 'required',
            'location' => 'required',
        ]);

        // Geo Validation
        $officeLat = \Illuminate\Support\Facades\Cache::remember('office_latitude', 60*24, function () {
            return Setting::get('office_latitude');
        });
        $officeLng = \Illuminate\Support\Facades\Cache::remember('office_longitude', 60*24, function () {
            return Setting::get('office_longitude');
        });
        $maxRadius = \Illuminate\Support\Facades\Cache::remember('office_radius', 60*24, function () {
            return Setting::get('office_radius', 100);
        });

        if ($officeLat && $officeLng) {
            $userLoc = explode(',', $request->location);
            if (count($userLoc) == 2) {
                $lat = floatval($userLoc[0]);
                $lng = floatval($userLoc[1]);
                
                $distance = $this->calculateDistance($officeLat, $officeLng, $lat, $lng);
                
                if ($distance > $maxRadius) {
                    return redirect()->back()->with('error', 'Anda berada di luar jangkauan kantor (' . round($distance) . 'm). Maksimal ' . $maxRadius . 'm.');
                }
            } else {
                return redirect()->back()->with('error', 'Format lokasi tidak valid.');
            }
        }

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

        $employee = User::find($request->user_id);
        $this->logActivity('Absensi Kiosk', 'Karyawan ' . $employee->name . ' melakukan absensi pada sesi ' . $session->title);

        return redirect()->back()->with('success', 'Absensi Berhasil! Selamat Bekerja.');
    }

    // Calculate Distance (Haversine Formula)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
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
