@extends('admin.master.main')
@section('content')

<style>
    .small-swal-popup {
        width: 250px !important;
        padding: 10px !important;
    }

    td[title] {
        position: relative;
        cursor: pointer;
    }

    td[title]:hover::after {
        content: attr(title);
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        white-space: nowrap;
        font-size: 12px;
        z-index: 10;
        pointer-events: none;
        opacity: 0.9;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="col-lg-12">
    <h4 class="m-2">Shift Schedule</h4>
    <div class="statbox widget box box-shadow">
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session('
                    success ') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    background: '#28a745',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif
        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'error',
                    title: '{{ session('
                    error ') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    background: '#dc3545', // Error background color
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif
        <div class="widget-content widget-content-area">

            @can('create attendance')
            <a href="{{ route('shift.create') }}" class="btn btn-secondary m-2">Add Shift</a>
            @endcan
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee Name</th>
                        <th>Shift Type</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Additional Duty</th>
                        <th>Note</th>
                        <th>Status</th>
                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
                        <th class="text-center">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($shifts as $shift)
                    @if($shift->employee) <!-- Check if the employee relation exists -->
                    <tr>
                        <td>{{ $shift->id }}</td>
                        <td>
                            @if(isset($shift->employee))
                            <img
                                src="{{ $shift->employee->image ? asset($shift->employee->image) : asset('images/dummy.jpg') }}"
                                alt="{{ $shift->employee->first_name ?? 'No Name' }} {{ $shift->employee->last_name ?? '' }}"
                                style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
                            {{ $shift->employee->first_name ?? 'No Name' }} {{ $shift->employee->last_name ?? '' }}
                            @else
                            No Employee
                            @endif
                        </td>
                        <td>{{ ucfirst($shift->shift_type) }}</td>
                        <td>{{ $shift->date }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}</td>

                        <td>{{ ucfirst($shift->add_duty) }}</td>

                        <td title="{{ $shift->node }}">
                            {{ Str::words($shift->node, 1, '...') }}
                        </td>

                        <td>

                            @if($shift->status == 0)
                            <badge class="badge badge-warning">Pending</badge>
                            @elseif($shift->status == 2)
                            <badge class="badge badge-danger">Rejected</badge>
                            @else
                            <badge class="badge badge-success">Accepted</badge>
                            @endif

                        </td>

                        @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
                        <td class="text-center">
                            <form action="{{ route('shift.destroy', $shift->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                @can('delete shift')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this shift?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan
                            </form>

                            <!-- <a href="{{ route('shift.show', $shift->id) }}" class="btn btn-info btn-sm view-details-btn">
                        <i class="fas fa-eye"></i>
                    </a> -->

                            @can('update shift')
                            <a href="{{ route('shift.edit', $shift->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                        </td>
                        @endif
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        @endsection