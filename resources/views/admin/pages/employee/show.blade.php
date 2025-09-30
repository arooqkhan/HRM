@extends('admin.master.main')
@section('content')

<div class="statbox widget box box-shadow">
    <div class="widget-content widget-content-area p-3">
        <a href="{{ route('employee.index') }}" class="btn btn-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Employees
        </a>

        <div class="card shadow-sm">
            <div class="card-header  text-white">
                <h4>Employee Details: {{ $employee->first_name }} {{ $employee->last_name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    
                    <!-- Left Column -->
                    <div class="col-md-4 text-center mb-3">
                        <img src="{{ $employee->image ? asset($employee->image) : asset('images/dummy.jpg') }}" 
                             class="img-fluid rounded-circle mb-3" 
                             alt="Employee Image" style="width: 180px; height: 180px; object-fit: cover;">
                        <ul class="list-group text-left">
                            <li class="list-group-item"><strong>ID:</strong> {{ $employee->id }}</li>
                            <li class="list-group-item"><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $employee->user->email }}</li>
                            <li class="list-group-item"><strong>Role:</strong> {{ $employee->role }}</li>
                            <li class="list-group-item"><strong>Employee ID:</strong> {{ $employee->employee_id }}</li>
                        </ul>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Department:</strong> {{ $employee->department ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Designation:</strong> {{ $employee->designation }}</li>
                                    <li class="list-group-item"><strong>Status:</strong> {{ $employee->employee_status ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Branch:</strong> {{ $employee->branch ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Salary:</strong> {{ $employee->salary ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Work Shift:</strong> {{ $employee->work_shift ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Joining Date:</strong> {{ $employee->joining_date ?? '-' }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-3">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Contact Number:</strong> {{ $employee->number ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Emergency Number:</strong> {{ $employee->emergency_number ?? '-' }}</li>
                                    <li class="list-group-item"><strong>NI Number:</strong> {{ $employee->ni_number ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Date of Birth:</strong> {{ $employee->dob ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Address:</strong> {{ $employee->address ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Visa Status:</strong> {{ $employee->visa_status ?? '-' }}</li>
                                    <li class="list-group-item"><strong>Next Check Date:</strong> {{ $employee->visa_date ?? '-' }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
