<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkSession;
use Carbon\Carbon;

class WorkSessionSeeder extends Seeder
{
    public function run()
    {
        // Create 5 sessions for the current week/future
        $sessions = [
            [
                'title' => 'Shift Pagi - Gudang A',
                'start_time' => '08:00',
                'end_time' => '16:00',
                'wage' => 150000,
            ],
            [
                'title' => 'Shift Siang - Gudang B',
                'start_time' => '13:00',
                'end_time' => '21:00',
                'wage' => 150000,
            ],
            [
                'title' => 'Shift Malam - Keamanan',
                'start_time' => '20:00',
                'end_time' => '04:00',
                'wage' => 175000,
            ],
            [
                'title' => 'Lembur Harian - Packing',
                'start_time' => '16:00',
                'end_time' => '20:00',
                'wage' => 75000,
            ],
            [
                'title' => 'Shift Khusus - Bongkar Muatan',
                'start_time' => '09:00',
                'end_time' => '12:00',
                'wage' => 100000,
            ],
        ];

        foreach ($sessions as $index => $session) {
            WorkSession::create([
                'title' => $session['title'],
                'date' => Carbon::today()->addDays($index), // Spread over next 5 days
                'start_time' => $session['start_time'],
                'end_time' => $session['end_time'],
                'wage' => $session['wage'],
                'is_active' => false, // Default inactive so admin can click
            ]);
        }
    }
}
