@extends('admin.master.main')

@section('content')
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }
    .container {
        border: 1px;
        border-radius: 5px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px; /* Added margin bottom to separate forms */
    }
    h2 {
        text-align: center;
        margin-bottom: 20px;
    }
    input[type="text"], input[type="email"], input[type="file"], input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }
    button {
        display: block;
        width: 100%;
        padding: 10px 15px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
    }
    button:hover {
        background-color: #218838;
    }
</style>

<div class="container">
    <h2>Update Profile</h2>
    @if(session('success'))
        <div class="alert bg-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert bg-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('profiles.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>

        <label for="image">Profile Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Update Profile</button>
    </form>
</div>
<div>
    <h3>Profile Image:</h3>
    <img alt="avatar" src="{{ asset(Auth::user()->image) }}" class="rounded-circle" height="100px" width="100px">
</div>

<div class="container">
    <h2>Update Password</h2>
    <form action="{{ route('adminprofilepass') }}" method="POST">
        @csrf
        @method('PUT') <!-- Add this line to specify the PUT method -->

        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="new_password_confirmation">Confirm New Password:</label>
        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>

        <button type="submit">Update Password</button>
    </form>
</div>



@endsection
