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
use Spatie\Permission\Models\Role;

class AdminUserRecordTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_all_user_list()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/admin/staff/list');
        $response->assertSee($user->name);
        $response->assertSee($user->email);
        $this->assertAuthenticatedAs($adminUser);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_user_record()
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
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/admin/staff/list');
        $this->assertAuthenticatedAs($adminUser);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        $attendance = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->first();
        $response = $this->get('/admin/attendance/staff/{id}?id=' . $attendance->id);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_user_sub_month_record()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today->toDateString(),
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $subMonth = Carbon::now()->subMonth();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $subMonth->toDateString(),
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/admin/staff/list');
        $this->assertAuthenticatedAs($adminUser);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'DESC')->get();
        $attendance = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'DESC')->first();
        $response = $this->get('/admin/attendance/staff/{id}?id=' . $attendance->id . '&month=' . $thisMonth);
        $lastMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$lastMonth])->orderby('date', 'ASC')->get();
        $attendance = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$lastMonth])->orderby('date', 'ASC')->first();
        $response = $this->get('/admin/attendance/staff/{id}?id=' . $attendance->id . '&month=' . $lastMonth);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_user_add_month_record()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today->toDateString(),
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $addMonth = Carbon::now()->addMonth();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $addMonth->toDateString(),
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/admin/staff/list');
        $this->assertAuthenticatedAs($adminUser);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'DESC')->get();
        $attendance = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'DESC')->first();
        $response = $this->get('/admin/attendance/staff/{id}?id=' . $attendance->id . '&month=' . $thisMonth);
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$nextMonth])->orderby('date', 'ASC')->get();
        $attendance = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$nextMonth])->orderby('date', 'ASC')->first();
        $response = $this->get('/admin/attendance/staff/{id}?id=' . $attendance->id . '&month=' . $nextMonth);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_user_a_day_record_detail()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $today = Carbon::now();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $today->toDateString(),
            'commute' => $commute,
            'leave' => $commute->modify('+9hour'),
            'break_time' => '01:00:00',
            'work_time' => '08:00:00'
        ]);
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $response = $this->get('/admin/staff/list');
        $this->assertAuthenticatedAs($adminUser);
        $dateString = Carbon::now()->format('Y/m');
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'DESC')->get();
        $attendance = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'DESC')->first();
        $response = $this->get('/admin/attendance/staff/{id}?id=' . $attendance->id . '&month=' . $thisMonth);
        $response = $this->get('/attendance/{id}?id=' . $attendance->id . '&date=' . $attendance->date);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }
}
