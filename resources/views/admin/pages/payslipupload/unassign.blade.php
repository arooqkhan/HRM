@extends('admin.master.main')

@section('content')

<style>
    .dropdown-menu {
        background-color: white !important;
        max-height: 300px;
        overflow-y: auto;
        width: auto;
        min-width: 200px;
        position: absolute;
        z-index: 1000;
    }

    .dropdown-item {
        white-space: nowrap;
    }
</style>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- jQuery (required by Bootstrap JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (required for dropdown) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>





<div class="col-lg-12">
<h4>Unassigned Employees</h4>
    <div class="statbox widget box box-shadow">
        <div class="widget-content widget-content-area">
            <div class="text-right mb-3">
                <a href="{{ route('payslipupload.index') }}" class="btn btn-success m-3" style="width: 80px;">Back</a>
            </div>
           
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                    <th style="padding-top: 0 !important;">ID</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unassignedEmployees as $employee)
                    <tr class="emp-row" id="employee-row-{{ $employee->employee_id }}">
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
                                        <button type="button" class="dropdown-item" onclick="unassignDocument('{{ $employee->employee_id }}', '{{ basename($pdf) }}')">
                                            {{ basename($pdf) }}
                                        </button>
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

<script>
    function unassignDocument(employeeId, pdfName) {
        $.ajax({
            url: "{{ route('payslipupload.remove') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                employee_id: employeeId,
                pdf: pdfName
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#employee-row-' + employeeId).remove();

                    // Remove the document from the dropdown
                    $('button.dropdown-item:contains("' + pdfName + '")').closest('li').remove();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('An error occurred while unassigning the document.');
            }
        });
    }
</script>


@endsection
