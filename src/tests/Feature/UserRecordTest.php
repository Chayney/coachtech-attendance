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

class UserRecordTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_all_record_list()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        $response = $this->get('/attendance/list');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_this_month()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_sub_month()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '勤務外')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => null,
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        $lastMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $attendancesLastMonth = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$lastMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list?month=' . $lastMonth);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_add_month()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '勤務外')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => null,
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');
        $attendancesNextMonth = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$nextMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list?month=' . $nextMonth);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_user_record_detail()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        $attendanceId = $attendances->first()->id;
        $attendances = Attendance::with('user')->where('id', $attendanceId)->get();
        $rests = Rest::where('attendance_id', $attendanceId)->get();
        $response = $this->get('/attendance/id=' . $attendanceId);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }
}
