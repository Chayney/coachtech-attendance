<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Approve;
use App\Models\Rest;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y/m/d'));
        $today = Carbon::createFromFormat('Y/m/d', $date);
        $yesterday = (new Carbon($request->date))->subDay();
        $tomorrow = (new Carbon($request->date))->addDay();
        $attendances = Attendance::with('user')->whereDate('date', $today)->get();

        return view('admin.index', compact('attendances', 'today', 'yesterday', 'tomorrow'));
    }

    public function show()
    {
        $users = User::all();

        return view('admin.userlist', compact('users'));
    }

    public function list(Request $request)
    {
        $id = $request->id;
        $user = $request->name;
        $dateString = $request->input('date', Carbon::now()->format('Y/m'));
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $lastMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');
        $attendances = Attendance::with('user')->where('user_id', $id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->get();

        return view('admin.attendance', compact('id', 'user', 'attendances', 'thisMonth', 'lastMonth', 'nextMonth'));
    }

    public function detail(Request $request)
    {
        $date = Carbon::parse($request->date);
        $attendances = Attendance::with('user')->where('id', $request->id)->whereDate('date', $date->toDateString())->get();
        $attendance = Attendance::with('user')->where('id', $request->id)->first();
        $rests = Rest::where('attendance_id', $attendance->id)->get();

        return view('admin.detail', compact('attendances', 'rests')); 
    }

    public function apply()
    {
        $unapproves = Approve::with(['approveAttendance', 'approveUser'])->where('status', '承認待ち')->get();
        $approves = Approve::with(['approveAttendance', 'approveUser'])->where('status', '承認済み')->get();
        
        return view('admin.application', compact('unapproves', 'approves'));
    }

    public function approve(Request $request)
    {
        $attendances = Approve::with(['approveAttendance', 'approveUser'])->where('id', $request->id)->get();
        $attendance = Attendance::with('user')->where('id', $request->id)->first();
        $rests = Rest::where('attendance_id', $attendance->id)->get();
        
        return view('admin.approve', compact('attendances', 'rests'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $approve = Approve::where('id', $request->id)->first();
        $approve->update([
            'status' => '承認済み'
        ]);
        
        return redirect("/stamp_correction_request/approve/{attendance_correct_request}?id={$id}")->with('success', '修正しました');
    }
}
