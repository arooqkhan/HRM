<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory,SoftDeletes;
    use HasRoles;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'contact_email',
        'gender',
        'employee_id',
        'department',
        'designation',
        'employee_status',
        'role',
        'salary',
        'number',
        'emgr_number',
        'joining_date',
        'work_shift',
        'password',
        'dob',
        'ninumber',
        'visa',
        'visadate',
        'address',
        'documents',
        'image',
    ];


    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

 

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function payRolls()
    {
        return $this->hasMany(PayRoll::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'employee_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'id');
    }

    public function payslipUploads()
    {
        return $this->hasMany(PayslipUpload::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

}
