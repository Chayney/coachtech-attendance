<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Status;
use App\Models\Attendance;
use Carbon\Carbon;

class LeaveTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_leave()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $onDutyStatus = Status::where('name', '出勤中')->first();
        $endDutyStatus = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $onDutyStatus->id,
            'date' => $today,
            'commute' => Carbon::now()
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->patch('/leave', [
            'status_id' => $endDutyStatus->id,
            'leave' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
        $this->assertAuthenticatedAs($user);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }

    public function test_leave_record_check()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $offDutyStatus = Status::where('name', '勤務外')->first();
        $onDutyStatus = Status::where('name', '出勤中')->first();
        $endDutyStatus = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $offDutyStatus->id,
            'date' => $today,
            'commute' => null
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');
        $response->assertSee('勤務外');
        $response = $this->post('/commute', [
            'status_id' => $onDutyStatus->id,
            'commute' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response = $this->patch('/leave', [
            'status_id' => $endDutyStatus->id,
            'leave' => Carbon::now()
        ]);
        $response->assertRedirect('/attendance');
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
        $this->assertAuthenticatedAs($user);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $lastMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $this->assertTrue($attendances->contains('status_id', $endDutyStatus->id));
        $response = $this->get('/attendance/list');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
    }
}
