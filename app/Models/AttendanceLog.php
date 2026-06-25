<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';
    protected $primaryKey = 'attendance_id';
    public $timestamps = false;
    protected $fillable = ['enrollment_id','date_time', 'status', 'date_created'];
    
 public function enrollment()
{
    return $this->belongsTo(StudentEnrollment::class, 'enrollment_id', 'enrollment_id');
}

     public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by', 'user_id');
    }
 
}
