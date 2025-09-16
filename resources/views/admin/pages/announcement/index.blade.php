@extends('admin.master.main')

@section('content')

<style>
    .small-swal-popup {
        width: 250px !important;
        padding: 10px !important;
    }

    .modal-content {
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e0e0e0;
    }

    .modal-header {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 15px;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
    }

    .modal-body {
        font-size: 16px;
        color: #555;
    }

    .modal-footer {
        border-top: 1px solid #e0e0e0;
        padding-top: 15px;
        justify-content: center;
    }

    .modal-footer .btn {
        border-radius: 5px;
        font-size: 14px;
        padding: 10px 20px;
    }

    .modal-dialog {
        max-width: 700px;
    }

    .modal-header .close {
        margin: -10px -10px -10px auto;
        padding: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .modal-header .close span {
        color: #999;
    }

    .modal-body p {
        margin: 0;
        padding: 10px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #138496;
        border-color: #117a8b;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="col-lg-12">
<h4>Latest Announcements</h4>
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
            <div class="d-flex justify-content-end mb-2">
            </div>
            @can('create announcements')
            <a href="{{ route('announcements.create') }}" class="btn btn-success m-2">Add Announcement</a>
            @endcan
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $announcement)
                    <tr>
                        <td>{{ $announcement->id }}</td>
                        <td>{{ $announcement->title }}</td>
                        <td>{{ Str::limit($announcement->message, 20) }}{{ strlen($announcement->message) > 20 ? '...' : '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($announcement->date)->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <!-- View Button -->
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#announcementModal-{{ $announcement->id }}">
                                <i class="fas fa-eye"></i>
                            </button>

                            <!-- Edit Button -->

                            @can('update announcements')
                            <a href="{{ route('announcements.edit', $announcement->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            <!-- Delete Form -->
                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                @can('delete announcements')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this announcement?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan
                            </form>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="announcementModal-{{ $announcement->id }}" tabindex="-1" role="dialog" aria-labelledby="announcementModalLabel-{{ $announcement->id }}" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title" id="announcementModalLabel-{{ $announcement->id }}">{{ $announcement->title }}</h5>
                                    <button type="button" class="btn btn-light close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($announcement->date)->format('Y-m-d') }}</p>
                                    <p>{{ $announcement->message }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
