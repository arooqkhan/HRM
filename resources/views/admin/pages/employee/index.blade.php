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

    .form-check-inline {
        margin-left: 10px;
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
                    title: '{{ session('
                    success ') }}',
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
                    title: '{{ session('
                    error ') }}',
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

            <!-- Add Employee Button -->
            @can('create employee')
            <a href="{{ route('employee.create') }}" class="btn btn-secondary m-2">Add Employee</a>
            @endcan
            <!-- Branch Filter Radio Buttons Aligned Right -->
            <div class="d-flex justify-content-end">
                <div class="form-check form-check-inline">
                    <input class="form-check-input branch-filter" type="radio" name="branch" id="branchAll" value="all" checked>
                    <label class="form-check-label" for="branchAll">All</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input branch-filter" type="radio" name="branch" id="branch1" value="branch 1">
                    <label class="form-check-label" for="branch1">Branch 1</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input branch-filter" type="radio" name="branch" id="branch2" value="branch 2">
                    <label class="form-check-label" for="branch2">Branch 2</label>
                </div>
            </div>



            <!-- Employee Table -->
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
                    <tr data-branch="{{ $employee->branch }}">
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

                            <a href="{{ route('employee.show', $employee->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>

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
                        <td colspan="7" class="text-center">No employee records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Branch Filter Script -->
<script>
    $(document).ready(function() {
        $('.branch-filter').on('change', function() {
            var selectedBranch = $(this).val().toLowerCase();

            $('table tbody tr').each(function() {
                var rowBranch = $(this).data('branch').toLowerCase();

                if (selectedBranch === 'all' || rowBranch === selectedBranch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>

@endsection