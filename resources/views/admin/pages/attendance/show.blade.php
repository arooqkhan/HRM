@extends('admin.master.main')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>{{ $employee->first_name }} {{ $employee->last_name }}'s Attendance Records for {{ $monthName }}</h2>
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="btn-group mb-4">
        <a href="{{ route('attendance.details.monthly', ['employee_id' => $employee->id]) }}" class="btn btn-primary {{ request()->is('attendance/details/'.$employee->id.'/monthly') ? 'active' : '' }}">This Month</a>

        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Previous Months
            </button>
            <ul class="dropdown-menu">
                @php
                $currentMonth = \Carbon\Carbon::now()->month;
                @endphp
                @for ($i = 1; $i < $currentMonth; $i++)
                    @php
                    $monthName = \Carbon\Carbon::now()->subMonths($i)->format('F Y');
                    @endphp
                    <li>
                        <a href="{{ route('attendance.details.previous_month', ['employee_id' => $employee->id, 'monthOffset' => $i]) }}" class="dropdown-item {{ request()->is('attendance/details/'.$employee->id.'/previous-month/'.$i) ? 'active' : '' }}">
                            {{ $monthName }}
                        </a>
                    </li>
                @endfor
            </ul>
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
            $today = \Carbon\Carbon::now()->format('Y-m-d');
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
@endsection
