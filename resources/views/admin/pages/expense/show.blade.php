@extends('admin.master.main')

@section('content')
    <!-- Container for displaying expenses -->
    <div class="container mt-4">
        <h1 class="mb-4">Expenses for {{ ucfirst($period) }}</h1>

        <!-- Buttons to select the period -->
        <div class="d-flex justify-content-between mb-4">
            <div>
                <a href="{{ route('expenses.show', ['expense' => $expense->id, 'period' => 'daily']) }}" class="btn btn-info btn-sm mx-1">Daily</a>
                <a href="{{ route('expenses.show', ['expense' => $expense->id, 'period' => 'weekly']) }}" class="btn btn-info btn-sm mx-1">Weekly</a>
                <a href="{{ route('expenses.show', ['expense' => $expense->id, 'period' => 'monthly']) }}" class="btn btn-info btn-sm mx-1">Monthly</a>
            </div>
            <div class="text-end">
                <!-- Download button dynamically set based on the current period -->
                <a href="{{ route('expenses.download', ['period' => $period]) }}" class="btn btn-success btn-sm mx-1">Download {{ ucfirst($period) }} Expenses</a>

            </div>
        </div>

        <!-- Display expenses -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Expense List</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    @php
                        $totalPrice = 0;
                    @endphp
                    @forelse ($expenses as $expenseItem)
                        @php
                            $totalPrice += $expenseItem->price;
                        @endphp
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold">Name:</span>
                                <span>{{ $expenseItem->name }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold">Price:</span>
                                <span>{{ number_format($expenseItem->price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold">Date:</span>
                                <span>{{ \Carbon\Carbon::parse($expenseItem->date)->format('Y-m-d') }}</span>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center">No expenses found for this period.</li>
                    @endforelse
                </ul>
                <div class="mt-3 text-right">
                    <h5 class="mb-0">Total Price: {{ number_format($totalPrice, 2) }}</h5>
                </div>
            </div>
        </div>
    </div>
@endsection
