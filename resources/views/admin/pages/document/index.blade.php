@extends('admin.master.main')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


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
    <h4 class="ml-2">Employees Document</h4>
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
                    background: '#dc3545',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif
        <div class="widget-content widget-content-area">


            <a href="{{ route('document.create') }}" class="btn btn-secondary m-2">Add Document</a>
            <a href="{{ route('accouncementdocument.index') }}" class="btn btn-secondary m-2">Requested Document</a>



            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
            <div class="row mt-3 p-2">
                <div class="col-4">
                    <select id="employeeSelect" class="form-control" onchange="filterDocuments()">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="col-4">
                    <input type="text" id="searchTitleInput" class="form-control" placeholder="Search by Document Title" onkeyup="filterDocuments()">
                </div>
                
                <div class="col-4">
                    <input type="text"  id="searchNameInput" class="form-control" placeholder="Search by Employee Name" onkeyup="filterDocuments()">
                </div>
            </div>
            <div class="m-3">
                <label>Status:</label>
                <div>
                    <input type="radio" name="status" value="" checked onclick="filterDocuments()"> All
                    <input type="radio" name="status" value="0" onclick="filterDocuments()"> Pending
                    <input type="radio" name="status" value="1" onclick="filterDocuments()"> Accepted
                    <input type="radio" name="status" value="2" onclick="filterDocuments()"> Rejected
                </div>
            </div>
            @endif
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th style="padding-top: 0 !important;">ID</th>
                        <th>Employee Name</th>
                        <th>Document Title</th>
                        <th>Document</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="documentTableBody">
                    @foreach($documents as $document)
                    <tr data-status="{{ $document->status }}" data-employee="{{ $document->employee_id }}">
                        <td>{{ $document->id }}</td>
                        <td>
                            <span>
                                @if($document->employee && $document->employee->image)
                                <img src="{{ asset($document->employee->image) }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
                                @else
                                <img src="{{ asset('images/dummy.jpg') }}" class="rounded-circle profile-img" alt="Employee Image" style="width: 50px; height: 50px; margin-right: 10px;">
                                @endif
                            </span>
                            @if($document->employee && $document->employee->first_name && $document->employee->last_name)
                            {{ $document->employee->first_name }} {{ $document->employee->last_name }}
                            @else
                            No Employee
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
                            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
                            <button class="btn btn-success btn-sm" onclick="updateStatus({{ $document->id }}, 1)">Accept</button>
                            <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $document->id }}, 2)">Reject</button>

                            @else
                            <span class="badge badge-warning">Pending</span>
                            @endif
                            @elseif($document->status == 1)
                            <span class="badge badge-success">Accepted</span>
                            @elseif($document->status == 2)

                            <a href="{{ route('documents.edits', ['title' => $document->name, 'id' => $document->employee->id, 'first_name' => $document->employee->first_name, 'last_name' => $document->employee->last_name,'docid' =>$document->id]) }}" class="btn btn-danger">Rejected/Upload</a>

                            @endif
                        </td>
                        <td class="text-center">
                            @can('update document')
                            <a href="{{ route('document.edit', $document->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            @endcan
                            <!-- <button href="javascript:void(0);" class="btn btn-info btn-sm" onclick="viewDocument('{{ $document->name }}', '{{ asset($document->document) }}')">
                                <i class="fas fa-eye"></i>
                            </button> -->
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

<script>
    function filterDocuments() {
        const searchTitleInput = document.getElementById('searchTitleInput').value.toLowerCase();
        const searchNameInput = document.getElementById('searchNameInput').value.toLowerCase();
        const status = document.querySelector('input[name="status"]:checked').value;
        const selectedEmployee = document.getElementById('employeeSelect').value;
        const documentRows = document.querySelectorAll('#documentTableBody tr');

        documentRows.forEach(row => {
            const title = row.cells[2].textContent.toLowerCase();
            const employeeName = row.cells[1].textContent.toLowerCase();
            const rowStatus = row.dataset.status;
            const rowEmployee = row.dataset.employee;

            const titleMatch = title.includes(searchTitleInput);
            const nameMatch = employeeName.includes(searchNameInput);
            const statusMatch = status === "" || rowStatus === status;
            const employeeMatch = selectedEmployee === "" || rowEmployee === selectedEmployee;

            if (titleMatch && nameMatch && statusMatch && employeeMatch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    function updateStatus(documentId, status) {
        fetch(`/document/${documentId}/update-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure you include the CSRF token
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Network response was not ok.');
            })
            .then(data => {
                // Optionally, you could update the status on the page here
                // but since you want a page refresh, we'll skip that

                // Reload the page to reflect the changes
                location.reload();
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    }
</script>

@endsection