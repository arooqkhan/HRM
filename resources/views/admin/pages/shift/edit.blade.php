@extends('admin.master.main')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Shift</div>

                <div class="card-body">
                    @if(session('success'))
                    <div id="successMessage" class="alert bg-success alert-dismissible text-white" role="alert">
                        <span>{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <form action="{{ route('shift.update', $shift->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="employee_id">Employee</label>
            <select class="form-control" id="employee_id" name="employee_id" required>
                <option value="">Select Employee</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $shift->employee_id == $employee->id ? 'selected' : '' }}>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="shift_type">Shift Type</label>
            <select class="form-control" id="shift_type" name="shift_type" required>
                <option value="morning" {{ $shift->shift_type == 'morning' ? 'selected' : '' }}>Morning</option>
                <option value="evening" {{ $shift->shift_type == 'evening' ? 'selected' : '' }}>Evening</option>
                <option value="night" {{ $shift->shift_type == 'night' ? 'selected' : '' }}>Night</option>
            </select>
        </div>

        <div class="form-group">
            <label for="add_duty">Additional Duty</label>
            <input type="text" class="form-control" id="add_duty" name="add_duty" value="{{ $shift->add_duty }}">
        </div>

        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ $shift->date }}" required>
        </div>

        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" class="form-control" id="start_time" name="start_time" value="{{ $shift->start_time }}" required>
        </div>

        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" class="form-control" id="end_time" name="end_time" value="{{ $shift->end_time }}" required>
        </div>

        <div class="form-group">
            <label for="end_time">Note</label>
            <input type="text" class="form-control" id="node" name="node" value="{{ $shift->node }}" required>
        </div>

<div class="form-group mt-5">

    <button type="submit" class="btn btn-primary">Update Shift</button>
    <a href="{{ route('shift.index') }}" class="btn btn-secondary">Cancel</a>
</div>
</form>





                </div>
            </div>
        </div>
    </div>
</div>

@endsection