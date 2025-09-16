<?php

use App\Models\Leave;
use App\Models\Expense;
use App\Models\Attendance;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RotaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PayRollController;
use App\Http\Controllers\PaySlipController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseConttroller;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\PayslipUploadController;
use App\Http\Controllers\AccouncementDocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('dashboard', function () {
//     return view('admin.master.main');
// });


Route::get('/test-email', function () {
    Mail::raw('This is a test email.', function ($message) {
        $message->to('farooqbsse@gmail.com')->subject('Testing Webmail SMTP');
    });
    return "Test email sent!";
});






Route::get('payslipupload/unassign', [PayslipUploadController::class, 'unassignPage'])->name('payslipupload.unassignPage');

Route::post('payslipupload/remove', [PayslipUploadController::class, 'remove'])->name('payslipupload.remove');








Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    // Dashboard route
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Resources routes
    Route::resource('employee', EmployeeController::class);

    Route::resource('document', DocumentController::class);

    Route::get('document/create/{title?}/{id?}/{first_name?}/{last_name?}', [DocumentController::class, 'creates'])->name('documents.create');

    Route::get('document/create/{title?}/{id?}/{first_name?}/{last_name?}/{docid?}', [DocumentController::class, 'edits'])->name('documents.edits');



    

    Route::get('documents/employee/{employee}', [DocumentController::class, 'showByEmployee'])->name('documents.showByEmployee');

    Route::patch('/document/{id}/update-status', [DocumentController::class, 'updateStatus'])->name('document.updateStatus');





    Route::resource('accouncementdocument',AccouncementDocumentController::class);
    Route::post('/announcementdocument/status/{id}', [AccouncementDocumentController::class, 'updateStatus'])->name('announcementdocument.updateStatus');

    Route::resource('leave', LeaveController::class);
    Route::resource('attendance', AttendanceController::class);

    Route::resource('payroll', PayRollController::class);
    Route::get('payroll/{payroll}/{employee}/{first_name}/{last_name}', [PayrollController::class, 'show'])->name('payroll.showWithEmployee');

    Route::get('showMonth', [PayrollController::class, 'showMonth'])->name('payroll.showMonth');
    Route::get('payslip/generate/{employee}/first_name/{first_name}/last_name/{last_name}', [PayrollController::class, 'generate'])->name('payslip.generate');



    
    Route::resource('payslip', PaySlipController::class)->except(['show']);
    
    
    // Route::get('payslip/generate', [PaySlipController::class, 'generate'])->name('payslip.generate');
    
    Route::get('payslip/{id}/download', [PaySlipController::class, 'download'])->name('payslip.download');
    
    
    Route::resource('payslipupload', PayslipUploadController::class);

    Route::get('payslipupload/unassign/{employee_id}/{pdf}', [PayslipUploadController::class, 'unassign'])->name('payslipupload.unassign');

  




    


    Route::resource('expenses', ExpenseController::class);


    Route::get('expenses/download/{period}', [ExpenseController::class, 'download'])->name('expenses.download');


    Route::resource('announcements', AnnouncementController::class);
    Route::get('announcementupdate/{id}', [HomeController::class, 'show'])->name('announcements.details');

   


    Route::resource('shift',ShiftController::class);

    Route::post('/shifts/{id}/accept', [ShiftController::class, 'acceptShift'])->name('shift.accept');
    Route::post('/shifts/{id}/reject', [ShiftController::class, 'rejectShift'])->name('shift.reject');

    

    Route::resource('rota',RotaController::class);

    Route::post('/rota/download', [RotaController::class, 'download'])->name('rota.download');


   

    // Custom routes for leave actions
    Route::post('leave/{id}/accept', [LeaveController::class, 'accept'])->name('leave.accept');
    Route::post('leave/{id}/reject', [LeaveController::class, 'reject'])->name('leave.reject');

    // Custom routes for attendance details
    //Route::get('attendance/details/{employee_id}', [AttendanceController::class, 'details'])->name('attendance.details');
    //Route::get('attendance/details/{employee_id}/weekly', [AttendanceController::class, 'weeklyDetails'])->name('attendance.details.weekly');
    Route::get('attendance/details/{employee_id}/monthly', [AttendanceController::class, 'monthlyDetails'])->name('attendance.details.monthly');

    Route::get('attendance/details/{employee_id}/monthly', [AttendanceController::class, 'attendanceDetailsMonthly'])
    ->name('attendance.details.monthly');

    
  

    // Payroll download route
   

    Route::get('payroll/download/{id}/{month}/{year}', [PayRollController::class, 'download'])->name('payroll.download');


    // Contacts route
    Route::get('contacts', [ContactController::class, 'index'])->name('contacts');

    // Profile update routes
    Route::get('profile/update', [HomeController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/update', [HomeController::class, 'update'])->name('profiles.update');
    Route::put('admin/profile/password', [HomeController::class, 'updatePassword'])->name('adminprofilepass');
});


Route::middleware(['role:super-admin|admin'])->group(function () {
    // Permissions routes
    Route::resource('permissions', PermissionController::class);
    Route::get('permissions/{permissionId}/delete', [PermissionController::class, 'destroy'])->name('permissions.delete');

    // Roles routes
    Route::resource('roles', RoleController::class);
    Route::get('roles/{roleId}/delete', [RoleController::class, 'destroy'])->name('roles.delete');
    Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole'])->name('roles.givePermissions');
    Route::put('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.updatePermissions');
 
    // Users routes
    Route::resource('users', UserController::class);
    Route::get('users/{userId}/delete', [UserController::class, 'destroy'])->name('users.delete');
});

// Public routes
Route::get('register', [HomeController::class, 'registerform'])->name('register');
Route::post('admin.register', [HomeController::class, 'registerData'])->name('admin.register');
Route::get('login', [HomeController::class, 'log2'])->name('login');
Route::get('/', [HomeController::class, 'log'])->name('admin.login');
Route::get('admin/login', [HomeController::class, 'log'])->name('admin.login');
Route::post('admin/login', [HomeController::class, 'login'])->name('admin.logins');
Route::get('password.request', [HomeController::class, "showForgotPasswordForm"])->name('password.request');
Route::get('reset-password', [HomeController::class, "resetPasswordForm"])->name('reset.password');
Route::post('reset-password', [HomeController::class, 'reset'])->name('password.update');
Route::get('logout', [HomeController::class, 'logout'])->name('logout');

// Admin routes
Route::get('admin', [HomeController::class, 'abcGet'])->name('admin.get');
Route::post('admin', [HomeController::class, 'abc'])->name('admin');


