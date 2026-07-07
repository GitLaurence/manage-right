<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\ShiftTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\ScheduleEntry>
 */
class ScheduleEntryFactory extends Factory
{
    public function definition(): array
    {
        $schedule = Schedule::factory()->create();
        $template = ShiftTemplate::factory()->create(['business_id' => $schedule->business_id]);

        return [
            'business_id' => $schedule->business_id,
            'schedule_id' => $schedule->id,
            'user_id' => User::factory(),
            'shift_template_id' => $template->id,
            'date' => $schedule->week_start,
            'start_time' => $template->start_time,
            'end_time' => $template->end_time,
            'break_minutes' => $template->break_minutes,
        ];
    }
}
