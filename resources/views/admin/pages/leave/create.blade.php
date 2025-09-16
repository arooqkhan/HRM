@extends('admin.master.main')

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Initially hide the date and time fields
        $('.time-fields').hide();
        $('.date-fields').hide();

        // Function to toggle visibility based on radio button change
        $('input[type=radio][name=duration]').change(function() {
            if (this.value === 'Single day') {
                $('.date-time-fields').slideUp(); // Slide up all fields
                $('.date-fields').slideUp(); // Slide down date fields only
                $('.time-fields').slideUp(); // Slide up time fields if they were shown
                $('.dt-fields').slideDown(); // Slide up time fields
            } else if (this.value === 'Multi day') {
                $('.date-time-fields').slideDown(); // Slide down all fields
                $('.date-fields').slideDown(); // Slide down date fields
                $('.time-fields').slideUp(); // Slide up time fields
                $('.dt-fields').slideUp();
                $('.half-fields').slideUp();
            } else if (this.value === 'Half day') {
                $('.date-time-fields').slideUp(); // Slide down all fields
                $('.date-fields').slideUp(); // Slide down date fields
                $('.time-fields').slideUp(); // Slide up time fields
                $('.dt-fields').slideDown();
                $('.half-fields').slideDown();
            } else if (this.value === 'Hours') {
                $('.date-time-fields').slideUp(); // Slide up all fields
                $('.date-fields').slideUp(); // Slide up date fields
                $('.half-fields').slideUp();
                $('.time-fields').slideDown();
                $('.dt-fields').slideDown();
            }
        });
    });
</script>


<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">

            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h3>Assign Leave</h3>
                </div>
            </div>


            <form action="{{ route('leave.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm">
                @csrf

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputEmployeeId">Employee Name</label>
                        <select class="form-control" id="inputEmployeeId" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="inputLeaveType">Leave Type</label>
                        <select class="form-control" id="inputLeaveType" name="leave_type" required>
                            <option value="">Select Leave Type</option>
                            <option value="Medical Leave" {{ old('leave_type') == 'Medical Leave' ? 'selected' : '' }}>Medical Leave</option>
                            <option value="Vacation Leave" {{ old('leave_type') == 'Vacation Leave' ? 'selected' : '' }}>Vacation Leave</option>
                            <option value="Maternity Leave" {{ old('leave_type') == 'Maternity Leave' ? 'selected' : '' }}>Maternity Leave</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label>Duration</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="duration" id="singleDay" value="Single day" {{ old('duration', 'Single day') == 'Single day' ? 'checked' : '' }}>
                            <label class="form-check-label" for="singleDay">Single day</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="duration" id="multiDay" value="Multi day" {{ old('duration') == 'Multi day' ? 'checked' : '' }}>
                            <label class="form-check-label" for="multiDay">Multi day</label>
                        </div>
                        <!-- <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="duration" id="halfDay" value="Half day" {{ old('duration') == 'Half day' ? 'checked' : '' }}>
                            <label class="form-check-label" for="halfDay">Half day</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="duration" id="hours" value="Hours" {{ old('duration') == 'Hours' ? 'checked' : '' }}>
                            <label class="form-check-label" for="hours">Hours</label>
                        </div> -->
                    </div>
                </div>

                <div class="date-fields">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <label for="inputStartDate">Start Date</label>
                            <input type="date" class="form-control" id="inputStartDate" name="start_date" placeholder="Start Date" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="inputEndDate">End Date</label>
                            <input type="date" class="form-control" id="inputEndDate" name="end_date" placeholder="End Date" value="{{ old('end_date') }}" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="time-fields">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <label for="inputStartTime">Start Time</label>
                            <input type="time" class="form-control" id="inputStartTime" name="start_time" placeholder="Start Time" value="{{ old('start_time') }}">
                        </div>
                        <div class="col-sm-6">
                            <label for="inputEndTime">End Time</label>
                            <input type="time" class="form-control" id="inputEndTime" name="end_time" placeholder="End Time" value="{{ old('end_time') }}">
                        </div>
                    </div>
                </div>

                <div class="dt-fields">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <label for="inputDate">Date</label>
                            <input type="date" class="form-control" id="inputDate" name="date" placeholder="Date" value="{{ old('date') }}" min="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="half-fields" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="leave_duration" id="firstHalf" value="First half" {{ old('leave_duration') == 'First half' ? 'checked' : '' }}>
                                <label class="form-check-label" for="firstHalf">First half</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="leave_duration" id="secondHalf" value="Second half" {{ old('leave_duration') == 'Second half' ? 'checked' : '' }}>
                                <label class="form-check-label" for="secondHalf">Second half</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label for="inputReason">Reason</label>
                        <textarea class="form-control" id="inputReason" name="reason" rows="3" placeholder="Reason for leave" required>{{ old('reason') }}</textarea>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="inputImage">Attachments</label>
                        <input type="file" class="form-control" id="inputImage" name="image" accept="image/*">
                        <img id="imagePreview" src="#" alt="Profile Image Preview" style="display: none; max-width: 100px; max-height: 100px; margin-top: 10px;">
                        @if ($errors->has('image'))
                        <span class="text-danger">{{ $errors->first('image') }}</span>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Back</a>

            </form>



        </div>
    </div>
</div>



<script>
    // JavaScript to show preview of selected image files
    document.getElementById('inputImage').addEventListener('change', function(event) {
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
</script>



@endsection