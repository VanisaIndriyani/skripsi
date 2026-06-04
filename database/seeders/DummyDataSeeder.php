<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create 5 employees
        $employees = [];
        $names = ['Ahmad Yusuf', 'Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan', 'Eka Putri'];
        $positions = ['Staff Gudang', 'Operator Produksi', 'Quality Control', 'Admin Logistik', 'Security'];

        foreach ($names as $index => $name) {
            $employees[] = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'position' => $positions[$index],
                'base_salary' => rand(3000000, 5000000),
            ]);
        }

        // 2. Define Date Range: 25 May to 4 June 2026
        $startDate = Carbon::create(2026, 5, 25);
        $endDate = Carbon::create(2026, 6, 4);

        // 3. Loop through each date
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Create a Work Session for this day
            $session = WorkSession::create([
                'title' => 'Shift Pagi - ' . $date->format('d M Y'),
                'date' => $date->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'is_active' => false,
                'wage' => 150000,
            ]);

            // 4. Create Attendance for each employee
            foreach ($employees as $employee) {
                // Randomly decide status: present, late, or absent
                $rand = rand(1, 10);
                if ($rand <= 7) {
                    $status = 'present';
                    $timeIn = '07:' . str_pad(rand(45, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $timeOut = '17:' . str_pad(rand(0, 15), 2, '0', STR_PAD_LEFT) . ':00';
                } elseif ($rand <= 9) {
                    $status = 'late';
                    $timeIn = '08:' . str_pad(rand(5, 30), 2, '0', STR_PAD_LEFT) . ':00';
                    $timeOut = '17:' . str_pad(rand(0, 5), 2, '0', STR_PAD_LEFT) . ':00';
                } else {
                    $status = 'absent';
                    $timeIn = null;
                    $timeOut = null;
                }

                Attendance::create([
                    'user_id' => $employee->id,
                    'work_session_id' => $session->id,
                    'date' => $date->toDateString(),
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'status' => $status,
                    'location_in' => $status !== 'absent' ? 'Office A' : null,
                    'location_out' => $status !== 'absent' ? 'Office A' : null,
                ]);
            }
        }
    }
}
