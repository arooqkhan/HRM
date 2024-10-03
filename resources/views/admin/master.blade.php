@extends('admin.master.main')

@section('content')
<div class="container">
    <div class="row">
        @if(Auth::user()->role == 'admin')
            <!-- Total Employees Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Total Employees</h5>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <p class="card-text mb-0">{{ $totalEmployees }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Salary Card -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body d-flex flex-column align-items-start">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                            <h5 class="card-title mb-0">Total Salary</h5>
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <p class="card-text mb-0">{{ number_format($totalSalary, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Attendance Chart -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h3>Absent And Present Employee</h3>
                        <div style="height: 250px; width: 400px; margin: 0 auto;">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Employees List -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h3>Employees Who Clocked In After 10:30 AM Today</h3>
                        <ul class="list-group">
                            @forelse($lateEmployees as $employee)
                                <li class="list-group-item">{{ $employee->first_name }} {{ $employee->last_name }}</li>
                            @empty
                                <li class="list-group-item">No employees clocked in after 10:30 AM today.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <!-- User Dashboard -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Welcome, {{ Auth::user()->name }}!</h5>
                        <p class="card-text">Thank you for logging in. Please use the navigation menu to access your features.</p>
                        <h5 class="card-title">Your Bonus</h5>
                        <p class="card-text">Your current bonuses are:</p>
                        
                            <h4>{{ $bonus }}</h4>
                        
                    </div>
                </div>
            </div>

            <!-- Absence Chart -->
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Your Absence in Current Month</h5>
                        <canvas id="absenceChart" style="height: 250px; width: 100%;"></canvas>
                        <p class="card-text">You have been absent for {{ $absentCount }} days this month (excluding weekends).</p>
                    </div>
                </div>
            </div>

            <!-- Recent Announcements -->
            @if($announcements->count() > 0)
    <div class="row">
        <h1>Recent Announcements</h1>
        @foreach($announcements as $announcement)
            <div class="col-md-4 mb-4"> <!-- Adjust column size as needed -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $announcement->title }}</h5>
                        <p class="card-text">{{ Str::limit($announcement->message, 10) }}{{ strlen($announcement->message) > 10 ? '...' : '' }}</p>
                        <a href="{{ route('announcements.details', $announcement->id) }}" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
        @endif

    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(Auth::user()->role == 'admin')
            // Attendance Chart
            var ctx = document.getElementById('attendanceChart').getContext('2d');
            var presentEmployees = @json($presentEmployees);
            var absentEmployees = @json($absentEmployees);

            var data = {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [{{ $totalPresent }}, {{ $totalAbsent }}],
                    backgroundColor: ['#28a745', '#dc3545'],
                    hoverBackgroundColor: ['#218838', '#c82333']
                }]
            };

            new Chart(ctx, {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var employees = label === 'Present' ? presentEmployees : absentEmployees;
                                    var employeeNames = employees.map(e => e.first_name + ' ' + e.last_name).join(', ');

                                    // Chunking names for better display
                                    var chunkSize = 30; // Adjust the chunk size based on your preference
                                    var employeeNamesChunks = employeeNames.match(new RegExp('.{1,' + chunkSize + '}', 'g')) || [];
                                    
                                    return [`${label}: ${context.raw}`].concat(employeeNamesChunks);
                                }
                            }
                        },
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        @endif

        // Absence Chart
        const absenceCtx = document.getElementById('absenceChart').getContext('2d');
        const absenceData = @json($absenceGraphData);

        new Chart(absenceCtx, {
            type: 'line',
            data: {
                labels: absenceData.map(data => data.date),
                datasets: [
                    {
                        label: 'Absent',
                        data: absenceData.map(data => data.absent),
                        backgroundColor: 'rgba(220, 53, 69, 0.5)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        fill: false,
                        tension: 0.1
                    },
                    {
                        label: 'Present',
                        data: absenceData.map(data => data.present),
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        fill: false,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: false,
                    },
                    y: {
                        stacked: false
                    }
                }   
            }
        });
    });
</script>
@endsection
