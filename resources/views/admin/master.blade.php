@extends('admin.master.main')

@section('content')

<style>
    /* General Styles */
    .dashboard-container {
        padding: 20px;
    }

    .card {
        border-radius: 16px;
        overflow: hidden; /* ✅ Gradient aur content neatly cover */
        border: none;
        transition: all 0.3s ease-in-out;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        background: #fff;
    }

    .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    /* Gradient Card Header */
    .card-header {
        font-size: 18px;
        font-weight: 600;
        padding: 16px 20px;
        text-transform: uppercase;
        background: linear-gradient(45deg, #4facfe, #00f2fe);
        color: #fff;
        border-bottom: none;
    }

    .card-title {
        font-weight: 600;
        font-size: 1.05rem;
    }

    .stats-number {
        font-size: 1.8rem;
        font-weight: bold;
        color: #333;
    }

    .icon-box {
        background: #f5f7fa;
        border-radius: 12px;
        padding: 12px;
    }

    /* Table Styling */
    table th {
        background: #f0f2f5 !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    table td {
        font-size: 0.95rem;
    }

    /* Announcements */
    .announcement-card {
        border-left: 5px solid #3085d6;
    }

    .announcement-card h5 {
        font-weight: 600;
    }
</style>

<div class="dashboard-container">
    <div class="row">
        @if(Auth::user()->role == 'admin')
            <!-- Stats Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-primary">Total Employees</h5>
                                <p class="stats-number">{{ $totalEmployees }}</p>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-success">Total Salary</h5>
                                <p class="stats-number">£{{ number_format($totalSalary, 2) }}</p>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-pound-sign fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title text-danger">Total Expense</h5>
                                <p class="stats-number">£{{ number_format($totalExpense, 2) }}</p>
                            </div>
                            <div class="icon-box">
                                <i class="fas fa-file-invoice-dollar fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Shifts -->
            <div class="card mb-4">
                <div class="card-header">Employee Shifts (This Week)</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Shift Type</th>
                                    <th>Additional Duty</th>
                                    <th>Date</th>
                                    <th>Start</th>
                                    <th>End</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shift as $sh)
                                    <tr>
                                        <td>{{ $sh->employee->first_name }} {{ $sh->employee->last_name }}</td>
                                        <td>{{ $sh->shift_type }}</td>
                                        <td>{{ $sh->add_duty }}</td>
                                        <td>{{ $sh->date }}</td>
                                        <td>{{ date('h:i A', strtotime($sh->start_time)) }}</td>
                                        <td>{{ date('h:i A', strtotime($sh->end_time)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-danger fw-bold">No shifts found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $shift->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>

            <!-- Near Right to Check Date Employees -->
            <div class="card mb-4">
                <div class="card-header">Near Next Right-to-Check Employees</div>
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
                        <p class="text-center text-muted">No employees found within the next 5 days.</p>
                    @endif
                </div>
            </div>
        @else
            <!-- User Dashboard -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Welcome, {{ Auth::user()->name }}!</h5>
                            <p class="card-text">Thank you for logging in. Use the menu to access your features.</p>
                            <h5 class="mt-3 card-title">Your Bonus</h5>
                            <p class="stats-number">{{ $bonus }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Your Documents</h5>
                            <ul class="list-unstyled">
                                @if(!empty($documents))
                                    @foreach($documents as $document)
                                        <li>📄 {{ $document ?? 'Unnamed Document' }}</li>
                                    @endforeach
                                @elseif(!empty($empdoc))
                                    <li>📄 {{ $empdoc->title }}</li>
                                @else
                                    <li>No documents available.</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements -->
            @if($announcements->count() > 0)
                <h3 class="mb-3">Recent Announcements</h3>
                <div class="row">
                    @foreach($announcements as $announcement)
                        <div class="col-md-4 mb-4">
                            <div class="card announcement-card">
                                <div class="card-body">
                                    <h5>{{ $announcement->title }}</h5>
                                    <p>{{ Str::limit($announcement->message, 50) }}</p>
                                    <a href="{{ route('announcements.details', $announcement->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Document Announcements -->
            @if($announcementdocuments->count() > 0)
                <h3 class="mb-3">Document Announcements</h3>
                <div class="row">
                    @foreach($announcementdocuments as $announcement)
                        <div class="col-md-4 mb-4">
                            <div class="card announcement-card">
                                <div class="card-body">
                                    <h5>{{ $announcement->title }}</h5>
                                    <form action="{{ route('announcementdocument.updateStatus', $announcement->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-success" onclick="confirmStatusUpdate('{{ $announcement->id }}')">Mark as Read</button>
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

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmStatusUpdate(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will mark the announcement as read!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, mark it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector(`form[action*='${id}']`).submit();
            }
        });
    }
</script>
@endsection
