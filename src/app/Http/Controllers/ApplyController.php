<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class ApplyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('application');
    }
}
