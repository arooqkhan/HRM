<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\PayslipUpload;

class ApiPayslipController extends Controller
{
    
    public function getPayslipUploads()
{
    // Get the logged-in user
    $loggedInUser = auth()->user();

    // Log user information for debugging
    Log::info('Logged-in User:', [
        'id' => $loggedInUser->id,
        'role' => $loggedInUser->role,
        'employee_id' => $loggedInUser->employee_id,
    ]);

    // Fetch all payslip uploads
    $payslipUploads = PayslipUpload::all();

    // Initialize arrays to hold employee data
    $employeesData = [];
    $assignedEmployeeIds = [];

    foreach ($payslipUploads as $payslipUpload) {
        // Decode the JSON data in the 'pdfs' column
        $pdfPaths = json_decode($payslipUpload->pdfs, true);

        // Iterate over each PDF path
        foreach ($pdfPaths as $pdfPath) {
            // Extract filename from the path (e.g., 'EMP01.pdf')
            $filename = basename($pdfPath);
            $employeeId = pathinfo($filename, PATHINFO_FILENAME); // Extract 'EMP01' from 'EMP01.pdf'

            // Fetch employee details based on employee_id
            $employee = Employee::where('employee_id', $employeeId)->first();

            if ($employee) {
                if (in_array($loggedInUser->role, ['admin', 'HR', 'Accountant'])) {
                    // Admin, HR, Accountant: show all payslip uploads
                    $employeesData[] = [
                        'payslip_upload_id' => $payslipUpload->id,
                        'first_name' => $employee->first_name,
                        'last_name' => $employee->last_name,
                        'pdf' => url('pdfs/' . basename($pdfPath)),
                    ];
                } else {
                    // Regular employees: show only their own payslip uploads
                    if ($employee->id == $loggedInUser->employee_id) {
                        $employeesData[] = [
                            'payslip_upload_id' => $payslipUpload->id,
                            'first_name' => $employee->first_name,
                            'last_name' => $employee->last_name,
                            'pdf' => url('pdfs/' . basename($pdfPath)),
                        ];
                    }
                }
                $assignedEmployeeIds[] = $employee->id; // Collect assigned employee IDs
            }
        }
    }

    // Log the collected assigned employee IDs for debugging
    \Log::info('Assigned Employee IDs:', $assignedEmployeeIds);

    // Fetch unassigned employees only for admin, HR, Accountant
    $unassignedEmployees = [];
    if (in_array($loggedInUser->role, ['admin', 'HR', 'Accountant'])) {
        $unassignedEmployees = Employee::whereNotIn('id', $assignedEmployeeIds)->get();
    }

    // Return the data as JSON response
    return response()->json([
        'status' => 'success',
        'data' => [
            'employeesData' => $employeesData,
            // 'unassignedEmployees' => $unassignedEmployees,
        ],
    ]);
}

}
