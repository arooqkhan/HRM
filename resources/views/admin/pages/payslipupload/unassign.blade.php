@extends('admin.master.main')

@section('content')

<style>
    .dropdown-menu {
        background-color: white !important;
        max-height: 300px;
        /* Maximum height for the dropdown */
        overflow-y: auto;
        /* Scroll if content exceeds max height */
        width: auto;
        /* Adjust width based on content */
        min-width: 200px;
        /* Minimum width for dropdown */
        position: absolute;
        z-index: 1000;
    }

    .dropdown-item {
        white-space: nowrap;
        /* Prevent text wrapping */
    }
</style>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery (required by Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (required for dropdown) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<div class="col-lg-12">
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
          <div class="text-right mb-3">
              <a href="{{ route('payslipupload.index') }}" class="btn btn-success">Back</a>
          </div>
            <h4>Unassigned Employees</h4>
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unassignedEmployees as $employee)
                    <tr>
                        <td>{{ $employee->employee_id }}</td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>
                            <!-- Upload PDF button -->
                            <a href="{{ route('payslipupload.create') }}" class="btn btn-primary">Upload PDF</a>

                            <!-- Unassign Document Button with Dropdown -->
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    Unassign Document
                                </button>
                                <ul class="dropdown-menu">
                                    @if(isset($unassignedPdfsByEmployee[$employee->employee_id]) && count($unassignedPdfsByEmployee[$employee->employee_id]) > 0)
                                    @foreach($unassignedPdfsByEmployee[$employee->employee_id] as $pdf)
                                    <li>
                                        <form action="{{ route('payslipupload.remove') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="employee_id" value="{{ $employee->employee_id }}">
                                            <input type="hidden" name="pdf" value="{{ basename($pdf) }}">
                                            <button type="submit" class="dropdown-item">
                                                {{ basename($pdf) }}
                                            </button>
                                        </form>
                                    </li>
                                    @endforeach
                                    @else
                                    <li><span class="dropdown-item">No unassigned PDFs</span></li>
                                    @endif
                                </ul>




                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection