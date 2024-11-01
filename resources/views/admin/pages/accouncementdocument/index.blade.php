@extends('admin.master.main')

@section('content')


<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">



<style>
    /* Modal Custom Styles */
    .modal-content {
        background-color: #f8f9fa;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background-color: #343a40;
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 20px;
        position: relative;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .close {
        color: white;
        font-size: 1.5rem;
        position: absolute;
        right: 20px;
        top: 15px;
    }

    .modal-body {
        padding: 30px;
        font-size: 1rem;
        color: #343a40;
    }

    #documentFile {
        border: 1px solid #ccc;
        border-radius: 5px;
        margin-top: 10px;
    }

    .modal-footer {
        background-color: #f8f9fa;
        padding: 20px;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
        text-align: right;
    }



    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>
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
                    background: '#dc3545',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif
        <div class="widget-content widget-content-area">
            <a href="{{ route('accouncementdocument.create') }}" class="btn btn-success m-2">Add Request Document</a>
            <table id="style-2" class="table style-2 dt-table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Employee Name</th>
            <th>Document Title</th>
            <th>Status</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($accouncementdocuments as $accouncementdocument)
        <tr>
            <td>{{ $accouncementdocument->id }}</td>
            <td>
                <span>
                    @if($accouncementdocument->employee->image)
                    <img src="{{ asset($accouncementdocument->employee->image) }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
                    @else
                    <img src="{{ asset('images/dummy.jpg') }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
                    @endif
                </span>
                {{ $accouncementdocument->employee->first_name }} {{ $accouncementdocument->employee->last_name }}
            </td>
            <td>{{ $accouncementdocument->title }}</td>

            <!-- Status column -->
            <td>
                @if($accouncementdocument->status == 0)
                <span class="badge badge-warning">Pending</span>
                @else
                <span class="badge badge-success">Uploaded</span>
                @endif
            </td>

            <td class="text-center">
                <!-- Delete button -->
                <form action="{{ route('accouncementdocument.destroy', $accouncementdocument->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this document?')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>

                <!-- Done button (only visible if status is Pending) -->
                @if($accouncementdocument->status == 0)
                <form action="{{ route('accouncementdocument.updateStatus', $accouncementdocument->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to mark this document as done?')">
                        <i class="fas fa-check"></i> Done
                    </button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection