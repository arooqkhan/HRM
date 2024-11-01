@extends('admin.master.main')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Employee</div>

                <div class="card-body">
                    @if(session('success'))
                    <div id="successMessage" class="alert bg-success alert-dismissible text-white" role="alert">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <form action="{{ route('employee.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <label for="inputFirstName">First Name</label>
                                <input type="text" class="form-control" id="inputFirstName" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required>
                                @if ($errors->has('first_name'))
                                <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputLastName">Last Name</label>
                                <input type="text" class="form-control" id="inputLastName" name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                                @if ($errors->has('last_name'))
                                <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">

                            <div class="col-sm-6">
                                <label for="inputEmail">User Email</label>
                                <input type="email" class="form-control" id="inputEmail" name="email" value="{{ old('email', $employee->user->email) }}" required>
                                @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>




                            <div class="col-sm-6">
                                <label for="inputContactEmail">Contact Email</label>
                                <input type="email" class="form-control" id="inputContactEmail" name="contact_email" value="{{ old('email', $employee->contact_email) }}" required>
                                @if ($errors->has('email'))
                                <span class="text-danger">{{ $errors->first('contact_email') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputEmployeeId">Employee ID</label>
                                <input type="text" class="form-control" id="inputEmployeeId" name="employee_id" value="{{ old('employee_id', $employee->employee_id) }}" required>
                                @if ($errors->has('employee_id'))
                                <span class="text-danger">{{ $errors->first('employee_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <label>Gender</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" {{ old('gender', $employee->gender) == 'male' ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="genderMale">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" {{ old('gender', $employee->gender) == 'female' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="genderFemale">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderOther" value="other" {{ old('gender', $employee->gender) == 'other' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="genderOther">Other</label>
                                </div>
                                @if ($errors->has('gender'))
                                <span class="text-danger">{{ $errors->first('gender') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputSalary">Salary</label>
                                <input type="number" class="form-control" id="inputSalary" name="salary" value="{{ old('salary', $employee->salary) }}" required>
                                @if ($errors->has('salary'))
                                <span class="text-danger">{{ $errors->first('salary') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputNumber">Number</label>
                                <input type="text" class="form-control" id="inputNumber" name="number" value="{{ old('number', $employee->number) }}" required>
                                @if ($errors->has('number'))
                                <span class="text-danger">{{ $errors->first('number') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
        <div class="col-sm-6">
            <label for="inputEmergencyNumber">Emergency Number</label>
            <input type="text" class="form-control" id="inputEmergencyNumber" name="emgr_number" value="{{ old('emgr_number', $employee->emgr_number) }}">
            @if ($errors->has('emgr_number'))
            <span class="text-danger">{{ $errors->first('emgr_number') }}</span>
            @endif
        </div>
    </div>


                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <label for="inputDepartment">Department</label>
                                <select class="form-control" id="inputDepartment" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Main Department" {{ old('department', $employee->department) == 'Main Department' ? 'selected' : '' }}>Main Department</option>
                                    <option value="Sub Department" {{ old('department', $employee->department) == 'Sub Department' ? 'selected' : '' }}>Sub Department</option>
                                </select>
                                @if ($errors->has('department'))
                                <span class="text-danger">{{ $errors->first('department') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputDesignation">Designation</label>
                                <input type="text" class="form-control" id="inputDesignation" name="designation" value="{{ old('designation', $employee->designation) }}" required>
                                @if ($errors->has('designation'))
                                <span class="text-danger">{{ $errors->first('designation') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <label for="inputEmployeeStatus">Employee Status</label>
                                <select class="form-control" id="inputEmployeeStatus" name="employee_status" required>
                                    <option value="">Select Status</option>
                                    <option value="Permanent" {{ old('employee_status', $employee->employee_status) == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                                    <option value="Probation" {{ old('employee_status', $employee->employee_status) == 'Probation' ? 'selected' : '' }}>Probation</option>
                                    <option value="0 hour contract" {{ old('employee_status', $employee->employee_status) == '0 hour contract' ? 'selected' : '' }}>0 Hour Contract</option>
                                </select>
                                @if ($errors->has('employee_status'))
                                <span class="text-danger">{{ $errors->first('employee_status') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputRole">Role</label>
                                <select class="form-control" id="inputRole" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="Accountant" {{ old('role', $employee->role) == 'Accountant' ? 'selected' : '' }}>Accountant</option>
                                    <option value="Hr" {{ old('role', $employee->role) == 'HR' ? 'selected' : '' }}>HR</option>
                                    <option value="Employee" {{ old('role', $employee->role) == 'Employee' ? 'selected' : '' }}>Employee</option>
                                </select>
                                @if ($errors->has('role'))
                                <span class="text-danger">{{ $errors->first('role') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <label for="inputJoiningDate">Joining Date</label>
                                <input type="date" class="form-control" id="inputJoiningDate" name="joining_date" value="{{ old('joining_date', $employee->joining_date) }}" required>
                                @if ($errors->has('joining_date'))
                                <span class="text-danger">{{ $errors->first('joining_date') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputWorkShift">Work Shift</label>
                                <select class="form-control" id="inputWorkShift" name="work_shift" required>
                                    <option value="">Select Work Shift</option>
                                    <option value="Day" {{ old('work_shift', $employee->work_shift) == 'Day' ? 'selected' : '' }}>Day</option>
                                    <option value="Night" {{ old('work_shift', $employee->work_shift) == 'Night' ? 'selected' : '' }}>Night</option>
                                    <!-- Add more options as needed -->
                                </select>
                                @if ($errors->has('work_shift'))
                                <span class="text-danger">{{ $errors->first('work_shift') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <label for="inputDob">Date of Birth</label>
                                <input type="date" class="form-control" id="inputDob" name="dob" value="{{ old('dob', $employee->dob) }}">
                                @if ($errors->has('dob'))
                                <span class="text-danger">{{ $errors->first('dob') }}</span>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label for="inputNinumber">NI Number</label>
                                <input type="text" class="form-control" id="inputNinumber" name="ninumber" placeholder="Enter NI Number" value="{{ old('cnic', $employee->ninumber) }}">
                                @if ($errors->has('ninumber'))
                                <span class="text-danger">{{ $errors->first('ninumber') }}</span>
                                @endif
                            </div>
                        </div>


                        <div class="row mb-4">
        <div class="col-sm-6">
            <label for="inputVisa">Visa Status</label>
            <input type="text" class="form-control" id="inputVisa" name="visa" placeholder="Enter Visa Status" value="{{ old('visa', $employee->visa) }}">
            @if ($errors->has('visa'))
            <span class="text-danger">{{ $errors->first('visa') }}</span>
            @endif
        </div>
        <div class="col-sm-6">
            <label for="inputVisaDate">Next Right To Check Date</label>
            <input type="date" class="form-control" id="inputVisaDate" name="visadate" value="{{ old('visadate', $employee->visadate) }}">
            @if ($errors->has('visadate'))
            <span class="text-danger">{{ $errors->first('visadate') }}</span>
            @endif
        </div>
    </div>

                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <label for="inputAddress">Address</label>
                                <textarea class="form-control" id="inputAddress" name="address" placeholder="Enter Address">{{ old('address', $employee->address) }}</textarea>
                                @if ($errors->has('address'))
                                <span class="text-danger">{{ $errors->first('address') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <label for="inputProfilePicture">Profile Picture</label>
                                <input type="file" class="form-control" id="inputProfilePicture" name="image" accept="image/*" onchange="previewImage(event)">
                                @if ($employee->image)
                                <img id="profilePicturePreview" src="{{ asset($employee->image) }}" alt="Profile Picture" style="max-width: 200px; margin-top: 10px;">
                                @else
                                <img id="profilePicturePreview" alt="Profile Picture" style="max-width: 200px; margin-top: 10px; display: none;">
                                @endif
                                @if ($errors->has('image'))
                                <span class="text-danger">{{ $errors->first('image') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('profilePicturePreview');
            output.src = reader.result;
            output.style.display = 'block'; // Ensure the image is visible
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection