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

class UserRecordDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_record_check_name()
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
        $attendances = Attendance::with('user')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        foreach ($attendances as $attendance) {
            $attendanceId = $attendance->id;
            $rests = Rest::where('attendance_id', $attendanceId)->get();
        }
        $attendanceArray = $attendances->first()->toArray();
        $userName = $attendanceArray['user']['name'];
        $response = $this->get('/attendance/{id}?id=' . $attendanceId);
        $response->assertSee($userName);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_user_record_check_date()
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
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        foreach ($attendances as $attendance) {
            $attendanceId = $attendance->id;
            $rests = Rest::where('attendance_id', $attendanceId)->get();
        }
        $attendanceArray = $attendances->first()->toArray();
        $userDate_1 = Carbon::parse($attendanceArray['date'])->isoFormat("YYYY年");
        $userDate_2 = Carbon::parse($attendanceArray['date'])->isoFormat("M月D日");
        $response = $this->get('/attendance/{id}?id=' . $attendanceId);
        $response->assertSee($userDate_1);
        $response->assertSee($userDate_2);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_user_record_check_commute_leave()
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
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        foreach ($attendances as $attendance) {
            $attendanceId = $attendance->id;
            $rests = Rest::where('attendance_id', $attendanceId)->get();
        }
        $attendanceArray = $attendances->first()->toArray();
        $userCommute = substr($attendance['commute'], 0, 5);
        $userLeave = substr($attendance['leave'], 0, 5);
        $response = $this->get('/attendance/{id}?id=' . $attendanceId);
        $response->assertSee($userCommute);
        $response->assertSee($userLeave);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_user_record_check_rest_start_end()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        $attend = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        Rest::create([
            'attendance_id' => $attend->id,
            'start_rest' => $commute,
            'end_rest' => $commute->modify('+1hour')
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list');
        foreach ($attendances as $attendance) {
            $attendanceId = $attendance->id;
            $rests = Rest::where('attendance_id', $attendanceId)->get();
        }
        $startRest = $rests->first()->start_rest;
        $userStartRest = substr($startRest, 0, 5);
        $endRest = $rests->first()->end_rest;
        $userEndRest = substr($endRest, 0, 5);
        $response = $this->get('/attendance/{id}?id=' . $attendanceId);
        $response->assertSee($userStartRest);
        $response->assertSee($userEndRest);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }
}
