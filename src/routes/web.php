<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

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

Route::middleware(['auth'])->group(function () {
    // 勤怠登録ページ
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/commute', [AttendanceController::class, 'commute']);
    Route::post('/rest/start', [AttendanceController::class, 'breakIn']);
    Route::patch('/rest/end', [AttendanceController::class, 'breakOut']);
    Route::patch('/leave', [AttendanceController::class, 'leave']);
});