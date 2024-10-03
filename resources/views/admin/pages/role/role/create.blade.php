@extends('admin.master.main')
@section('content')

<div class="content">
    @if ($errors->any())
    <ul class="alert alert-warning">
        @foreach ($errors->all() as $error)
        <li>{{$error}}</li>
        @endforeach
    </ul>
    @endif
    <nav class="mb-2" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Role</a></li>
            <li class="breadcrumb-item active">Form</li>
        </ol>
    </nav>
    <div class="card-header">
        <h4>
            <a href="{{ url('roles') }}" class="btn btn-danger float-end">Back</a>
        </h4>
    </div>
    <form class="mb-9" action="{{ url('roles') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row flex-between-end mb-5">
            <div class="col-auto">
                <h2 class="mb-2"> Create Role</h2>
                <h5 class="text-700 fw-semi-bold">Insert your Roles item</h5>
            </div>
        </div>
        <div class="row">


            <div class="col-12 col-xl-6">
                <label>Name</label>
                <input class="form-control mb-3" name="name" type="text" placeholder="Write Name here..." />
            </div>




        </div>
        <div class="col-auto">
            <button class="btn btn-success me-2 mb-2 mb-sm-0" name="submit" type="submit"><span class="fas fa-plus"></span>Save</button>
        </div>
    </form>

</div>

@endsection