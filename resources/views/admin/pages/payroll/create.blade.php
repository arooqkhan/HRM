@extends('admin.master.main')

@section('content')

<div class="row">
    <div id="flStackForm" class="col-lg-12 layout-spacing layout-top-spacing">
        <div class="statbox widget box box-shadow">
            <div class="widget-header">
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                        <h3>Add Payroll</h3>
                    </div>
                </div>
            </div>
            <div class="widget-content widget-content-area">

            <form action="{{ route('payroll.store') }}" method="POST" id="payrollForm">
    @csrf

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="inputEmployeeId">Employee Name</label>
                <select class="form-control" id="inputEmployeeId" name="employee_id" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="salary">Salary:</label>
                <input type="number" step="0.01" class="form-control" id="salary" name="salary" value="{{ old('salary') }}" required>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="bonus">Bonus:</label>
                <input type="number" step="0.01" class="form-control" id="bonus" name="bonus" value="{{ old('bonus') }}">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="deduction">Deduction:</label>
                <input type="number" step="0.01" class="form-control" id="deduction" name="deduction" value="{{ old('deduction') }}">
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label for="total">Total:</label>
                <input type="number" step="0.01" class="form-control" id="total" name="total" value="{{ old('total') }}" readonly required>
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
    document.addEventListener('DOMContentLoaded', function() {
        const salaryField = document.getElementById('salary');
        const bonusField = document.getElementById('bonus');
        const deductionField = document.getElementById('deduction');
        const totalField = document.getElementById('total');

        function calculateTotal() {
            const salary = parseFloat(salaryField.value) || 0;
            const bonus = parseFloat(bonusField.value) || 0;
            const deduction = parseFloat(deductionField.value) || 0;
            const total = salary + bonus - deduction;
            totalField.value = total.toFixed(2);
        }

        salaryField.addEventListener('input', calculateTotal);
        bonusField.addEventListener('input', calculateTotal);
        deductionField.addEventListener('input', calculateTotal);

        // Initialize the total field on page load
        calculateTotal();
    });
</script>



@endsection