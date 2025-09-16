<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'employee_id',
        'title',
        'message',
        'status',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    
}
