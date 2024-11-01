<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'employee_id',
        'shift_type',
        'add_duty',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
