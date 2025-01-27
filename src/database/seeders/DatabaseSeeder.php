<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StatusesTableSeeder::class,
            UsersTableSeeder::class
        ]);
        User::factory(10)->create()->each(function ($user) {
            $attendances = Attendance::factory(1)->create(['user_id' => $user->id]);
            $attendances->each(function ($attendance) {
                $rests = Rest::factory(rand(1, 3))->create(['attendance_id' => $attendance->id]);
                $break_time = '01:00:00';
                $work_time = '08:00:00';
                $attendance->update([
                    'break_time' => $break_time,
                    'work_time' => $work_time
                ]);
            });
        });
    }
}
