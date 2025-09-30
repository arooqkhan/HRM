@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">

            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h3>Add Shift</h3>
                </div>
            </div>

            <form action="{{ route('shift.store') }}" method="POST" id="shiftForm">
                @csrf

                <!-- Employee Dropdown -->
                <div class="row mb-4">
                    <div class="col-sm-12">
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
                </div>

                <!-- Shift Type & Additional Duty -->
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="shift_type">Shift Type:</label>
                        <select class="form-control" id="shift_type" name="shift_type" required>
                            <option value="">Select Shift Type</option>
                            <option value="Morning" {{ old('shift_type') == 'Morning' ? 'selected' : '' }}>Morning</option>
                            <option value="Evening" {{ old('shift_type') == 'Evening' ? 'selected' : '' }}>Evening</option>
                            <option value="Night" {{ old('shift_type') == 'Night' ? 'selected' : '' }}>Night</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="add_duty">Additional Duty:</label>
                        <input type="text" class="form-control" placeholder="Additiona Duty..." id="add_duty" name="add_duty"
                               value="{{ old('add_duty') }}" required>
                    </div>
                </div>

                <!-- Date & Start Time -->
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" id="date" name="date"
                               value="{{ old('date') ? old('date') : date('Y-m-d') }}" required>
                    </div>
                    <div class="col-sm-6">
                        <label for="start_time">Start Time:</label>
                        <input type="time" class="form-control" id="start_time" name="start_time"
                               value="{{ old('start_time') }}" required>
                    </div>
                </div>

                <!-- End Time -->
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <label for="end_time">End Time:</label>
                        <input type="time" class="form-control" id="end_time" name="end_time"
                               value="{{ old('end_time') }}" required>
                    </div>
                

                <!-- Note -->
              
                    <div class="col-sm-6">
                        <label for="note">Note:</label>
                        <input type="text" placeholder="Note..." class="form-control" id="note" name="note"
                               value="{{ old('note') }}" required>
                    </div>
                </div>

                <!-- Submit & Back -->
                <div class="row mb-4">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-secondary">Submit</button>
                        <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>




@endsection