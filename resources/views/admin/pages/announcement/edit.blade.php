@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h3>Edit Announcement</h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" id="announcementForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="inputTitle">Title</label>
                                <input type="text" class="form-control" id="inputTitle" name="title" value="{{ old('title', $announcement->title) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="inputMessage">Message</label>
                                <textarea class="form-control" id="inputMessage" name="message" rows="4" required>{{ old('message', $announcement->message) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="inputDate">Date:</label>
                                <input type="date" class="form-control" id="inputDate" name="date" value="{{ old('date', $announcement->date) }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 mt-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('announcements.index') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
