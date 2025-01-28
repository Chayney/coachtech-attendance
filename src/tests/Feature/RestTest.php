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

class RestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_rest()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $commuteStatus = Status::where('name', '出勤中')->first();
        $restStatus = Status::where('name', '休憩中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $commuteStatus->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->post('/rest/start', [
            'status_id' => $restStatus->id
        ]);
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_rest_many_times_a_day()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $commuteStatus = Status::where('name', '出勤中')->first();
        $restStatus = Status::where('name', '休憩中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $commuteStatus->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->post('/rest/start', [
            'status_id' => $restStatus->id
        ]);
        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response = $this->patch('/rest/end', [
            'status_id' => $commuteStatus->id,
            'end_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_status_working()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $commuteStatus = Status::where('name', '出勤中')->first();
        $restStatus = Status::where('name', '休憩中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $commuteStatus->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->post('/rest/start', [
            'status_id' => $restStatus->id
        ]);
        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response = $this->patch('/rest/end', [
            'status_id' => $commuteStatus->id,
            'end_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_back_rest()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $commuteStatus = Status::where('name', '出勤中')->first();
        $restStatus = Status::where('name', '休憩中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $commuteStatus->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->post('/rest/start', [
            'status_id' => $restStatus->id
        ]);
        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response = $this->patch('/rest/end', [
            'status_id' => $commuteStatus->id,
            'end_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->post('/rest/start', [
            'status_id' => $restStatus->id
        ]);
        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_rest_record_check()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $commuteStatus = Status::where('name', '出勤中')->first();
        $restStatus = Status::where('name', '休憩中')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $commuteStatus->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->post('/rest/start', [
            'status_id' => $restStatus->id
        ]);
        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
        $response = $this->patch('/rest/end', [
            'end_rest' => Carbon::now(),
            'break_time' => Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->id)->first()->totalRestTime,
            'status_id' => $commuteStatus->id,        
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $lastMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $this->assertTrue($attendances->contains('status_id', $commuteStatus->id));
        $response = $this->get('/attendance/list');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }
}
