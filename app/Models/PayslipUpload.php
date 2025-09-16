<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayslipUpload extends Model
{
    use HasFactory,SoftDeletes;

    
    protected $fillable = [
        'month', 
        'pdfs', 
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
