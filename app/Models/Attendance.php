<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'employee_id',
        'clock_in_date',
        'clock_in_time',
        'clock_out_date',
        'clock_out_time',
        'reason',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
