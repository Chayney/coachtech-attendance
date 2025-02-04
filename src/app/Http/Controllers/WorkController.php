<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\Status;
use App\Models\Approve;
use Carbon\Carbon;
use App\Http\Requests\AttendRequest;

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

    public function update(AttendRequest $request)
    {
        $unapprove = Approve::with(['approveAttendance', 'approveUser'])->where('id', $request->id)->first();
        if (empty($unapprove)) {
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
            $attendance = Attendance::where('id', $id)->first();
            if (is_array($startRests)) {
                foreach ($request->start_rest as $id => $startRest) {
                    $rest = Rest::find($id);
                    if ($rest) {
                        if (empty($startRest)) {
                            $rest->delete();
                            continue;
                        }
                        $rest->start_rest = Carbon::createFromFormat('H:i', $startRest)->toTimeString();
                        $endRest = $endRests[$id] ?? null;
                        if ($endRest) {
                            $rest->end_rest = Carbon::createFromFormat('H:i', $endRest)->toTimeString();
                        }        
                        $rest->save();
                    }
                }
            } else {
                $rest = Rest::where('attendance_id', $attendance->id)->first();
                $startRest = $request->input('start_rest');
                $endRest = $request->input('end_rest');
                if (!empty($rest) && empty($startRest) && empty($endRest)) {
                    $rest->delete();
                }
                if (empty($startRest) && empty($endRest)) {
                    $stayTime = $commute->diffInSeconds($leave);
                    $workingTimeSeconds = floor($stayTime % 60);
                    $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                    $workingTimeHours = floor($stayTime / 3600);
                    $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);   
                    $attendance->update([
                        'date' => $date,
                        'commute' => $commuteTime,
                        'leave' => $leaveTime,
                        'work_time' => $workTime,
                        'break_time' => null,
                        'reason' => $reason
                    ]);
                    $approve = Approve::create([
                        'user_id' => $user->id,
                        'attendance_id' =>$request->attendance_id,
                        'status' => '承認待ち'
                    ]);
                    
                    return redirect("/attendance/{id}?id={$approve->id}")->with('success', '修正しました');
                } else {
                    $startRestTime = Carbon::createFromFormat('H:i', $startRest)->toTimeString();
                    $endRestTime = Carbon::createFromFormat('H:i', $endRest)->toTimeString();
                    if (empty($rest)) {
                        Rest::create([
                            'attendance_id' => $attendance->id,
                            'start_rest' => $startRestTime,
                            'end_rest' => $endRestTime
                        ]);
                    } else {
                        $rest->update([
                            'start_rest' => $startRestTime,
                            'end_rest' => $endRestTime
                        ]);
                    }    
                }
            }    
            $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->id)->get();
            $breakTime = $restTime->first()->totalRestTime;
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
            $approve = Approve::create([
                'user_id' => $user->id,
                'attendance_id' =>$request->attendance_id,
                'status' => '承認待ち'
            ]);
            
            return redirect("/attendance/{id}?id={$approve->id}")->with('success', '修正しました');
            $rests = Rest::where('attendance_id', $attendance->id)->get();
            if ($rests->isEmpty()) {
                $stayTime = $commute->diffInSeconds($leave);
                $workingTimeSeconds = floor($stayTime % 60);
                $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                $workingTimeHours = floor($stayTime / 3600);
                $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);     
                $attendance->update([
                    'date' => $date,
                    'commute' => $commuteTime,
                    'leave' => $leaveTime,
                    'work_time' => $workTime,
                    'break_time' => null,
                    'reason' => $reason
                ]);
                $approve = Approve::create([
                    'user_id' => $user->id,
                    'attendance_id' =>$request->attendance_id,
                    'status' => '承認待ち'
                ]);
                
                return redirect("/attendance/{id}?id={$approve->id}")->with('success', '修正しました');
            } else {
                $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->id)->get();
                $breakTime = $restTime->first()->totalRestTime;
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
                $approve = Approve::create([
                    'user_id' => $user->id,
                    'attendance_id' =>$request->attendance_id,
                    'status' => '承認待ち'
                ]);
                
                return redirect("/attendance/{id}?id={$approve->id}")->with('success', '修正しました');
            }
        }
        if ($unapprove->status == '承認済み') {
            $id = $request->attendance_id;
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
            $attendance = Approve::where('attendance_id', $id)->first();
            if (is_array($startRests)) {
                foreach ($request->start_rest as $id => $startRest) {
                    $rest = Rest::find($id);
                    if ($rest) {
                        if (empty($startRest)) {
                            $rest->delete();
                            continue;
                        }
                        $rest->start_rest = Carbon::createFromFormat('H:i', $startRest)->toTimeString();
                        $endRest = $endRests[$id] ?? null;
                        if ($endRest) {
                            $rest->end_rest = Carbon::createFromFormat('H:i', $endRest)->toTimeString();
                        }        
                        $rest->save();
                    }
                }
            } else {
                $rest = Rest::where('attendance_id', $attendance->attendance_id)->first();
                $startRest = $request->input('start_rest');
                $endRest = $request->input('end_rest');
                if (empty($startRest) && empty($endRest)) {
                    if (!empty($rest)) {
                        $rest->delete();
                    }
                    $stayTime = $commute->diffInSeconds($leave);
                    $workingTimeSeconds = floor($stayTime % 60);
                    $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                    $workingTimeHours = floor($stayTime / 3600);
                    $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);    
                    $unapprove->approveAttendance->date = $date;
                    $unapprove->approveAttendance->commute = $commuteTime;
                    $unapprove->approveAttendance->leave = $leaveTime;
                    $unapprove->approveAttendance->work_time = $workTime;
                    $unapprove->approveAttendance->break_time = null;
                    $unapprove->approveAttendance->reason = $reason;
                    $unapprove->approveAttendance->save();
                    $unapprove->update([
                        'status' => '承認待ち'
                    ]);
                
                    return redirect("/attendance/{id}?id={$unapprove->id}")->with('success', '修正しました');
                } else {
                    $startRestTime = Carbon::createFromFormat('H:i', $startRest)->toTimeString();
                    $endRestTime = Carbon::createFromFormat('H:i', $endRest)->toTimeString();
                    if (empty($rest)) {
                        Rest::create([
                            'attendance_id' => $attendance->attendance_id,
                            'start_rest' => $startRestTime,
                            'end_rest' => $endRestTime
                        ]);
                        $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->attendance_id)->get();
                        $breakTime = $restTime->first()->totalRestTime;
                        $recessTime = new Carbon($breakTime);
                        $seconds = $recessTime->hour * 3600 + $recessTime->minute * 60 + $recessTime->second;
                        $stayingTime = $commute->diffInSeconds($leave);
                        $stayTime = $stayingTime - $seconds;
                        $workingTimeSeconds = floor($stayTime % 60);
                        $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                        $workingTimeHours = floor($stayTime / 3600);
                        $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);    
                        $unapprove->approveAttendance->date = $date;
                        $unapprove->approveAttendance->commute = $commuteTime;
                        $unapprove->approveAttendance->leave = $leaveTime;
                        $unapprove->approveAttendance->work_time = $workTime;
                        $unapprove->approveAttendance->break_time = $breakTime;
                        $unapprove->approveAttendance->reason = $reason;
                        $unapprove->approveAttendance->save();
                        $unapprove->update([
                            'status' => '承認待ち'
                        ]);
                        
                        return redirect("/attendance/{id}?id={$unapprove->id}")->with('success', '修正しました');
                    } else {
                        $rest->update([
                            'start_rest' => $startRestTime,
                            'end_rest' => $endRestTime
                        ]);
                        $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->attendance_id)->get();
                        $breakTime = $restTime->first()->totalRestTime;
                        $recessTime = new Carbon($breakTime);
                        $seconds = $recessTime->hour * 3600 + $recessTime->minute * 60 + $recessTime->second;
                        $stayingTime = $commute->diffInSeconds($leave);
                        $stayTime = $stayingTime - $seconds;
                        $workingTimeSeconds = floor($stayTime % 60);
                        $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                        $workingTimeHours = floor($stayTime / 3600);
                        $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);    
                        $unapprove->approveAttendance->date = $date;
                        $unapprove->approveAttendance->commute = $commuteTime;
                        $unapprove->approveAttendance->leave = $leaveTime;
                        $unapprove->approveAttendance->work_time = $workTime;
                        $unapprove->approveAttendance->break_time = $breakTime;
                        $unapprove->approveAttendance->reason = $reason;
                        $unapprove->approveAttendance->save();
                        $unapprove->update([
                            'status' => '承認待ち'
                        ]);
                        
                        return redirect("/attendance/{id}?id={$unapprove->id}")->with('success', '修正しました');
                    }
                }
            }
            $rests = Rest::where('attendance_id', $attendance->attendance_id)->get();
            if ($rests->isEmpty()) {
                $stayTime = $commute->diffInSeconds($leave);
                $workingTimeSeconds = floor($stayTime % 60);
                $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                $workingTimeHours = floor($stayTime / 3600);
                $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);    
                $unapprove->approveAttendance->date = $date;
                $unapprove->approveAttendance->commute = $commuteTime;
                $unapprove->approveAttendance->leave = $leaveTime;
                $unapprove->approveAttendance->work_time = $workTime;
                $unapprove->approveAttendance->break_time = $breakTime;
                $unapprove->approveAttendance->reason = $reason;
                $unapprove->approveAttendance->save();
                $unapprove->update([
                    'status' => '承認待ち'
                ]);
                
                return redirect("/attendance/{id}?id={$unapprove->id}")->with('success', '修正しました');
            } else {
                $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attendance->attendance_id)->get();
                $breakTime = $restTime->first()->totalRestTime;
                $recessTime = new Carbon($breakTime);
                $seconds = $recessTime->hour * 3600 + $recessTime->minute * 60 + $recessTime->second;
                $stayingTime = $commute->diffInSeconds($leave);
                $stayTime = $stayingTime - $seconds;
                $workingTimeSeconds = floor($stayTime % 60);
                $workingTimeMinutes = floor(($stayTime % 3600) / 60);
                $workingTimeHours = floor($stayTime / 3600);
                $workTime = sprintf('%02d:%02d:%02d', $workingTimeHours, $workingTimeMinutes, $workingTimeSeconds);    
                $unapprove->approveAttendance->date = $date;
                $unapprove->approveAttendance->commute = $commuteTime;
                $unapprove->approveAttendance->leave = $leaveTime;
                $unapprove->approveAttendance->work_time = $workTime;
                $unapprove->approveAttendance->break_time = $breakTime;
                $unapprove->approveAttendance->reason = $reason;
                $unapprove->approveAttendance->save();
                $unapprove->update([
                    'status' => '承認待ち'
                ]);
                
                return redirect("/attendance/{id}?id={$unapprove->id}")->with('success', '修正しました');
            }
        }      
    }
}