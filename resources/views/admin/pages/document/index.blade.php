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
            <a href="{{ route('document.create') }}" class="btn btn-success m-2">Add Document</a>
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Name</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
    @foreach($documents as $document)
    <tr>
        <td>{{ $document->id }}</td>
        <td>
            @if($document->employee_first_name && $document->employee_last_name)
                <div class="d-flex align-items-center">
                    <img src="{{ asset($document->employee_image) }}" alt="{{ $document->employee_first_name }} {{ $document->employee_last_name }}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                    {{ $document->employee_first_name }} {{ $document->employee_last_name }}
                </div>
            @else
                No employee
            @endif
        </td>
        <td>{{ $document->name }}</td>
        <td>
            <a href="{{ asset($document->document) }}" target="_blank" rel="noopener noreferrer">
                <i class="fa fa-file-alt"></i> {{ $document->name }}
            </a>
        </td>
        <td class="text-center">
            @if($document->status == 0)
                <button class="btn btn-success btn-sm" onclick="updateStatus({{ $document->id }}, 1)">Accept</button>
                <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $document->id }}, 2)">Reject</button>
            @elseif($document->status == 1)
                <span class="badge badge-success">Accepted</span>
            @elseif($document->status == 2)
                <span class="badge badge-danger">Rejected</span>
            @endif
        </td>
        <td class="text-center">
            <a href="{{ route('document.edit', $document->id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i>
            </a>
            <button href="javascript:void(0);" class="btn btn-info btn-sm" onclick="viewDocument('{{ $document->name }}', '{{ asset($document->document) }}')">
                <i class="fas fa-eye"></i>
        </button>
            <form action="{{ route('document.destroy', $document->id) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this document?')">
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
</div>

<!-- Modal for Viewing Document -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Document Details</h5>
                <button type="button" class="close" data-dismiss="modal" onclick="$('#documentModal').modal('hide');" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6 id="documentName"></h6>
                <embed id="documentFile" type="application/pdf" width="100%" height="500px" />
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" onclick="$('#documentModal').modal('hide');">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    function updateStatus(documentId, status) {
        if (confirm('Are you sure you want to update the status?')) {
            $.ajax({
                url: '{{ route("document.update.status") }}', // The route to handle status update
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: documentId,
                    status: status
                },
                success: function(response) {
                    if(response.success) {
                        location.reload(); // Reload the page to reflect the status update
                    } else {
                        alert('Failed to update the status.');
                    }
                },
                error: function(error) {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    }
</script>




<script>
    
    function viewDocument(name, url) {
        document.getElementById('documentName').textContent = name;
        document.getElementById('documentFile').src = url;
        $('#documentModal').modal('show');
    }
</script>

@endsection
