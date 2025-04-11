<?php

use App\Http\Controllers\TimeLogController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/attendance', [TimeLogController::class, 'index'])->middleware(['auth'])->name('attendance.index');
Route::post('/attendance/clock-in', [TimeLogController::class, 'clockIn'])->name('clock-in');
Route::post('/attendance/break-in', [TimeLogController::class, 'breakIn'])->name('break-in');
Route::post('/attendance/break-out', [TimeLogController::class, 'breakOut'])->name('break-out');
Route::post('/attendance/clock-out', [TimeLogController::class, 'clockOut'])->name('clock-out');
Route::post('/attendance/add-note', [TimeLogController::class, 'addNote'])->name('attendance.add-note');

require __DIR__.'/auth.php';
