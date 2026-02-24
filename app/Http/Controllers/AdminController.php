<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Card Statistics
        $totalEmployees = User::where('role', 'employee')->count();
        $totalPresentToday = Attendance::where('date', date('Y-m-d'))
            ->where('status', 'present')
            ->count();
        $totalPayroll = Payroll::sum('total_salary'); // Total all time or filter by month if needed

        // 2. Attendance Chart Data (Last 7 Days)
        $attendanceLabels = [];
        $attendanceData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $attendanceLabels[] = date('d M', strtotime($date));
            $attendanceData[] = Attendance::where('date', $date)->where('status', 'present')->count();
        }

        // 3. Payroll Chart Data (Last 6 Months)
        $payrollLabels = [];
        $payrollData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('n', strtotime("-$i months"));
            $year = date('Y', strtotime("-$i months"));
            $payrollLabels[] = date('M Y', strtotime("-$i months"));
            
            // Sum total_salary for payrolls created in that month/year (approx)
            // Or use the month/year columns if they are reliable
            $payrollData[] = Payroll::where('month', $month)->where('year', $year)->sum('total_salary');
        }

        return view('admin.dashboard', compact(
            'totalEmployees', 
            'totalPresentToday', 
            'totalPayroll',
            'attendanceLabels',
            'attendanceData',
            'payrollLabels',
            'payrollData'
        ));
    }

    // Employee CRUD
    public function employees()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Photo is now file upload
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        // Auto generate dummy email
        $email = Str::slug($validated['name']) . rand(100,999) . '@pms.local';
        // Auto generate password
        $password = Hash::make('password123');

        User::create([
            'name' => $validated['name'],
            'email' => $email,
            'password' => $password,
            'role' => 'employee',
            'position' => 'Karyawan Harian', // Default position or make nullable
            'base_salary' => 0, 
            'phone_number' => $request->phone_number,
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee created successfully.');
    }

    public function updateEmployee(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $photoPath = $user->photo;
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        $user->update([
            'name' => $validated['name'],
            'phone_number' => $request->phone_number,
            'photo' => $photoPath,
        ]);

        return redirect()->route('admin.employees')->with('success', 'Employee updated successfully.');
    }

    public function destroyEmployee(User $user)
    {
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        $user->delete();
        return redirect()->route('admin.employees')->with('success', 'Employee deleted successfully.');
    }

    // Attendance Monitoring
    public function attendances()
    {
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->get();
        return view('admin.attendances.index', compact('attendances'));
    }

    // Admin Profile
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
}
