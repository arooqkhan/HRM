@extends('admin.master.main')
@section('content')

<style>
    .small-swal-popup {
        width: 250px !important;
        padding: 10px !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="col-lg-12">
<h4 class="m-2">Attendance Records</h4>
    <div class="statbox widget box box-shadow">
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
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
                    title: '{{ session('error') }}',
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
            <a href="{{ route('attendance.create') }}" class="btn btn-secondary m-2">Add Attendance</a>
            @endcan
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>

                        <th>ID</th>
                        <th>Employee Name</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $displayedEmployees = [];
                    @endphp

                    @foreach($attendances as $attendance)
                    @if($attendance->employee && !in_array($attendance->employee->id, $displayedEmployees))
                    @php
                    $displayedEmployees[] = $attendance->employee->id;
                    @endphp
                    <tr>
                    
                        <td>{{$attendance->id}}</td>
                        <td>
    <span>
        @if($attendance->employee && $attendance->employee->image)
            <img src="{{ asset($attendance->employee->image) }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
        @else
            <img src="{{ asset('images/dummy.jpg') }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
        @endif
    </span>
    @if($attendance->employee && $attendance->employee->first_name && $attendance->employee->last_name)
        {{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}
    @else
        No Employee
    @endif
</td>

                        <td class="text-center">
                            <!--
                <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                -->
                            <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')

                                @can('delete attendance')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to Remove this Attendance?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan

                            </form>

                            <a href="{{ route('attendance.show', $attendance->employee->id) }}" class="btn btn-info btn-sm view-details-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endif
                    @endforeach

                </tbody>
            </table>
        </div>
        @endsection