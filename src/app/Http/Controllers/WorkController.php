<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\Status;
use App\Models\Approve;
use Carbon\Carbon;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $statuses = Status::all();
        $today = Carbon::now()->toDateString();
        $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        if (empty($attendance)) {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'status_id' => 1,
                'date' => $today, 
                'commute' => null,
                'leave' => null,
                'break_time' => null,
                'work_time' => null,
                'reason' => null
            ]);
            return view('index', compact('attendance', 'statuses'));
        } else {
            $rests = Rest::where('attendance_id', $attendance->id)->latest()->first();

            return view('index', compact('attendance', 'rests', 'statuses'));
        }    
    }

    public function commute(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::now()->toDateString();
        $attend = Attendance::where('user_id', $user->id)->where('date', $today)->first();
        $attend->update([
            'commute' => Carbon::now(),
            'status_id' => 2
        ]);
 
        return redirect('/attendance')->with('success', '出勤しました');
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
                'work_time' => $workTime,
                'status_id' => 4
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
                'work_time' => $workTime,
                'status_id' => 4
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
        $attendances = Attendance::with('approves')->where('user_id', $user->id)->whereRaw("DATE_FORMAT(date, '%Y/%m') = ?", [$thisMonth])->orderby('date', 'ASC')->get();
        
        return view('attendance', compact('attendances', 'thisMonth', 'lastMonth', 'nextMonth'));
    }

    public function detail(Request $request)
    {
        $attendances = Approve::with(['approveAttendance', 'approveUser'])->where('id', $request->id)->get();
        if ($attendances->isEmpty()) {
            $attendances = Attendance::with('user')->where('id', $request->id)->get();
            $rests = Rest::where('attendance_id', $request->id)->get();

            return view('detail', compact('attendances', 'rests'));
        } else {
            $user = Auth::user();
            $attendances = Approve::with(['approveAttendance', 'approveUser'])->where('id', $request->id)->get();
            foreach ($attendances as $attendance) {
                $approveAttendanceId = $attendance->approveAttendance->id;
                $rests = Rest::where('attendance_id', $approveAttendanceId)->get();
            }
            
            return view('detail', compact('attendances', 'rests'));
        }    
    }
}