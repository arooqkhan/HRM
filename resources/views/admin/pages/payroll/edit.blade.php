@extends('admin.master.main')
@section('content')
<div class="container">
    <h1>Edit Payroll</h1>

    <form action="{{ route('payroll.update', $payroll->id) }}" method="POST" id="payrollForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="inputEmployeeId">Employee Name</label>
                    <select class="form-control" id="inputEmployeeId" name="employee_id" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $payroll->employee_id == $employee->id ? 'selected' : '' }}>
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
                    <input type="number" step="0.01" class="form-control" id="salary" name="salary" value="{{ $payroll->salary }}" required>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="bonus">Bonus:</label>
                    <input type="number" step="0.01" class="form-control" id="bonus" name="bonus" value="{{ $payroll->bonus }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="deduction">Deduction:</label>
                    <input type="number" step="0.01" class="form-control" id="deduction" name="deduction" value="{{ $payroll->deduction }}">
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label for="total">Total:</label>
                    <input type="number" step="0.01" class="form-control" id="total" name="total" value="{{ $payroll->total }}" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary mt-2">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const salaryInput = document.getElementById('salary');
        const bonusInput = document.getElementById('bonus');
        const deductionInput = document.getElementById('deduction');
        const totalInput = document.getElementById('total');

        function updateTotal() {
            const salary = parseFloat(salaryInput.value) || 0;
            const bonus = parseFloat(bonusInput.value) || 0;
            const deduction = parseFloat(deductionInput.value) || 0;
            totalInput.value = (salary + bonus - deduction).toFixed(2);
        }

        salaryInput.addEventListener('input', updateTotal);
        bonusInput.addEventListener('input', updateTotal);
        deductionInput.addEventListener('input', updateTotal);

        updateTotal();
    });
</script>

