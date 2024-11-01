<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\PayslipUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PayslipUploadController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view payslipupload', ['only' => ['index']]);
        $this->middleware('permission:create payslipupload', ['only' => ['create','store']]);
        $this->middleware('permission:delete payslipupload', ['only' => ['destroy']]);
        $this->middleware('permission:unassignPage payslipupload', ['only' => ['unassignPage']]);
        $this->middleware('permission:remove payslipupload', ['only' => ['remove']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the logged-in user
        $loggedInUser = auth()->user();
    
        // Log user information for debugging
        \Log::info('Logged-in User:', [
            'id' => $loggedInUser->id,
            'role' => $loggedInUser->role,
            'employee_id' => $loggedInUser->employee_id, // Log employee ID for clarity
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
                    // Check if the logged-in user is an admin, HR, or Accountant
                    if (in_array($loggedInUser->role, ['admin', 'HR', 'Accountant'])) {
                        // Admin, HR, Accountant: show all payslip uploads
                        $employeesData[] = [
                            'payslip_upload_id' => $payslipUpload->id,
                            'first_name' => $employee->first_name,
                            'last_name' => $employee->last_name,
                            'pdf' => $pdfPath,
                        ];
                    } else {
                        // Regular employees: show only their own payslip uploads
                        if ($employee->id == $loggedInUser->employee_id) {
                            $employeesData[] = [
                                'payslip_upload_id' => $payslipUpload->id,
                                'first_name' => $employee->first_name,
                                'last_name' => $employee->last_name,
                                'pdf' => $pdfPath,
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
        if (in_array($loggedInUser->role, ['admin', 'HR', 'Accountant'])) {
            $unassignedEmployees = Employee::whereNotIn('id', $assignedEmployeeIds)->get();
        } else {
            // Regular employees: fetch only their own record
            $unassignedEmployees = Employee::where('id', $loggedInUser->employee_id)->get();
        }
    
        // Check the count of unassigned employees and log it
        $unassignedCount = $unassignedEmployees->count();
        \Log::info('Count of Unassigned Employees:', ['count' => $unassignedCount]);
    
        // Convert the collection to an array for logging
        $unassignedEmployeesArray = $unassignedEmployees->toArray();
        \Log::info('Unassigned Employees:', $unassignedEmployeesArray);
    
        // Pass the data to the view
        return view('admin.pages.payslipupload.index', compact('employeesData', 'unassignedEmployees'));
    }
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.payslipupload.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate that the files are PDFs and not too large
        $request->validate([
            'pdfs.*' => 'mimes:pdf|max:2048', // Validate PDF files
        ]);
    
        $pdfPaths = [];
    
        // Check if there are files uploaded
        if ($request->hasFile('pdfs')) {
            foreach ($request->file('pdfs') as $file) {
                // Generate a unique filename
                $filename = $file->getClientOriginalName();
    
                // Move the file to the 'public/pdfs' directory
                $destinationPath = public_path('pdfs');
                $file->move($destinationPath, $filename);
    
                // Store the public file path
                $pdfPaths[] = 'pdfs/' . $filename; 
            }
    
            // Save the file paths to the database
            PayslipUpload::create([
                'pdfs' => json_encode($pdfPaths), // Convert array of file paths to JSON
            ]);
        }
    
        // Redirect back with a success message
        return redirect()->route('payslipupload.index')->with('success', 'PDF(s) uploaded successfully!');
    }
    
    


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
   
    
    // Find the payslip upload by ID
    $payslipUpload = PayslipUpload::findOrFail($id);
    
    // Delete the record
    $payslipUpload->delete();

    return redirect()->route('payslipupload.index')->with('success', 'Payslip upload deleted successfully!');
}



public function unassignPage()
{
    // Fetch all employee IDs from the employees table
    $employeeIds = Employee::pluck('employee_id')->toArray();

    // Fetch all payslip uploads
    $payslipUploads = PayslipUpload::all();

    // Initialize an array to hold all PDFs and assigned employee IDs
    $allPdfs = [];
    $assignedEmployeeIds = [];

    foreach ($payslipUploads as $payslipUpload) {
        // Decode the JSON data in the 'pdfs' column
        $pdfPaths = json_decode($payslipUpload->pdfs, true);

        // Check if $pdfPaths is an array
        if (is_array($pdfPaths)) {
            // Add all PDFs to the allPdfs array
            $allPdfs = array_merge($allPdfs, $pdfPaths);

            // Extract employee IDs from PDFs
            foreach ($pdfPaths as $pdfPath) {
                $filename = basename($pdfPath);
                $employeeIdWithExtension = pathinfo($filename, PATHINFO_FILENAME); // Extract 'EMP01' from 'EMP01.pdf'
                $assignedEmployeeIds[] = $employeeIdWithExtension;
            }
        }
    }

    // Remove duplicates just in case
    $assignedEmployeeIds = array_unique($assignedEmployeeIds);

    // Filter out PDFs that are not assigned to existing employees
    $unassignedPdfs = array_filter($allPdfs, function ($pdfPath) use ($employeeIds) {
        $filename = basename($pdfPath);
        $employeeIdWithExtension = pathinfo($filename, PATHINFO_FILENAME);
        return !in_array($employeeIdWithExtension, $employeeIds);
    });

    // Fetch all employees and filter out those who have not been assigned any PDFs
    $unassignedEmployees = Employee::whereNotIn('employee_id', $assignedEmployeeIds)->get();

    // Prepare data for the view
    $unassignedPdfsByEmployee = [];

    foreach ($unassignedEmployees as $employee) {
        // Only include unassigned PDFs for each employee
        $unassignedPdfsByEmployee[$employee->employee_id] = $unassignedPdfs;
    }

    // Pass both unassigned employees and unassigned PDFs to the view
    return view('admin.pages.payslipupload.unassign', compact('unassignedEmployees', 'unassignedPdfsByEmployee'));
}



public function remove(Request $request)
{
    $employeeId = $request->input('employee_id');
    $pdfName = $request->input('pdf');
    
    // Corrected the path to match public directory storage
    $pdfPath = "pdfs/$pdfName"; 
    $formattedPdfPath = "pdfs/{$employeeId}.pdf"; 

    // Find the payslip upload record that contains the PDF
    $payslipUpload = PayslipUpload::all()->filter(function($payslipUpload) use ($pdfPath) {
        $pdfPaths = json_decode($payslipUpload->pdfs, true);
        return is_array($pdfPaths) && in_array($pdfPath, $pdfPaths);
    })->first();

    if (!$payslipUpload) {
        return redirect()->route('payslipupload.index')->with('error', 'PDF not found in any payslip upload record.');
    }

    // Decode the JSON data in the 'pdfs' column
    $pdfPaths = json_decode($payslipUpload->pdfs, true);

    // Check if the PDF exists in the current record
    if (is_array($pdfPaths) && in_array($pdfPath, $pdfPaths)) {
        // Update the PDF path to match the format
        $updatedPdfPaths = array_map(function ($path) use ($pdfPath, $formattedPdfPath) {
            return $path === $pdfPath ? $formattedPdfPath : $path;
        }, $pdfPaths);

        // Update the payslip upload record
        $payslipUpload->pdfs = json_encode(array_values($updatedPdfPaths));
        $payslipUpload->save();

        // Rename the PDF file in the public/pdfs path
        $oldFilePath = public_path($pdfPath); 
        $newFilePath = public_path($formattedPdfPath);

        if (file_exists($oldFilePath)) {
            rename($oldFilePath, $newFilePath);
        } else {
            return redirect()->route('payslipupload.index')->with('error', 'PDF file not found in storage.');
        }
    } else {
        return redirect()->route('payslipupload.index')->with('error', 'PDF not found in the selected payslip upload record.');
    }

    // Find the employee and assign the PDF
    $employee = Employee::where('employee_id', $employeeId)->first();
    if ($employee) {
        // Assuming there's a method or attribute to store assigned PDFs
        $assignedPdfs = json_decode($employee->pdfs, true) ?? [];
        if (!in_array($formattedPdfPath, $assignedPdfs)) {
            $assignedPdfs[] = $formattedPdfPath;
            // $employee->pdfs = json_encode($assignedPdfs);
            $employee->save();

            return redirect()->route('payslipupload.index')->with('success', 'PDF assigned successfully.');
        } else {
            return redirect()->route('payslipupload.index')->with('error', 'PDF already assigned to the employee.');
        }
    }

    return redirect()->route('payslipupload.index')->with('error', 'Employee not found.');
}




// public function remove(Request $request)
// {
//     $employeeId = $request->input('employee_id');
//     $pdfName = $request->input('pdf');
//     $pdfPath = "/storage/pdfs/$pdfName";
//     $formattedPdfPath = "/storage/pdfs/{$employeeId}.pdf";

//     // Find the payslip upload record that contains the PDF
//     $payslipUpload = PayslipUpload::all()->filter(function($payslipUpload) use ($pdfPath) {
//         $pdfPaths = json_decode($payslipUpload->pdfs, true);
//         return is_array($pdfPaths) && in_array($pdfPath, $pdfPaths);
//     })->first();

//     if (!$payslipUpload) {
//         return redirect()->route('payslipupload.index')->with('error', 'PDF not found in any payslip upload record.');
//     }

//     // Decode the JSON data in the 'pdfs' column
//     $pdfPaths = json_decode($payslipUpload->pdfs, true);

//     // Check if the PDF exists in the current record
//     if (is_array($pdfPaths) && in_array($pdfPath, $pdfPaths)) {
//         // Update the PDF path to match the format
//         $updatedPdfPaths = array_map(function ($path) use ($pdfPath, $formattedPdfPath) {
//             return $path === $pdfPath ? $formattedPdfPath : $path;
//         }, $pdfPaths);

//         // Update the payslip upload record
//         $payslipUpload->pdfs = json_encode(array_values($updatedPdfPaths));
//         $payslipUpload->save();

//         // Rename the PDF file in the storage path
//         $oldFilePath = str_replace('/storage', 'public', $pdfPath); // Adjust for the correct storage path
//         $newFilePath = str_replace('/storage', 'public', $formattedPdfPath);

//         if (Storage::exists($oldFilePath)) {
//             Storage::move($oldFilePath, $newFilePath);
//         } else {
//             return redirect()->route('payslipupload.index')->with('error', 'PDF file not found in storage.');
//         }
//     } else {
//         return redirect()->route('payslipupload.index')->with('error', 'PDF not found in the selected payslip upload record.');
//     }

//     // Find the employee and assign the PDF
//     $employee = Employee::where('employee_id', $employeeId)->first();
//     if ($employee) {
//         // Assuming there's a method or attribute to store assigned PDFs
//         $assignedPdfs = json_decode($employee->pdfs, true) ?? [];
//         if (!in_array($formattedPdfPath, $assignedPdfs)) {
//             $assignedPdfs[] = $formattedPdfPath;
//             // $employee->pdfs = json_encode($assignedPdfs);
//             $employee->save();

//             return redirect()->route('payslipupload.index')->with('success', 'PDF assigned successfully.');
//         } else {
//             return redirect()->route('payslipupload.index')->with('error', 'PDF already assigned to the employee.');
//         }
//     }

//     return redirect()->route('payslipupload.index')->with('error', 'Employee not found.');
// }

}
