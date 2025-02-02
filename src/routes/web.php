<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ログイン時のカスタムバリデーション設定
Route::post('/login', [AuthController::class, 'login']);

// 管理者専用ログインページ
Route::get('/admin/login', function () {
    return view('admin.login');
});

Route::middleware(['auth'])->group(function () {
    // 勤怠登録ページ
    Route::get('/attendance', [WorkController::class, 'index']);
    Route::post('/commute', [WorkController::class, 'commute']);
    Route::post('/rest/start', [RestController::class, 'breakIn']);
    Route::patch('/rest/end', [RestController::class, 'breakOut']);
    Route::patch('/leave', [WorkController::class, 'leave']);

    // 勤怠一覧ページ
    Route::get('/attendance/list', [WorkController::class, 'show']);

    // 勤怠詳細ページ(一般ユーザーと管理者が同様のURLを使用しているため)
    Route::get('/attendance/{id}', function(Request $request, $id) {
        if ($request->user()->hasRole('admin')) {
            return app('App\Http\Controllers\AdminController')->detail($request);
        } else {
            return app('App\Http\Controllers\WorkController')->detail($request);
        }
    });
    Route::patch('/attendance/update', [WorkController::class, 'update']);

    // 申請一覧ページ(一般ユーザーと管理者が同様のURLを使用しているため)
    Route::get('/stamp_correction_request/list', function () {
        if (Auth::user()->hasRole('admin')) {
            return app('App\Http\Controllers\AdminController')->apply();
        } else {
            return app('App\Http\Controllers\ApplyController')->index();
        }
    });
});

// 管理者専用ルート
Route::middleware(['auth', 'role:admin'])->group(function () {
    // 勤怠一覧ページ
    Route::get('/admin/attendance/list', [AdminController::class, 'index']);

    // 管理者用勤怠修正処理
    Route::patch('/admin/attendance/update', [AdminController::class, 'renew']);

    // スタッフ一覧ページ
    Route::get('/admin/staff/list', [AdminController::class, 'show']);
    
    // スタッフ別勤怠一覧ページ
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'list']);

    // 修正申請承認ページ
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AdminController::class, 'approve']);

    // 承認処理
    Route::patch('/approve/update', [AdminController::class, 'update']);

    // CSV出力処理
    Route::post('/export', [AdminController::class, 'export']);
});