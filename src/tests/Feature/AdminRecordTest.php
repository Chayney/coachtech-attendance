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

class AdminRecordTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_all_user_record()
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
        $response = $this->get('/admin/attendance/list');
        $this->assertAuthenticatedAs($adminUser);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_today()
    {
        $adminUser = User::factory()->create([
            'name' => 'テスト',
            'email' => 'admin@hello.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');
        $this->actingAs($adminUser);
        $now = Carbon::now()->format("Y年n月j日");
        $response = $this->get('/admin/attendance/list');
        $response->assertSee($now);
        $this->assertAuthenticatedAs($adminUser);
        $adminUser->delete();
    }

    public function test_admin_yesterday_record()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $yesterday = Carbon::now()->subDay();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $yesterday->toDateString(),
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
        $dateString = Carbon::now()->format('Y/m/d');
        $currentDay = Carbon::createFromFormat('Y/m/d', $dateString);
        $today = $currentDay->format('Y/m');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$today])->orderby('date', 'ASC')->get();
        $response = $this->get('/admin/attendance/list');
        $yesterdayDate = $yesterday->format('Y/m/d');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$yesterdayDate])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list?date=' . $yesterdayDate);
        $this->assertAuthenticatedAs($adminUser);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }

    public function test_admin_tommorow_record()
    {
        $this->seed(\Database\Seeders\StatusesTableSeeder::class);
        $status = Status::where('name', '退勤済')->first();
        $user = User::factory()->create();
        $tomorrow = Carbon::now()->addDay();
        $commute = Carbon::now();
        Attendance::create([
            'user_id' => $user->id,
            'status_id' => $status->id,
            'date' => $tomorrow->toDateString(),
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
        $dateString = Carbon::now()->format('Y/m/d');
        $currentDay = Carbon::createFromFormat('Y/m/d', $dateString);
        $today = $currentDay->format('Y/m');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$today])->orderby('date', 'ASC')->get();
        $response = $this->get('/admin/attendance/list');
        $tomorrowDate = $tomorrow->format('Y/m/d');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$tomorrowDate])->orderby('date', 'ASC')->get();
        $response = $this->get('/attendance/list?date=' . $tomorrowDate);
        $this->assertAuthenticatedAs($adminUser);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Status::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $user->delete();
        $adminUser->delete();
    }
}
