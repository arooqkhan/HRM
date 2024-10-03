<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\ApiLeaveController;
use App\Http\Controllers\ApiExpenseController;
use App\Http\Controllers\ApiPayrollController;
use App\Http\Controllers\ApiAttendanceController;
use App\Http\Controllers\ApiAnnouncementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', [AuthApiController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('attendance/checkin', [ApiAttendanceController::class, 'checkIn']);
    Route::post('attendance/checkout', [ApiAttendanceController::class, 'checkOut']);

    Route::resource('leaves', ApiLeaveController::class);

    Route::get('payroll', [ApiPayrollController::class, 'showForCurrentEmployee']);

    Route::get('payslip', [ApiPayrollController::class, 'showForCurrentEmployeeSlip']);

    Route::get('payslip/{employeeId}', [ApiPayrollController::class, 'showForEmployeeById']);



    Route::get('attendance', [ApiAttendanceController::class, 'getCurrentMonthAttendance']);





    Route::resource('announcement', ApiAnnouncementController::class);

});

Route::resource('expense', ApiExpenseController::class);


