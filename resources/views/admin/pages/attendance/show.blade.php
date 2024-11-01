@extends('admin.master.main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $employee->first_name }} {{ $employee->last_name }}'s Attendance Records for {{ $monthName }}</h2>
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="btn-group mb-4">
        <!-- 'This Month' Button -->
        <a href="{{ route('attendance.details.monthly', ['employee_id' => $employee->id]) }}" class="btn btn-primary {{ request()->is('attendance/details/'.$employee->id.'/monthly') ? 'active' : '' }}">This Month</a>

        <!-- 'Select Month' Label and Month Select Box with Enhanced Styling -->
        <label for="month-select" class="ms-3 me-2 align-self-center font-weight-bold">Select Month:</label>
        <form action="{{ route('attendance.details.monthly', ['employee_id' => $employee->id]) }}" method="GET" class="d-inline">
            <div class="input-group stylish-select">
                <select id="month-select" name="month" class="form-select form-select-lg" onchange="this.form.submit()" style="width: 150px;">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ $month == date('n') ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="year" value="{{ date('Y') }}">
            </div>
        </form>
    </div>

    <!-- Display attendance records -->
    <table class="table">
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Clock In Date</th>
                <th>Clock In Time</th>
                <th>Clock Out Date</th>
                <th>Clock Out Time</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @php
            $absentDaysCount = 0;
            @endphp

            @if ($attendances->isEmpty())
                <tr>
                    <td colspan="6" class="text-center">No attendance records found.</td>
                </tr>
            @else
                @php
                $startDate = \Carbon\Carbon::parse($attendances->first()->clock_in_date)->startOfMonth();
                $endDate = \Carbon\Carbon::parse($attendances->first()->clock_in_date)->endOfMonth();
                @endphp

                @while ($startDate <= $endDate && $startDate <= \Carbon\Carbon::now())
                    @php
                    $dayOfWeek = $startDate->format('l');
                    $dateString = $startDate->format('Y-m-d');
                    $found = false;
                    @endphp

                    @if (!in_array($dayOfWeek, ['Saturday', 'Sunday']))
                        @foreach($attendances as $attendance)
                            @if($attendance && $attendance->clock_in_date == $startDate->toDateString())
                                <tr>
                                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                    <td>{{ date('D, M d, Y', strtotime($attendance->clock_in_date)) }}</td>
                                    <td>{{ date('h:i A', strtotime($attendance->clock_in_time)) }}</td>
                                    <td>{{ $attendance->clock_out_date ? date('D, M d, Y', strtotime($attendance->clock_out_date)) : '-' }}</td>
                                    <td>{{ $attendance->clock_out_time ? date('h:i A', strtotime($attendance->clock_out_time)) : '-' }}</td>
                                    <td>{{ $attendance->reason }}</td>
                                </tr>
                                @php
                                $found = true;
                                break;
                                @endphp
                            @endif
                        @endforeach

                        @if (!$found)
                            <tr class="missing-row bg-danger">
                                <td colspan="6" class="text-center text-white">Absent on {{ $dayOfWeek }}, {{ $dateString }}</td>
                            </tr>
                            @php $absentDaysCount++; @endphp
                        @endif
                    @endif

                    @php $startDate->addDay(); @endphp
                @endwhile
            @endif
        </tbody>
    </table>

    <div class="mb-4">
        <h4>Total Absent Days: {{ $absentDaysCount }}</h4>
    </div>
</div>


<style>
    /* Custom Styling for Month Select Dropdown */
    .stylish-select .form-select-lg {
        font-size: 1rem;
        color: #4a4a4a;
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 0.5rem 1rem;
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: border-color 0.2s ease-in-out;
    }
    .stylish-select .form-select-lg:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
</style>


@endsection