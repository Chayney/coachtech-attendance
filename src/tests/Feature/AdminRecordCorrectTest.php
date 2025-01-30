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
use App\Models\Approve;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class AdminRecordCorrectTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_unapprove_record()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        $commuteClone = $commute->copy();
        $leave = $commuteClone->modify('+9hour');
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute->toTimeString(),
            'leave' => $leave->toTimeString(),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $startRest = $commuteClone->modify('+1hour');
        $endRest = $commuteClone->modify('+2hour');
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => $startRest->toTimeString(),
            'end_rest' => $endRest->toTimeString()
        ]);
        $unapprove = Approve::create([
            'user_id' => $user->id,
            'attendance_id' =>$attendance->id,
            'status' => '承認待ち'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/stamp_correction_request/list?status=承認待ち');
        $this->assertAuthenticatedAs($adminUser);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_approved_record()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        $commuteClone = $commute->copy();
        $leave = $commuteClone->modify('+9hour');
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute->toTimeString(),
            'leave' => $leave->toTimeString(),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $startRest = $commuteClone->modify('+1hour');
        $endRest = $commuteClone->modify('+2hour');
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => $startRest->toTimeString(),
            'end_rest' => $endRest->toTimeString()
        ]);
        $unapprove = Approve::create([
            'user_id' => $user->id,
            'attendance_id' =>$attendance->id,
            'status' => '承認済み'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/stamp_correction_request/list?status=承認済み');
        $this->assertAuthenticatedAs($adminUser);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_unapprove_record_detail()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        $commuteClone = $commute->copy();
        $leave = $commuteClone->modify('+9hour');
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute->toTimeString(),
            'leave' => $leave->toTimeString(),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $startRest = $commuteClone->modify('+1hour');
        $endRest = $commuteClone->modify('+2hour');
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => $startRest->toTimeString(),
            'end_rest' => $endRest->toTimeString()
        ]);
        $unapprove = Approve::create([
            'user_id' => $user->id,
            'attendance_id' =>$attendance->id,
            'status' => '承認待ち'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/stamp_correction_request/list?status=承認待ち');
        $this->assertAuthenticatedAs($adminUser);
        $attendances = Approve::with(['approveAttendance', 'approveUser'])->where('id', $unapprove->id)->get();
        foreach ($attendances as $attendance) {
            $approveAttendanceId = $attendance->approveAttendance->id;
            $rests = Rest::where('attendance_id', $approveAttendanceId)->get();
        }
        $approve = Approve::with(['approveAttendance', 'approveUser'])->where('id', $unapprove->id)->first();
        $response = $this->get('/stamp_correction_request/approve/{attendance_correct_request}?id=' . $approve->id);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_approve_record_detail()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now()->toDateString();
        $commute = Carbon::now();
        $commuteClone = $commute->copy();
        $leave = $commuteClone->modify('+9hour');
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today,
            'commute' => $commute->toTimeString(),
            'leave' => $leave->toTimeString(),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $startRest = $commuteClone->modify('+1hour');
        $endRest = $commuteClone->modify('+2hour');
        Rest::create([
            'attendance_id' => $attendance->id,
            'start_rest' => $startRest->toTimeString(),
            'end_rest' => $endRest->toTimeString()
        ]);
        $unapprove = Approve::create([
            'user_id' => $user->id,
            'attendance_id' =>$attendance->id,
            'status' => '承認待ち'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/stamp_correction_request/list?status=承認待ち');
        $this->assertAuthenticatedAs($adminUser);
        $attendances = Approve::with(['approveAttendance', 'approveUser'])->where('id', $unapprove->id)->get();
        foreach ($attendances as $attendance) {
            $approveAttendanceId = $attendance->approveAttendance->id;
            $rests = Rest::where('attendance_id', $approveAttendanceId)->get();
        }
        $approve = Approve::with(['approveAttendance', 'approveUser'])->where('id', $unapprove->id)->first();
        $response = $this->get('/stamp_correction_request/approve/{attendance_correct_request}?id=' . $approve->id);
        $response = $this->patch('/approve/update', [
            'status' => '承認済み'
        ]);
        $response = $this->get('/stamp_correction_request/approve/{attendance_correct_request}?id=' . $approve->id);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }
}
