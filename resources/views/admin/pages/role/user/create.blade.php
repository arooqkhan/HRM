
@extends('admin.master.main')
@section('content')
<div class="content">

    <nav class="mb-2" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active">Form</li>
        </ol>
    </nav>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">

            @if ($errors->any())
    <div id="alertMessage" class="alert alert-warning alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <script>
        // Wait until the DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Find the alert message element by ID
            const alertMessage = document.getElementById('alertMessage');

            // If the alert message exists, set a timeout to hide it after 5 seconds
            if (alertMessage) {
                setTimeout(function () {
                    alertMessage.classList.remove('show'); // Remove 'show' class to hide the alert
                    alertMessage.classList.add('fade'); // Add 'fade' class for CSS transition
                }, 5000); // 5000 milliseconds = 5 seconds
            }
        });
    </script>
@endif


                <div class="card">
                    <div class="card-header">
                        <h4>Create User
                            <a href="{{ url('users') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('users') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="">Name</label>
                                <input type="text" name="name" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label for="">Email</label>
                                <input type="text" name="email" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label for="">Password</label>
                                <input type="text" name="password" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <label for="">Roles</label>
                                <select name="role[]" class="form-control" multiple>
                                    <option value="">Select Role</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection