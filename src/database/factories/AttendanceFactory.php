<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Attendance::class;

    public function definition()
    {
        $commute = Carbon::create(2025, 1, rand(1, 31), rand(6, 9));
        $leave = $commute->copy()->addMinutes(540);

        return [
            'user_id' => User::factory(),
            'status_id' => 1,
            'date' => $commute->toDateString(),
            'commute' => $commute,
            'leave' => $leave,
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ];
    }
}
