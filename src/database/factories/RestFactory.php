<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Rest::class;

    public function definition()
    {
        $start_rest = Carbon::create(2025, 1, rand(1, 31), rand(10, 13));
        $end_rest = $start_rest->copy()->addMinutes(60);

        return [
            'attendance_id' => Attendance::factory(),
            'start_rest' => $start_rest,
            'end_rest' => $end_rest
        ];
    }
}
