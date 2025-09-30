@extends('admin.master.main')
@section('content')

<style>
    .table-responsive {
        overflow-x: auto;
        /* Enable horizontal scrolling */
        -webkit-overflow-scrolling: touch;
        /* Smooth scrolling for iOS */
    }

    .table {
        min-width: 800px;
        /* Set a minimum width for the table */
    }

    .table-header-height {
        height: 70px;
        /* Adjust the height as needed */
    }

    .table-header-height th {
        vertical-align: middle;
        /* Center content vertically */
    }

    div[title] {
        position: relative;
        cursor: pointer;
    }

    div[title]:hover::after {
        content: attr(title);
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        background-color: rgba(0, 0, 0, 0.75);
        color: #fff;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10;
        pointer-events: none;
        opacity: 0.9;
    }
</style>



    <h4>Work Rota Schedule</h4>
    
        <div class="widget-content widget-content-area">
            <div class="row mb-3">
                <div class="col-12 d-flex justify-content-end">
                    <form method="POST" action="{{ route('rota.download') }}">
                        @csrf
                        <input type="hidden" name="employee_id" id="download_employee_id" value="{{ request()->get('employee_id') }}">
                        <input type="hidden" name="start_date" id="download_start_date" value="{{ request()->get('start_date') }}">
                        <input type="hidden" name="end_date" id="download_end_date" value="{{ request()->get('end_date') }}">
                        @can('download rota')
                        <button type="submit" class="btn btn-success m-2">Excel</button>
                        @endcan
                    </form>
                </div>
            </div>
            <!-- Form for selecting the view -->
            <form method="GET" class="p-2" action="{{ route('rota.index') }}" class="mb-3">
                <div class="row align-items-end mb-3">
                    @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
                    <div class="col-auto">
                        <label for="employee_id" class="form-label">Select Employee:</label>
                        <select name="employee_id" id="employee_id" class="form-control">
                            <option value="">All Employees</option>
                            @foreach($allEmployees as $employee)
                            <option value="{{ $employee->id }}" {{ request()->get('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    @else

                    @endif
                </div>
                <div class="row align-items-end mb-3">
                    <div class="col-auto">
                        <button type="submit" name="view" value="week" class="btn btn-info">Current Week</button>
                        <button type="submit" name="view" value="two_weeks" class="btn btn-secondary">Next Two Weeks</button>
                        <button type="submit" name="view" value="month" class="btn btn-primary">Current Month</button>
                    </div>
                    <div class="col-auto ms-auto">
                        <div class="form-group mb-0">
                            <label for="start_date" class="form-label">From:</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request()->get('start_date') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <label for="end_date" class="form-label">To:</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request()->get('end_date') }}">
                        </div>
                    </div>
                    <div class="col-auto mt-4">
                        <button type="submit" class="btn btn-primary">Show Custom Range</button>
                    </div>
                </div>
            </form>

            <!-- Display the date range -->
            <div class="date-range mb-3 p-2">
                @if(isset($startDate) && isset($endDate))
                <h4>Shifts from {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</h4>
                @endif
            </div>

            <!-- Table with horizontal scrolling -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr class="table-header-height">
                            <th style="background-color: orange; color: black !important;">Employee Name</th>
                            @php
                            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
                            @endphp
                            @foreach($period as $date)
                            <th style="background-color: orange; color: black !important;">{{ $date->format('l, d M') }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td class="bg-warning text-white font-weight-bold">{{ $employee->first_name }} {{ $employee->last_name }}</td>

                            @php
                            $employeeShifts = $employee->shifts->keyBy(function($shift) {
                            return \Carbon\Carbon::parse($shift->date)->format('Y-m-d');
                            });

                            $employeeLeaves = $employee->leaves;
                            @endphp

                            @foreach($period as $date)
                            @php
                            $shiftDate = $date->format('Y-m-d');
                            $shift = $employeeShifts->get($shiftDate);

                            // Enhanced logic to handle both multi-day and single-day leaves
                            $leave = $employeeLeaves->first(function($leave) use ($date) {
                            $leaveStart = \Carbon\Carbon::parse($leave->start_date ?? $leave->date);
                            $leaveEnd = \Carbon\Carbon::parse($leave->end_date ?? $leave->date);

                            // Check if the date falls within the leave period or matches a single-day leave
                            return $date->between($leaveStart, $leaveEnd) || $date->isSameDay($leaveStart);
                            });
                            @endphp

                            <td class="@if($leave) bg-danger text-white @elseif($shift) bg-success text-white @else bg-secondary text-white @endif">
                                @if($leave)
                                {{ ucfirst($leave->leave_type) }} <br>
                                {{ $leave->reason }} <br>
                                From: {{ \Carbon\Carbon::parse($leave->start_date ?? $leave->date)->format('d M Y') }} <br>
                                To: {{ \Carbon\Carbon::parse($leave->end_date ?? $leave->date)->format('d M Y') }}
                                @elseif($shift)
                                {{ ucfirst($shift->shift_type) }} <br>
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }} <br>
                                {{ ucfirst($shift->add_duty) }}
                                <br>
                                <div style="display: inline-block;" title="{{ ucfirst($shift->node) }}">
                                    {{ ucfirst(Str::words($shift->node, 4, '...')) }}
                                </div>

                                @if($shift->status == 0)
                                <div class="mt-2 shift-actions" data-shift-id="{{ $shift->id }}">
                                    <button class="btn btn-sm btn-primary accept-btn">Accept</button>
                                    <button class="btn btn-sm btn-danger reject-btn">Reject</button>
                                </div>
                                @elseif($shift->status == 1)
                                <br>
                                <span class="badge" style="background-color: green;">Accepted</span>
                                @elseif($shift->status == 2)
                                <br>
                                <span class="badge badge-danger">Rejected</span>
                                @endif
                                @else
                                No shift or leave
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.accept-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var shiftId = this.closest('.shift-actions').getAttribute('data-shift-id');
                updateShiftStatus(shiftId, 'accept');
            });
        });

        document.querySelectorAll('.reject-btn').forEach(function(button) {
            button.addEventListener('click', function() {
                var shiftId = this.closest('.shift-actions').getAttribute('data-shift-id');
                updateShiftStatus(shiftId, 'reject');
            });
        });

        function updateShiftStatus(shiftId, action) {
            var url = action === 'accept' ? `/shifts/${shiftId}/accept` : `/shifts/${shiftId}/reject`;

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'accepted' || data.status === 'rejected') {
                        var cell = document.querySelector(`[data-shift-id="${shiftId}"]`);
                        cell.innerHTML = `<span class="badge badge-${data.status === 'accepted' ? 'success' : 'danger'}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>`;
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
</script>

@endsection