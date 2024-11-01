<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccouncementDocument extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id','title','status'];



    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
