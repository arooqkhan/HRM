@extends('admin.master.main')

@section('content')
    <div class="container">
        <!-- Form to upload PDFs -->
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('payslipupload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="month">Select Month</label>
                        <!-- Dropdown to select a month -->
                        <select name="month" id="month" class="form-control">
                            <option value="">-- Select Month --</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        @if ($errors->has('month'))
                            <span class="text-danger">{{ $errors->first('month') }}</span>
                        @endif
                    </div>

                    <div class="form-group mt-3">
    <label for="pdfs">Upload PDF(s)</label>
    <input type="file" name="pdfs[]" class="form-control" id="pdfs" multiple>
    @if ($errors->has('pdfs'))
        <span class="text-danger">{{ $errors->first('pdfs') }}</span>
    @endif
    @if ($errors->has('pdfs.*'))
        <span class="text-danger">{{ $errors->first('pdfs.*') }}</span>
    @endif
</div>
                    <button type="submit" class="btn btn-primary mt-2">Upload PDF</button>
                    <a href="javascript:history.back()" class="btn btn-secondary mt-2">Back</a>
                </form>
            </div>
        </div>
    </div>
@endsection
