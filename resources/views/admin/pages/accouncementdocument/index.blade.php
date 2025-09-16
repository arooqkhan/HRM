@extends('admin.master.main')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Modal Custom Styles */
    .modal-content { background-color: #f8f9fa; border-radius: 15px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
    .modal-header { background-color: #343a40; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; padding: 20px; }
    .modal-title { font-size: 1.5rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .close { color: white; font-size: 1.5rem; position: absolute; right: 20px; top: 15px; }
    .modal-body { padding: 30px; font-size: 1rem; color: #343a40; }
    .modal-footer { background-color: #f8f9fa; padding: 20px; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; text-align: right; }

    /* Filter Section Styles */
    .filter-container { display: flex; align-items: center; gap: 15px; padding: 15px; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
    .filter-container input[type="text"], .filter-container select { border-radius: 4px; padding: 6px; }
    .filter-container label { font-weight: bold; cursor: pointer; color: #007bff; }

    /* Profile Image Styling */
    .profile-img { width: 50px; height: 50px; margin-right: 10px; object-fit: cover; border-radius: 50%; }
</style>

<div class="col-lg-12">
<h4>Request Documents</h4>
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
                    background: '#28a745'
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
                    background: '#dc3545'
                });
            });
        </script>
        @endif

        <div class="widget-content widget-content-area">
           
            @can('create announcementsdocument')
            <a href="{{ route('accouncementdocument.create') }}" class="btn btn-success m-2">Add Request Document</a>
            @endcan
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-success">Back</a>

            <!-- Filter Section -->
            @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
            <div class="filter-container mt-4">
                <select id="employeeSelect" class="form-control">
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                    @endforeach
                </select>
                <input type="text" id="nameFilter" placeholder="Search by Name" class="form-control" style="padding: 12px;">
                <input type="radio" id="uploadedFilter" name="statusFilter">
                <label for="uploadedFilter">Show Uploaded</label>
                <input type="radio" id="pendingFilter" name="statusFilter">
                <label for="pendingFilter">Show Pending/Upload</label>
                <input type="radio" id="bothFilter" name="statusFilter" checked>
                <label for="bothFilter">Show Both</label>
            </div>
            @endif
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th style="padding-top: 0 !important;">ID</th>
                        <th>Employee Name</th>
                        <th>Document Title</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accouncementdocuments as $accouncementdocument)
                    <tr class="{{ $accouncementdocument->status == 0 ? 'status-pending' : 'status-uploaded' }}" data-employee="{{ $accouncementdocument->employee->id }}">
                        <td>{{ $accouncementdocument->id }}</td>
                        <td class="employee-name">
    <span>
        @if($accouncementdocument->employee && $accouncementdocument->employee->image)
            <img src="{{ asset($accouncementdocument->employee->image) }}" class="profile-img" alt="Employee Image">
        @else
            <img src="{{ asset('images/dummy.jpg') }}" class="profile-img" alt="Employee Image">
        @endif
    </span>
    @if($accouncementdocument->employee && $accouncementdocument->employee->first_name && $accouncementdocument->employee->last_name)
        {{ $accouncementdocument->employee->first_name }} {{ $accouncementdocument->employee->last_name }}
    @else
        No Employee
    @endif
</td>
                        <td>{{ $accouncementdocument->title }}</td>
                        <td>
                            @if($accouncementdocument->status == 0)
                            <a href="{{ route('documents.create', ['title' => $accouncementdocument->title, 'id' => $accouncementdocument->employee->id, 'first_name' => $accouncementdocument->employee->first_name, 'last_name' => $accouncementdocument->employee->last_name]) }}" class="btn btn-warning">Pending/Upload</a>

                            @else
                            <span class="badge badge-success">Uploaded</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('accouncementdocument.destroy', $accouncementdocument->id) }}" method="POST" style="display:inline;">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
    const employeeSelect = document.getElementById('employeeSelect');
    const uploadedFilter = document.getElementById('uploadedFilter');
    const pendingFilter = document.getElementById('pendingFilter');
    const bothFilter = document.getElementById('bothFilter');
    const nameFilter = document.getElementById('nameFilter');

    function filterRows() {
        const selectedEmployee = employeeSelect.value;
        const uploadedRows = document.querySelectorAll('.status-uploaded');
        const pendingRows = document.querySelectorAll('.status-pending');
        const allRows = document.querySelectorAll('tbody tr');

        // Hide all rows initially
        allRows.forEach(row => row.style.display = 'none');

        // Show rows based on the selected filter
        if (bothFilter.checked) {
            allRows.forEach(row => row.style.display = '');
        } else if (uploadedFilter.checked) {
            uploadedRows.forEach(row => row.style.display = '');
        } else if (pendingFilter.checked) {
            pendingRows.forEach(row => row.style.display = '');
        }

        // Apply employee filter
        if (selectedEmployee) {
            allRows.forEach(row => {
                if (row.dataset.employee !== selectedEmployee) {
                    row.style.display = 'none';
                }
            });
        }

        // Apply name filter
        const nameFilterValue = nameFilter.value.toLowerCase();
        if (nameFilterValue) {
            allRows.forEach(row => {
                const employeeName = row.querySelector('.employee-name').textContent.toLowerCase();
                if (!employeeName.includes(nameFilterValue)) {
                    row.style.display = 'none';
                }
            });
        }
    }

    // Add event listeners for filters
    employeeSelect.addEventListener('change', filterRows);
    uploadedFilter.addEventListener('change', filterRows);
    pendingFilter.addEventListener('change', filterRows);
    bothFilter.addEventListener('change', filterRows);
    nameFilter.addEventListener('input', filterRows);

    // Initial call to apply the default state
    filterRows();
});

</script>

@endsection
