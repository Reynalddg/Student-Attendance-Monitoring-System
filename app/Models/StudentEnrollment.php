<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $primaryKey = 'enrollment_id';
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'section_id',
        'semester_id',
        'status',
        'remarks',
        'date_created',
        'date_archived',
    ];

  
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'semester_id');
    }

        public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'enrollment_id', 'enrollment_id');
    }
}
