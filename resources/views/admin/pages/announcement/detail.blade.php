@extends('admin.master.main')

@section('content')

<div class="col-md-12 mb-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $announcement->title }}</h5>
            <p>{{ $announcement->message }}</p>
            <!-- Add more details if needed -->
        </div>
    </div>
</div>
<div class="col-md-4 mb-4">
<a href="{{ route('dashboard') }}" class="btn btn-primary">Back</a>
</div>

@endsection
