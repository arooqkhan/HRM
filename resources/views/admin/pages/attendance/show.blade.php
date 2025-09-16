@extends('admin.master.main')

@section('content')


<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $employee->first_name }} {{ $employee->last_name }}'s Attendance Records for {{ $monthName }}</h2>
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-success">Back</a>
    </div>


    <div class="d-flex align-items-start mb-4">
    <div class="row w-100 align-items-start">
  
    <!-- <div class="col-2 d-flex justify-content-start" style="margin-top: 27px;">
    <a href="{{ route('attendance.details.monthly', ['employee_id' => $employee->id]) }}" 
               class="btn btn-primary w-100 shadow-sm rounded" style="padding: 12px;">
               <i class="fas fa-calendar"></i> View This Month
            </a>
    </div> -->
    
    <!-- Select Month Dropdown with 4-column width -->
    
    <div class="row">
       <form action="{{ route('attendance.details.monthly', ['employee_id' => $employee->id]) }}" method="GET" class="d-flex align-items-end">
        <div class="col-4">
            <label for="year-select" class="d-block font-weight-bold mb-1">Select Year:</label>
            <div class="input-group stylish-select mb-2">
                <select id="year-select" name="year" class="form-select" onchange="this.form.submit()" style="border-radius: 8px;">
                    @for ($i = now()->year; $i >= now()->year - 10; $i--)
                        <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="col-4" style="margin-left: 6px; margin-bottom:8px">
            <label for="month-select" class="d-block font-weight-bold mb-1">Select Month:</label>
            <div class="input-group stylish-select">
                <select id="month-select" name="month" class="form-select" onchange="this.form.submit()" style="border-radius: 8px;">
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ request('month', date('n')) == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
       </form>
    </div>



</div>
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

    @if ($attendances->isEmpty() && $paidLeaves->isEmpty())
        <tr>
            <td colspan="6" class="text-center">No attendance records found.</td>
        </tr>
    @else
        @php
        // Check if attendances are available and get the start and end dates
        if ($attendances->isNotEmpty()) {
            $startDate = \Carbon\Carbon::parse($attendances->first()->clock_in_date)->startOfMonth();
            $endDate = \Carbon\Carbon::parse($attendances->first()->clock_in_date)->endOfMonth();
        } else {
            // Fallback dates if attendances are empty
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
        }
        @endphp

        @while ($startDate <= $endDate && $startDate <= \Carbon\Carbon::now())
            @php
            $dayOfWeek = $startDate->format('l');
            $dateString = $startDate->format('Y-m-d');
            $found = false;
            @endphp

            @if (!in_array($dayOfWeek, ['Saturday', 'Sunday']))
                {{-- Check if the day is a Paid Leave --}}
                @foreach ($paidLeaves as $leave)
                    @php
                    $isSingleDayLeave = $leave->date == $dateString;
                    $isRangeLeave = $leave->date === null && $leave->start_date <= $dateString && $dateString <= $leave->end_date;
                    @endphp

                    @if ($isSingleDayLeave || $isRangeLeave)
                        <tr class="paid-leave-row bg-success">
                            <td colspan="6" class="text-center text-white">Paid Leave {{ $dayOfWeek }}, {{ $dateString }}</td>
                        </tr>
                        @php
                        $found = true;
                        break;
                        @endphp
                    @endif
                @endforeach

                {{-- Display attendance if not a Paid Leave --}}
                @if (!$found)
                    @foreach($attendances as $attendance)
                        @if($attendance->clock_in_date == $dateString)
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
                @endif

                {{-- Mark as Absent if neither Paid Leave nor Attendance found --}}
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