<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Status;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class StatusCheckTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_status_not_work()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '勤務外')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('勤務外');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_status_working()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '出勤中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_status_breaking()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '休憩中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_status_leave()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => Carbon::now(),
            'leave' => Carbon::now()
        ]);
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now(),
            'end_rest' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }
}
