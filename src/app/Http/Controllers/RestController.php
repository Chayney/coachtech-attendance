<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class RestController extends Controller
{
    public function breakIn(Request $request)
    {
        $user = Auth::user();
        $attend = Attendance::where('user_id', $user->id)->latest()->first();
        Rest::create([
            'attendance_id' => $attend->id,
            'start_rest' => Carbon::now()
        ]);
        
        return redirect('/attendance')->with('success', '休憩開始しました');   
    }

    public function breakOut(Request $request)
    {
        $user = Auth::user();
        $attend = Attendance::where('user_id', $user->id)->latest()->first();
        $rest = Rest::where('attendance_id', $attend->id)->latest()->first();
        $rest->update([
            'end_rest' => Carbon::now()
        ]);
        $restTime = Rest::selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(end_rest, start_rest)))) as totalRestTime')->where('attendance_id', $attend->id)->first();
        $attend->update([
            'break_time' => $restTime->totalRestTime
        ]);

        return redirect('/attendance')->with('success', '休憩終了しました');   
    }
}
