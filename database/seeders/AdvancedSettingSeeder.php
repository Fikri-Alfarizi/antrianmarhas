<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdvancedSetting;
use Carbon\Carbon;

class AdvancedSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdvancedSetting::firstOrCreate(
            ['id' => 1],
            [
                'theme_color' => '#088828ff',
                'display_refresh_seconds' => 5,
                'working_hours_start' => Carbon::createFromTime(8, 0, 0),
                'working_hours_end' => Carbon::createFromTime(17, 0, 0),
                'display_clock_mode' => 'now',
            ]
        );
    }
}
