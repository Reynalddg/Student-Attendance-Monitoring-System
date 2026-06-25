<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'semester_id';
    protected $fillable = ['name','academic_year_id', 'start_date', 'end_date','status','date_created', 'date_archived'];

    public function academicYear()
        {
            return $this->belongsTo(AcademicYear::class, 'academic_year_id', 'academic_year_id');
        }
}
