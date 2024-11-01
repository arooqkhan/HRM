<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'employee_id', 'leave_type', 'age', 'date', 'start_date', 'end_date',
        'start_time', 'end_time', 'first_half', 'last_half', 'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function employees()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getLeaveDaysAttribute()
    {
        if ($this->start_date && $this->end_date) {
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);
            $leaveDays = 0;

            while ($startDate <= $endDate) {
                if (!$startDate->isWeekend()) {
                    $leaveDays++;
                }
                $startDate->addDay();
            }

            return $leaveDays;
        } elseif ($this->date && !Carbon::parse($this->date)->isWeekend()) {
            return 1;
        }

        return 0;
    }
}
