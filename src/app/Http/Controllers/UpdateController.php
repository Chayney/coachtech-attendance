<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\Approve;
use Carbon\Carbon;
use App\Http\Requests\AttendRequest;

class UpdateController extends Controller
{
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