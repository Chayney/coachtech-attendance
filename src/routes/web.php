<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\ApplyController;

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
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    // 勤怠登録ページ
    Route::get('/attendance', [WorkController::class, 'index']);
    Route::post('/commute', [WorkController::class, 'commute']);
    Route::post('/rest/start', [RestController::class, 'breakIn']);
    Route::patch('/rest/end', [RestController::class, 'breakOut']);
    Route::patch('/leave', [WorkController::class, 'leave']);

    // 勤怠一覧ページ
    Route::get('/attendance/list', [WorkController::class, 'show']);

    // 勤怠詳細ページ
    Route::get('/attendance/{id}', [WorkController::class, 'detail']);
    Route::patch('/attendance/update', [WorkController::class, 'update']);

    // 申請一覧ページ
    Route::get('/stamp_correction_request/list', [ApplyController::class, 'index']);
});