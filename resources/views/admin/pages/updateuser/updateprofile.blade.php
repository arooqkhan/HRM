@extends('admin.master.main')

@section('content')
<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">

            <!-- Update Profile -->
            <h3 class="mb-4">Update Profile</h3>

            @if(session('success'))
            <div class="alert alert-success" id="success-alert">{{ session('success') }}</div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('profiles.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" id="name" name="name" class="form-control"
                        value="{{ Auth::user()->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ Auth::user()->email }}" required>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Profile Image:</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                @if(Auth::user()->image)
                <div class="mb-3">
                    <label class="form-label">Current Profile Image:</label><br>
                    <img alt="avatar" src="{{ asset(Auth::user()->image) }}" height="100px" width="100px"
                        class="rounded-circle border">
                </div>
                @endif

                <div class="text-center">
                    <button class="btn btn-primary px-4" type="submit">Update Profile</button>
                </div>
            </form>

        </div>
    </div>
</div>



<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">

                <!-- Update Password -->
                <h3 class="mb-4">Update Password</h3>

                <form action="{{ route('adminprofilepass') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password:</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                            class="form-control" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-4">Update Password</button>
                    </div>
                </form>

            </div>
        </div>
    </div>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const successAlert = document.getElementById("success-alert");
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.display = "none";
            }, 2000); // 2 seconds
        }
    });
</script>

@endsection