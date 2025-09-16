<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayRoll extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'employee_id',
        'salary',
        'bonus',
        'deduction',
        'total'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
