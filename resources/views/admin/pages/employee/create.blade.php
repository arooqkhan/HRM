@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">

            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12 d-flex justify-content-between align-items-center mb-2">
                    <h3>Employee Registration Form</h3>
                    <a href="{{route('employee.index')}}" class="btn btn-success">Back</a>
                </div>
            </div>

            <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputFirstName">First Name</label>
                        <input type="text" class="form-control" id="inputFirstName" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" required>
                        @if ($errors->has('first_name'))
                        <span class="text-danger">{{ $errors->first('first_name') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputLastName">Last Name</label>
                        <input type="text" class="form-control" id="inputLastName" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputContactEmail">Contact Email</label>
                        <input type="email" class="form-control" id="inputContactEmail" name="contact_email" placeholder="Contact Email" value="{{ old('contact_email') }}">
                        @if ($errors->has('contact_email'))
                        <span class="text-danger">{{ $errors->first('contact_email') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputEmployeeId">Employee ID</label>
                        <input type="text" class="form-control" id="inputEmployeeId" name="employee_id" placeholder="Employee ID" value="{{ old('employee_id', $nextEmployeeId) }}" required>
                        @if ($errors->has('employee_id'))
                        <span class="text-danger">{{ $errors->first('employee_id') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputEmail">User Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputPassword">Password</label>
                        <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password" required>
                        @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label>Gender</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="genderMale">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                            <label class="form-check-label" for="genderFemale">Female</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="gender" id="genderOther" value="other" {{ old('gender') == 'other' ? 'checked' : '' }}>
                            <label class="form-check-label" for="genderOther">Other</label>
                        </div>
                        @if ($errors->has('gender'))
                        <span class="text-danger">{{ $errors->first('gender') }}</span>
                        @endif
                    </div>

                    <div class="col-sm-6">
                        <label for="inputBranch">Branch</label>
                        <select class="form-control" id="inputBranch" name="branch" required>
                            <option value="">Select Branch</option>
                            <option value="branch 1" {{ old('branch') == 1 ? 'selected' : '' }}>Branch 1</option>
                            <option value="branch 2" {{ old('branch') == 2 ? 'selected' : '' }}>Branch 2</option>
                        </select>
                        @if ($errors->has('branch'))
                        <span class="text-danger">{{ $errors->first('branch') }}</span>
                        @endif
                    </div>


                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputSalary">Salary</label>
                        <input type="number" class="form-control" id="inputSalary" name="salary" placeholder="Salary" value="{{ old('salary') }}" required>
                        @if ($errors->has('salary'))
                        <span class="text-danger">{{ $errors->first('salary') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputNumber">Number</label>
                        <input type="text" class="form-control" id="inputNumber" name="number" placeholder="Number" value="{{ old('number') }}" required>
                        @if ($errors->has('number'))
                        <span class="text-danger">{{ $errors->first('number') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label for="inputemgrNumber">Emergency Number</label>
                        <input type="text" class="form-control" id="inputemgrNumber" name="emgr_number" placeholder="Emergency Number" value="{{ old('emgr_number') }}" required>
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
                            <option value="Main Department" {{ old('department') == 'Main Department' ? 'selected' : '' }}>Main Department</option>
                            <option value="Sub Department" {{ old('department') == 'Sub Department' ? 'selected' : '' }}>Sub Department</option>
                        </select>
                        @if ($errors->has('department'))
                        <span class="text-danger">{{ $errors->first('department') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputDesignation">Designation</label>
                        <input type="text" class="form-control" id="inputDesignation" placeholder="Enter Designation" name="designation" value="{{ old('designation') }}" required>
                        @if ($errors->has('designation'))
                        <span class="text-danger">{{ $errors->first('designation') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputEmployeeStatus">Employee Status</label>
                        <select class="form-control" id="inputEmployeeStatus" name="employee_status" required>
                            <option value="">Select Employee Status</option>
                            <option value="Probation" {{ old('employee_status') == 'Probation' ? 'selected' : '' }}>Probation</option>
                            <option value="Permanent" {{ old('employee_status') == 'Permanent' ? 'selected' : '' }}>Permanent</option>
                            <option value="0 hour contract" {{ old('employee_status') == '0 hour contract' ? 'selected' : '' }}>0 hour Contract</option>
                        </select>
                        @if ($errors->has('employee_status'))
                        <span class="text-danger">{{ $errors->first('employee_status') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputRole">Role</label>
                        <select class="form-control" id="inputRole" name="role" required>
                            <option value="">Select Role</option>
                            <option value="Accountant" {{ old('role') == 'Accountant' ? 'selected' : '' }}>Accountant</option>
                            <option value="HR" {{ old('role') == 'HR' ? 'selected' : '' }}>HR</option>
                            <option value="Employee" {{ old('role') == 'Employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                        @if ($errors->has('role'))
                        <span class="text-danger">{{ $errors->first('role') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputJoiningDate">Joining Date</label>
                        <input type="date" class="form-control" id="inputJoiningDate" name="joining_date" placeholder="Joining Date">
                        @if ($errors->has('joining_date'))
                        <span class="text-danger">{{ $errors->first('joining_date') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputWorkShift">Work Shift</label>
                        <select class="form-control" id="inputWorkShift" name="work_shift" required>
                            <option value="">Select Work Shift</option>
                            <option value="Day" {{ old('work_shift') == 'Day' ? 'selected' : '' }}>Day</option>
                            <option value="Night" {{ old('work_shift') == 'Night' ? 'selected' : '' }}>Night</option>
                        </select>
                        @if ($errors->has('work_shift'))
                        <span class="text-danger">{{ $errors->first('work_shift') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputDob">Date of Birth</label>
                        <input type="date" class="form-control" id="inputDob" name="dob" value="{{ old('dob') }}">
                        @if ($errors->has('dob'))
                        <span class="text-danger">{{ $errors->first('dob') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputCnic">NI Number</label>
                        <input type="text" class="form-control" id="inputCnic" name="ninumber" placeholder="Enter NI Number" value="{{ old('ninumber') }}">
                        @if ($errors->has('ninumber'))
                        <span class="text-danger">{{ $errors->first('ninumber') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputVisa">Visa Status</label>
                        <input type="text" class="form-control" id="inputVisa" name="visa" placeholder="Enter Visa Status" value="{{ old('visa') }}">
                        @if ($errors->has('visa'))
                        <span class="text-danger">{{ $errors->first('visa') }}</span>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <label for="inputVisaDate">Next Right To Check Date</label>
                        <input type="date" class="form-control" id="inputVisaDate" name="visadate" value="{{ old('visadate') }}">
                        @if ($errors->has('visadate'))
                        <span class="text-danger">{{ $errors->first('visadate') }}</span>
                        @endif
                    </div>
                </div>

                <div id="document-fields" class="mb-4">
                    <label for="documents" class="form-label">Add Documents (e.g., CNIC, Passport, CV):</label>
                    <div class="document-input mb-2">
                        <div class="input-group">
                            <input type="text" name="documents[]" placeholder="Enter document name (e.g., CNIC)" class="form-control">
                            <button type="button" class="btn btn-success add-document-btn">Add</button>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label for="inputAddress">Address</label>
                        <textarea class="form-control" id="inputAddress" name="address" placeholder="Enter Address">{{ old('address') }}</textarea>
                        @if ($errors->has('address'))
                        <span class="text-danger">{{ $errors->first('address') }}</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label for="profileImage">Profile Image</label>
                        <input type="file" class="form-control-file" id="profileImage" name="image" accept="image/*" onchange="previewImage(event)">
                        @if ($errors->has('profile_image'))
                        <span class="text-danger">{{ $errors->first('profile_image') }}</span>
                        @endif
                        <div class="mt-3">
                            <img id="imagePreview" src="#" alt="Image Preview" style="display: none; max-width: 200px; max-height: 200px;" />
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Back</a>

            </form>

        </div>
    </div>
</div>


<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('document-fields');

        // Add a new input field
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-document-btn')) {
                const newField = document.createElement('div');
                newField.classList.add('document-input', 'mb-2');
                newField.innerHTML = `
                    <div class="input-group">
                        <input type="text" name="documents[]" placeholder="Enter document name" class="form-control">
                        <button type="button" class="btn btn-danger remove-document-btn">Remove</button>
                    </div>
                `;
                container.appendChild(newField);
            }
        });

        // Remove an input field
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-document-btn')) {
                e.target.parentElement.parentElement.remove();
            }
        });
    });
</script>











@endsection