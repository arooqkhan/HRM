@extends('admin.master.main')

@section('content')

<style>
    .small-swal-popup {
        width: 250px !important;
        padding: 10px !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="expenseModalLabel">Expense Details</h5>
        <button type="button" class="btn btn-light close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="expenseName">Name</label>
          <input type="text" class="form-control" id="expenseName" readonly>
        </div>
        <div class="form-group">
          <label for="expensePrice">Price</label>
          <input type="text" class="form-control" id="expensePrice" readonly>
        </div>
        <div class="form-group">
          <label for="expenseDate">Date</label>
          <input type="text" class="form-control" id="expenseDate" readonly>
        </div>
        <div class="form-group">
          <label for="expenseImage">Image</label>
          <img id="expenseImage" src="" alt="Expense Image" class="img-fluid rounded" style="max-width: 100%;">
        </div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="col-lg-12">
<h4>Company Expenses</h4>
    <div class="statbox widget box box-shadow">
        @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    background: '#28a745',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif

        @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    position: 'bottom-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    toast: true,
                    background: '#dc3545',
                    customClass: {
                        popup: 'small-swal-popup'
                    }
                });
            });
        </script>
        @endif

        <div class="widget-content widget-content-area">
            <a href="{{ route('expenses.create') }}" class="btn btn-success m-2">Add Expense</a>
            <table id="style-2" class="table style-2 dt-table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Date</th>
                        <th>Image</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense->id }}</td>
                        <td>{{ $expense->name }}</td>
                        <td>{{ number_format($expense->price, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}</td>
                        <td>
                            @if($expense->image)
                            <img src="{{ asset($expense->image) }}" class="rounded-circle profile-img" alt="Expense Image" style="width: 50px; height: 50px; margin-right: 10px;">
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-info btn-sm view-expense" 
                                    data-id="{{ $expense->id }}" 
                                    data-name="{{ $expense->name }}" 
                                    data-price="{{ number_format($expense->price, 2) }}" 
                                    data-date="{{ \Carbon\Carbon::parse($expense->date)->format('Y-m-d') }}" 
                                    data-image="{{ asset($expense->image) }}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this expense?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.view-expense', function() {
        var name = $(this).data('name');
        var price = $(this).data('price');
        var date = $(this).data('date');
        var image = $(this).data('image');

        $('#expenseName').val(name);
        $('#expensePrice').val(price);
        $('#expenseDate').val(date);
        $('#expenseImage').attr('src', image);

        $('#expenseModal').modal('show');


        $('.close').click(function() {
        $('#expenseModal').modal('hide');
    });
    });
</script>

@endsection
