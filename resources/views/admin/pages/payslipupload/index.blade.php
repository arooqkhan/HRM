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
<h4>Assigned Employees</h4>
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
                    background: '#dc3545',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif


        <div class="d-flex justify-content-end m-2 p-2">
            <a href="{{ route('payslipupload.create') }}" class="btn btn-success m-2">Upload PDF</a>
            @can('unassignPage payslipupload')
            <a href="{{ route('payslipupload.unassignPage') }}" class="btn btn-warning m-2">View Unassigned Employees</a>
            @endcan
        </div>
        
        <table id="style-2" class="table style-2 dt-table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>PDF</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employeesData as $data)
                <tr>
                    <td>{{ $data['payslip_upload_id'] }}</td>
                    <td>{{ $data['first_name'] }} {{ $data['last_name'] }}</td>
                    <td><a href="{{ asset($data['pdf']) }}" target="_blank">View PDF</a></td>
                    <td class="text-center">
                        <form action="{{ route('payslipupload.destroy', $data['payslip_upload_id']) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="pdf" value="{{ $data['pdf'] }}"> <!-- Pass the full PDF path -->
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this PDF?')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>

@endsection