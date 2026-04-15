<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('office_latitude', '-6.175392');
        Setting::set('office_longitude', '106.827153');
        Setting::set('office_radius', '100');
    }
}
