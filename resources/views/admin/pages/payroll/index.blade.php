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
            @can('create payroll')
    <a href="{{ route('payroll.create') }}" class="btn btn-success m-2">Add PayRoll</a>
    @endcan
    <table id="style-2" class="table style-2 dt-table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee Name</th>
                <th>Salary</th>
                <th>Bounus</th>
                <th>Deduction</th>
                <th>Total</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payrolls as $payroll)
    <tr>     
        <td>{{$payroll->id}}</td>   
    <td>
    @if($payroll->employee)
    <img src="{{ $payroll->employee->image }}" alt="{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
@else
    <img src="path/to/default/image.jpg" alt="Default Image" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">
@endif
            @if($payroll->employee)
    {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}
@else
    No employee assigned
@endif

        </td>
        <td>{{$payroll->salary}}</td>
        <td>{{$payroll->bonus}}</td>
        <td>{{$payroll->deduction}}</td>
        <td>{{$payroll->total}}</td>
        
        <td class="text-center">
        @can('update payroll')
            <a href="{{ route('payroll.edit', $payroll->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            <form action="{{ route('payroll.destroy', $payroll->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')

                @can('delete payroll')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to Remove this Payroll?')">
                    <i class="fas fa-trash-alt"></i>
                </button>
                @endcan
            </form>

            <a href="{{ route('payroll.show', $payroll->id) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i>
            </a>
        </td>
    </tr>
@endforeach
        </tbody>
    </table>
</div>
@endsection