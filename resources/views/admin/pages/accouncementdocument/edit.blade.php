@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <h3>Edit Document</h3>
                </div>
            </div>

            <form action="{{ route('document.update', $document->id) }}" method="POST" id="documentForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="inputName">Name</label>
                            <input type="text" class="form-control" id="inputName" name="name" placeholder="Document Name" value="{{ old('name', $document->name) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Check if the logged-in user is an admin, HR, or Accountant -->
                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant')
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="employee_id">Select Employee</label>
                            <select class="form-control" id="employee_id" name="employee_id">
                                <option value="" disabled selected>Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $document->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->first_name }} {{$employee->last_name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="inputDocument">Document:</label>
                            <input type="file" class="form-control" id="inputDocument" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <div id="documentPreview" class="mt-2">
                                @if($document->document)
                                    <div>
                                        <strong>Current Document:</strong><br>
                                        @if(strpos($document->document, '.pdf') !== false)
                                            <a href="{{ asset($document->document) }}" target="_blank" class="btn btn-info">View PDF</a>
                                        @else
                                            <a href="{{ asset($document->document) }}" download class="btn btn-info">Download</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 mt-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('inputDocument').addEventListener('change', function(event) {
        var documentPreview = document.getElementById('documentPreview');
        documentPreview.innerHTML = ''; // Clear any existing preview

        var files = event.target.files;
        if (files && files[0]) {
            var file = files[0];
            var fileType = file.type;
            var fileName = file.name;
            var fileURL = URL.createObjectURL(file);

            var preview = document.createElement('div');
            preview.innerHTML = `<strong>Selected File:</strong> ${fileName}<br>`;

            // For PDF files, provide a link to view the file
            if (fileType === 'application/pdf') {
                preview.innerHTML += `<a href="${fileURL}" target="_blank" class="btn btn-info">View PDF</a>`;
            } else {
                // For other document types, provide a link to download the file
                preview.innerHTML += `<a href="${fileURL}" download="${fileName}" class="btn btn-info">Download ${fileName}</a>`;
            }

            documentPreview.appendChild(preview);
        }
    });
</script>

@endsection
