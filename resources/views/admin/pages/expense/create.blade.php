@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h3>Add Expense</h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">
                <form action="{{ route('expenses.store') }}" method="POST" id="expenseForm" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="inputName">Expense Name</label>
                                <input type="text" class="form-control" id="inputName" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="inputPrice">Price:</label>
                                <input type="number" step="0.01" class="form-control" id="inputPrice" name="price" value="{{ old('price') }}" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="inputDate">Date:</label>
                                <input type="date" class="form-control" id="inputDate" name="date" value="{{ old('date') ? old('date') : date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="inputImage">Image:</label>
                                <input type="file" class="form-control" id="inputImage" name="image" accept="image/*" required>
                                <div id="imagePreview" class="mt-2">
                                    <!-- Image preview will be shown here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 mt-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="javascript:history.back()" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('inputImage').addEventListener('change', function(event) {
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.innerHTML = ''; // Clear any existing preview

    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '10%'; // Ensure the image fits within the container
            img.style.height = 'auto'; // Maintain aspect ratio
            img.alt = 'Preview';
            imagePreview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
});
</script>

@endsection
