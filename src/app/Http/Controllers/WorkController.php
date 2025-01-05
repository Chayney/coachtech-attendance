<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class WorkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $attendances = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if (empty($attendances)) {
            return view('index', compact('attendances'));
        } else {
            $rests = Rest::where('attendance_id', $attendances->id)->latest()->first();

            return view('index', compact('attendances', 'rests'));
        }    
    }

    public function commute(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $attend = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if (empty($attend)) {
            $attend = Attendance::create([
                'user_id' => $user->id,
                'commute' => Carbon::now(),
                'date' => Carbon::now()
            ]);

            return redirect('/attendance')->with('success', '出勤しました');
        } else {
            return redirect('/attendance')->with('alert', '出勤済です');
        } 
    }

    public function leave(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $attend = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        $now = new Carbon();
        $commute = new Carbon($attend->commute);
        if (empty($attend->break_time)) {
            $stayingTime = $commute->diffInSeconds($now);
            $workingTimeSeconds = floor($stayingTime % 60);
            $workingTimeMinutes = floor(($stayingTime % 3600) / 60);
            $workingTimeHours = floor($stayingTime / 3600);
            $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;
            $attend->update([
                'leave' => Carbon::now(),
                'work_time' => $workTime
            ]);

            return redirect('/attendance')->with('success', '退勤しました');
        } else {
            $restTime = new Carbon($attend->break_time);
            $seconds = $restTime->hour * 3600 + $restTime->minute * 60 + $restTime->second;
            $stayingTime = $commute->diffInSeconds($now);
            $stayTime = $stayingTime - $seconds;
            $workingTimeSeconds = floor($stayTime % 60);
            $workingTimeMinutes = floor(($stayTime % 3600) / 60);
            $workingTimeHours = floor($stayTime / 3600);
            $workTime = $workingTimeHours . ':' . $workingTimeMinutes . ':' . $workingTimeSeconds;
            $attend->update([
                'leave' => Carbon::now(),
                'work_time' => $workTime
            ]);

            return redirect('/attendance')->with('success', '退勤しました');
        }   
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $dateString = $request->input('date', Carbon::now()->format('Y/m'));
        $currentMonth = Carbon::createFromFormat('Y/m', $dateString);
        $thisMonth = $currentMonth->format('Y/m');
        $lastMonth = $currentMonth->copy()->subMonth()->format('Y/m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y/m');
        $attendances = Attendance::where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->get();

        return view('attendance', compact('attendances', 'thisMonth', 'lastMonth', 'nextMonth'));
    }

    public function detail(Request $request)
    {
        $attendances = Attendance::with('user')->where('id', $request->id)->get();
        $attendance = Attendance::with('user')->where('id', $request->id)->first();
        $rests = Rest::with('attendance')->where('attendance_id', $attendance->id)->get();

        return view('detail', compact('attendances', 'rests'));
    }
}
