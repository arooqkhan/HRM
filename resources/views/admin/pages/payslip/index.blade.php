@extends('admin.master.main')

@section('content')
<form action="{{ route('payslip.generate') }}" method="GET">
    <div class="row">
        <!-- Employee Information -->
        <div class="form-group col-md-6">
            <label for="employee">Select Employee:</label>
            <select id="employee" name="employee" class="form-control">
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Month Selection -->
        <div class="form-group col-md-6">
            <label for="month">Select Month:</label>
            <select id="month" name="month" class="form-control">
                @php
                    $currentMonth = date('n'); // Current month as a number (1-12)
                    $currentYear = date('Y'); // Current year
                @endphp

                @for ($i = 1; $i <= $currentMonth; $i++)
                    <option value="{{ $i }}" {{ old('month') == $i ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }} {{ $currentYear }}
                    </option>
                @endfor
            </select>
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-2">Generate Payslip</button>
</form>

<!-- Show matching PDFs if any -->
@if(!empty($matchingPdfs))
    <div class="mt-4">
        <h4>Payslip Document</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payslip PDF</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matchingPdfs as $pdf)
                    <tr>
                        <td>{{ basename($pdf) }}</td>
                        <td>
                            <a href="{{ asset($pdf) }}" class="btn btn-primary" target="_blank">View Document</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@elseif(isset($notFoundMessage))
    <div class="alert bg-danger mt-4">
        {{ $notFoundMessage }}
    </div>
@endif
@endsection     
