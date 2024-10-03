@extends('admin.master.main')
@section('content')


<div class="content">
  <nav class="mb-2" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
      <li class="breadcrumb-item active">Role</li>
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
                        <h4>Edit Role
                            <a href="{{ url('roles') }}" class="btn btn-danger float-end">Back</a>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('roles/'.$role->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="">Role Name</label>
                                <input type="text" name="name" value="{{ $role->name }}" class="form-control" />
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
</div>

@endsection