@extends('admin.master.main')
@section('content')

<style>
    .small-swal-popup {
        width: 250px !important;
        padding: 10px !important;
    }

    .btn-circle {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="col-lg-12">
<h4 class="m-2">Employees Record</h4>

    <div class="statbox widget box box-shadow">
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    background: '#28a745',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    background: '#dc3545', // Error background color
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif
        <div class="widget-content widget-content-area">
            @can('create employee')
            <a href="{{ route('employee.create') }}" class="btn btn-success m-2">Add Employee</a>
            @endcan
            <table id="style-2" class="table style-2 dt-table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>User Email</th>
            <th>Designation</th>
            <th>EmployeeID</th>
            <th>Role</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($employees as $employee)
        <tr>
            <td>{{ $employee->id }}</td>
            <td>
                <span>
                    @if($employee->image)
                    <img src="{{ asset($employee->image) }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
                    @else
                    <img src="{{ asset('images/dummy.jpg') }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
                    @endif
                </span>
                {{ $employee->first_name }} {{ $employee->last_name }}
            </td>
            <td>{{ $employee->user->email }}</td>
            <td>{{ $employee->designation }}</td>
            <td>{{ $employee->employee_id }}</td>
            <td>{{ $employee->role }}</td>
            <td class="text-center">
    @can('update employee')
    <a href="{{ route('employee.edit', $employee->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i>
    </a>
    @endcan
    <form action="{{ route('employee.destroy', $employee->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        @can('delete employee')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?')">
            <i class="fas fa-trash-alt"></i>
        </button>
        @endcan
    </form>
    <button type="button" class="btn btn-info btn-sm view-details-btn" data-toggle="modal" data-target="#viewDetailsModal" data-employee="{{ json_encode($employee) }}">
        <i class="fas fa-eye"></i>
    </button>

    
    <a href="{{ route('documents.showByEmployee', $employee->id) }}" class="btn btn-info btn-sm">
        <i class="fas fa-file-alt"></i> Documents
    </a>

    <a href="{{ route('attendance.show', $employee->id) }}" class="btn btn-info btn-sm">
        <i class="fas fa-calendar-check"></i> Attendance
    </a>

    <a href="{{ route('payroll.showWithEmployee', [0, $employee->id, $employee->first_name, $employee->last_name]) }}" class="btn btn-info btn-sm">
    <i class="fas fa-dollar-sign"></i> Payroll
</a>

    

</td>

        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No employee records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
        </div>
    </div>
</div>

<!-- Modal Code -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">Employee Details</h5>
                <button type="button" class="btn btn-sm btn-primary close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="modalEmployeeImage" src="" alt="Employee Image" class="rounded-circle profile-img" style="width: 120px; height: 120px; border: 3px solid #007bff;">
                </div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item"><strong>ID:</strong> <span id="modalEmployeeId"></span></li>
                                <li class="list-group-item"><strong>Name:</strong> <span id="modalEmployeeName"></span></li>
                                <li class="list-group-item"><strong>Contact Email:</strong> <span id="modalEmployeeEmail"></span></li>
                                <li class="list-group-item"><strong>Gender:</strong> <span id="modalEmployeeGender"></span></li>
                                <li class="list-group-item"><strong>Employee ID:</strong> <span id="modalEmployeeemployeeid"></span></li>
                                <li class="list-group-item"><strong>Department:</strong> <span id="modalEmployeeDepartment"></span></li>
                                <li class="list-group-item"><strong>Designation:</strong> <span id="modalEmployeeDesignation"></span></li>
                                <li class="list-group-item"><strong>Employee Status:</strong> <span id="modalEmployeeEmployeeStatus"></span></li>
                                <li class="list-group-item"><strong>Role:</strong> <span id="modalEmployeeRole"></span></li>
                                <li class="list-group-item"><strong>Salary:</strong> <span id="modalEmployeeSalary"></span></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Number:</strong> <span id="modalEmployeeNumber"></span></li>
                                <li class="list-group-item"><strong>Emergency Number:</strong> <span id="modalEmployeeemgrNumber"></span></li>
                                <li class="list-group-item"><strong>Joining Date:</strong> <span id="modalEmployeeJoiningDate"></span></li>
                                <li class="list-group-item"><strong>Work Shift:</strong> <span id="modalEmployeeWorkShift"></span></li>
                                <li class="list-group-item"><strong>NI Number:</strong> <span id="modalEmployeeninumber"></span></li>
                                <li class="list-group-item"><strong>Date Of Birth:</strong> <span id="modalEmployeedob"></span></li>
                                <li class="list-group-item"><strong>Address:</strong><span id="modalEmployeeaddress"></span></li>
                                <li class="list-group-item"><strong>Visa Status:</strong><span id="modalEmployeevisastatus"></span></li>
                                <li class="list-group-item"><strong>Next Right to Check date:</strong><span id="modalEmployeevisadate"></span></li>
                            </ul>
                        </div>  
                    </div>
                </div>
            </div>
            <hr>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-content {
        border-radius: 15px;
    }

    .modal-header {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    .modal-title {
        font-weight: bold;
    }

    .modal-body {
        padding: 2rem;
    }

    .list-group-item {
        border: none;
        padding: 0.75rem 1.25rem;
    }

    .list-group-item strong {
        display: inline-block;
        width: 150px;
    }

    
</style>




<script>
   $(document).ready(function() {
    $('.view-details-btn').on('click', function() {
        var employee = $(this).data('employee');

        $('#modalEmployeeId').text(employee.id);
        $('#modalEmployeeName').text(employee.first_name + ' ' + employee.last_name);
        $('#modalEmployeeEmail').text(employee.contact_email);
        $('#modalEmployeeGender').text(employee.gender);
        $('#modalEmployeeemployeeid').text(employee.employee_id);
        $('#modalEmployeeDepartment').text(employee.department);
        $('#modalEmployeeDesignation').text(employee.designation);
        $('#modalEmployeeEmployeeStatus').text(employee.employee_status);
        $('#modalEmployeeRole').text(employee.role);
        $('#modalEmployeeSalary').text(employee.salary);
        $('#modalEmployeeNumber').text(employee.number);
        $('#modalEmployeeemgrNumber').text(employee.emgr_number);
        $('#modalEmployeeJoiningDate').text(employee.joining_date);
        $('#modalEmployeeWorkShift').text(employee.work_shift);
        $('#modalEmployeeninumber').text(employee.ninumber);
        $('#modalEmployeedob').text(employee.dob);
        $('#modalEmployeeaddress').text(employee.address);
        $('#modalEmployeevisastatus').text(employee.visa);
        $('#modalEmployeevisadate').text(employee.visadate);

        if (employee.image) {
            $('#modalEmployeeImage').attr('src', '{{ asset('') }}' + employee.image);
        } else {
            $('#modalEmployeeImage').attr('src', '{{ asset('images/dummy.jpg') }}');
        }
    });
});
</script>



<!-- End Modal Code -->

@endsection