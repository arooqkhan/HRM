<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['employee_id', 'name', 'document','status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'employee_id');
    }
}