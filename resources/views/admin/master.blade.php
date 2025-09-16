@extends('admin.master.main')

@section('content')

<style>
    .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease-in-out;
}


    .card {
        border-radius: 12px; /* Rounded corners for the card */
        overflow: hidden; /* Smooth edges */
    }
    .card-header {
        font-size: 18px;
        font-weight: bold;
        padding: 15px;
        text-transform: uppercase;
    }
   
    #attendanceChart {
        max-width: 100%;
        max-height: 100%;
    }


</style>


<div class="container">
    <div class="row">
        @if(Auth::user()->role == 'admin')
            <!-- Total Employees Card -->
            <div class="row">
    <!-- Total Employees Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 text-primary">Total Employees</h5>
                    <i class="fas fa-users fa-2x text-secondary"></i>
                </div>
                <p class="card-text h4 mb-0 fw-bold">{{ $totalEmployees }}</p>
            </div>
        </div>
    </div>

    <!-- Total Salary Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 text-success">Total Salary</h5>
                    <i class="fas fa-pound-sign fa-2x text-secondary"></i>
                </div>
                <p class="card-text h4 mb-0 fw-bold">{{ number_format($totalSalary, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Total Expense Card -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0 text-danger">Total Expense for This month</h5>
                    <i class="fas fa-pound-sign fa-2x text-secondary"></i>
                </div>
                <p class="card-text h4 mb-0 fw-bold">{{ number_format($totalExpense, 2) }}</p>
            </div>
        </div>
    </div>
</div>


    <div class="row">
    <div class="col-12 mb-4">
    <div class="card shadow-lg">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Employee Shifts for This Week</h3>
            <i class="fas fa-clock fa-2x"></i>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle" style="border-collapse: collapse;">
                    <thead class="table-light">
                        <tr>
                            <th style="border: 1px solid #dee2e6;">Shift Name</th>
                            <th style="border: 1px solid #dee2e6;">Shift Type</th>
                            <th style="border: 1px solid #dee2e6;">Additional Duty</th>
                            <th style="border: 1px solid #dee2e6;">Date</th>
                            <th style="border: 1px solid #dee2e6;">Start Time</th>
                            <th style="border: 1px solid #dee2e6;">End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shift as $sh)
                        <tr>
                            <td style="border: 1px solid #dee2e6;">{{ $sh->employee->first_name }} {{ $sh->employee->last_name }}</td>
                            <td style="border: 1px solid #dee2e6;">{{ $sh->shift_type }}</td>
                            <td style="border: 1px solid #dee2e6;">{{ $sh->add_duty }}</td>
                            <td style="border: 1px solid #dee2e6;">{{ $sh->date }}</td>
                            <td style="border: 1px solid #dee2e6;">{{ date('h:i A', strtotime($sh->start_time)) }}</td>
                            <td style="border: 1px solid #dee2e6;">{{ date('h:i A', strtotime($sh->end_time)) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger fw-bold" style="border: 1px solid #dee2e6;">
                                No shifts found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
            {{ $shift->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
</div>



<div class="row">
            <!-- Attendance Chart -->
            <div class="col-md-6 mb-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white text-center">
            <h4>Absent and Present Employee</h4>
        </div>
        <div class="card-body text-center">
            <div style="height: 250px; width: 400px; margin: 0 auto;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
        <div class="card-footer text-muted text-center">
            Attendance Summary
        </div>
    </div>
</div>

<div class="col-md-6 mb-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white text-center">
            <h4>Near Next Right to Check Date Employees</h4>
        </div>
        <div class="card-body">
            @if(!empty($nxtRgtDate) && count($nxtRgtDate) > 0)
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nxtRgtDate as $nxtRgtDt)
                            <tr>
                                <td>{{ $nxtRgtDt->employee_id }}</td>
                                <td>{{ $nxtRgtDt->first_name }} {{ $nxtRgtDt->last_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-muted">No employees found with visa dates within the next 5 days.</p>
            @endif
        </div>
    </div>
</div>



</div>      
        @else
            <!-- User Dashboard -->
            <div class="row mb-4">
    <!-- Welcome Section -->
    <div class="col-md-6">
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

    <!-- Documents Section -->
    <div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Your Documents</h5>
            <p class="card-text">Below are the documents associated with your profile:</p>
            <ul>
                @if(!empty($documents))
                    @foreach($documents as $document)
                        <li>{{ $document ?? 'Unnamed Document' }}</li>
                    @endforeach
                @elseif(!empty($empdoc))
                    <li>{{ $empdoc->title }}</li>
                @else
                    <li>No documents available.</li>
                @endif
            </ul>
        </div>
    </div>
</div>
</div>



            <!-- Absence Chart -->
             <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Your Absence in Current Month</h5>
                        <canvas id="absenceChart" style="height: 250px; width: 100%;"></canvas>
                        <p class="card-text">You have been absent for {{ $absentCount }} days this month (excluding weekends).</p>
                    </div>
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

        <!-- Document Anouncement -->

        @if($announcementdocuments->count() > 0)
    <div class="row">
        <h1>Recent Document Announcements</h1>
        @foreach($announcementdocuments as $announcement)
            <div class="col-md-4 mb-4"> <!-- Adjust column size as needed -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $announcement->title }}</h5>
                        <form action="{{ route('announcementdocument.updateStatus', $announcement->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-success" onclick="confirmStatusUpdate('{{ $announcement->id }}')">Mark as Read</button>
                        </form>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

<script>
    function confirmStatusUpdate(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will mark the announcement as read!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                const form = document.querySelector(`form[action*='${id}']`);
                form.submit();
            }
        });
    }
</script>
@endsection
