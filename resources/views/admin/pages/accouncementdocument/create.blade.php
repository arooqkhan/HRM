@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">

            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h3>Add Announcement Document</h3>
                </div>
            </div>

            <form action="{{ route('accouncementdocument.store') }}" method="POST" id="expenseForm" enctype="multipart/form-data">
                @csrf

                <!-- Employee Dropdown -->
                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label for="inputEmployee">Select Employee</label>
                        <select class="form-control" id="inputEmployee" name="employee_id" required>
                            <option value="" disabled selected>Select an Employee</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Document Title Input -->
                <div class="row mb-4">
                    <div class="col-sm-12">
                        <label for="inputTitle">Title</label>
                        <input type="text" class="form-control" id="inputTitle" name="title" placeholder="Document Title" value="{{ old('title') }}" required>
                    </div>
                </div>

                <!-- Submit and Back Buttons -->
                <div class="row mb-4">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


@endsection
