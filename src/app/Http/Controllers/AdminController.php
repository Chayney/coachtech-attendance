<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Approve;
use App\Models\Rest;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Requests\AttendRequest;

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

    public function renew(AttendRequest $request)
    {
        $user = Auth::user();
        $id = $request->id;
        $date1 = $request->input('date_1');
        $date2 = $request->input('date_2');
        $full_date = $date1 . $date2;
        $date = Carbon::createFromFormat('Y年m月d日', $full_date)->toDateString();
        $commute = new Carbon($request->commute);
        $commuteStr = $request->input('commute');
        $commuteTime = Carbon::createFromFormat('H:i', $commuteStr)->toTimeString();
        $leave = new Carbon($request->leave);
        $leaveStr = $request->input('leave');
        $leaveTime = Carbon::createFromFormat('H:i', $leaveStr)->toTimeString();
        $startRests = $request->input('start_rest');
        $endRests = $request->input('end_rest');
        $reason = $request->input('reason');
        $attendance = Attendance::where('id', $request->id)->first();
        $rests = Rest::where('attendance_id', $attendance->id)->get();
        if (is_array($startRests)) {
            foreach ($request->start_rest as $id => $startRest) {
                $rest = Rest::find($id);
                if ($rest) {
                    $rest->start_rest = Carbon::createFromFormat('H:i', $startRest)->toTimeString();
                    $endRest = $endRests[$id] ?? null;
                    if ($endRest) {
                        $rest->end_rest = Carbon::createFromFormat('H:i', $endRest)->toTimeString();
                    }        
                    $rest->save();
                }
            }
        } else {
            $rest = Rest::find($id);
            $startRest = $request->input('start_rest');
            $endRest = $request->input('end_rest');
            $rest->start_rest = Carbon::createFromFormat('H:i', $startRest)->toTimeString();
            $rest->end_rest = Carbon::createFromFormat('H:i', $endRest)->toTimeString();
            $rest->save();
        }      
        $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->id)->first();
        $breakTime = $restTime->totalRestTime;
        $recessTime = new Carbon($breakTime);
        $seconds = $recessTime->hour * 3600 + $recessTime->minute * 60 + $recessTime->second;
        $stayingTime = $commute->diffInSeconds($leave);
        $stayTime = $stayingTime - $seconds;
        $workingTimeSeconds = floor($stayTime % 60);
        $workingTimeMinutes = floor(($stayTime % 3600) / 60);
        $workingTimeHours = floor($stayTime / 3600);
        $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);
        $attendance->update([
            'date' => $date,
            'commute' => $commuteTime,
            'leave' => $leaveTime,
            'work_time' => $workTime,
            'break_time' => $breakTime,
            'reason' => $reason
        ]);
              
        return redirect("/attendance/{id}?id={$id}&date={$date}")->with('success', '修正しました');
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
        foreach ($attendances as $attendance) {
            $approveAttendanceId = $attendance->approveAttendance->id;
            $rests = Rest::where('attendance_id', $approveAttendanceId)->get();
        }
        
        return view('admin.approve', compact('attendances', 'rests'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $approve = Approve::where('id', $request->id)->first();
        $approve->update([
            'status' => '承認済み'
        ]);
        
        return redirect("/stamp_correction_request/approve/{attendance_correct_request}?id={$id}")->with('success', '承認しました');
    }
}
