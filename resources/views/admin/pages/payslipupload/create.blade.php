@extends('admin.master.main')

@section('content')
    <div class="container">
        <!-- Form to upload PDFs -->
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('payslipupload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="pdfs">Upload PDF(s)</label>
                        <!-- File input to upload single or multiple PDFs -->
                        <input type="file" name="pdfs[]" class="form-control" id="pdfs" multiple>
                        @if ($errors->has('pdfs.*'))
                            <span class="text-danger">{{ $errors->first('pdfs.*') }}</span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Upload PDF</button>
                </form>
            </div>
        </div>

       
    </div>
@endsection
