<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Approve;

class ApplyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $unapproves = Approve::with(['approveAttendance', 'approveUser'])->where('user_id', $user->id)->where('status', '承認待ち')->get();
        $approves = Approve::with(['approveAttendance', 'approveUser'])->where('user_id', $user->id)->where('status', '承認済み')->get();
        
        return view('application', compact('unapproves', 'approves'));
    }
}