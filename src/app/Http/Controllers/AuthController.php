<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->hasRole('admin')) {
                return redirect()->intended('/admin/attendance/list');
            }

            return redirect()->intended('/attendance');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}