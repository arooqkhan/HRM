<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RotaExport implements FromCollection, WithHeadings
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        $data = collect();

        foreach ($this->employees as $employee) {
            foreach ($employee->shifts as $shift) {

                $statusText = $shift->status == 0 ? 'Pending' : ($shift->status == 1 ? 'Accepted' : ($shift->status == 2 ? 'Rejected' : 'Unknown'));
                $data->push([
                    'Employee Name' => $employee->first_name . ' ' . $employee->last_name,
                    'Shift Type' => $shift->shift_type,
                    'Duty' => $shift->add_duty,
                    'Start Time' => $shift->start_time,
                    'End Time' => $shift->end_time,
                    'Date' => $shift->date,
                    'Status' => $statusText,
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Shift Type',
            'Duty',
            'Start Time',
            'End Time',
            'Date',
            'Status'
        ];
    }
}
