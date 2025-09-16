<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiRotaController;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\ApiLeaveController;
use App\Http\Controllers\ApiShiftController;
use App\Http\Controllers\ApiExpenseController;
use App\Http\Controllers\ApiPayRollController;
use App\Http\Controllers\ApiPayslipController;
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

    Route::post('attendance/checkinlocation', [ApiAttendanceController::class, 'checkIn']);
    Route::post('attendance/checkout', [ApiAttendanceController::class, 'checkOut']);

    Route::resource('leaves', ApiLeaveController::class);

    Route::get('payroll', [ApiPayRollController::class, 'showForCurrentEmployee']);

    Route::get('payslip', [ApiPayRollController::class, 'showForCurrentEmployeeSlip']);

    Route::get('payslip/{employeeId}', [ApiPayrollController::class, 'showForEmployeeById']);



    Route::get('attendance', [ApiAttendanceController::class, 'getCurrentMonthAttendance']);





    Route::resource('announcement', ApiAnnouncementController::class);


    Route::get('shifts', [ApiShiftController::class, 'index']);

    Route::get('rotas', [ApiRotaController::class, 'index']);

 

    Route::get('payslip-uploads', [ApiPayslipController::class, 'getPayslipUploads']);


    
});

Route::post('shift/accept/{id}', [ApiRotaController::class, 'acceptShift']);

Route::post('shift/reject/{id}', [ApiRotaController::class, 'rejectShift']);

Route::resource('expense', ApiExpenseController::class);




