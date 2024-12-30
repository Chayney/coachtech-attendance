<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $attendances = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if (empty($attendances)) {
            return view('index', compact('attendances'));
        } else {
            $rests = Rest::where('attendance_id', $attendances->id)->orderBy('created_at', 'desc')->first();

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

    public function breakIn(Request $request)
    {
        $user = Auth::user();
        $attend = Attendance::where('user_id', $user->id)->first();
        $rest = Rest::where('attendance_id', $attend->id)->first();
        $rest = Rest::create([
            'attendance_id' => $attend->id,
            'start_rest' => Carbon::now()
        ]);
        
        return redirect('/attendance')->with('success', '休憩開始しました');   
    }

    public function breakOut(Request $request)
    {
        $user = Auth::user();
        $attend = Attendance::where('user_id', $user->id)->first();
        $rest = Rest::where('attendance_id', $attend->id)->orderBy('created_at', 'desc')->first();
        $rest->update([
            'end_rest' => Carbon::now()
        ]);
        $resttime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalresttime')->where('attendance_id', $attend->id)->first();
        $attend->update([
            'break_time' => $resttime->totalresttime
        ]);

        return redirect('/attendance')->with('success', '休憩終了しました');   
    }
}
