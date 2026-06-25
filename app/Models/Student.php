<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
   
    
    protected $primaryKey = 'student_id';
    public $timestamps = false;
    protected $fillable = ['first_name', 'middle_name', 'last_name', 'suffix', 'lrn', 'gender', 'barangay', 'municipality', 'province', 'birthdate','religion' , 'grade_level', 'remarks','image', 'date_created', 'date_archived' ];


     public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id', 'student_id');
    }
     
    public function currentEnrollment()
{
    return $this->hasOne(StudentEnrollment::class, 'student_id', 'student_id')
                ->whereNull('date_archived'); 
}

public function currentSection()
{
    return $this->hasOneThrough(
        Section::class,
        StudentEnrollment::class,
        'student_id',
        'section_id',  
        'student_id',  
        'section_id'    
    )->whereNull('student_enrollments.date_archived'); 
}

public function guardians()
{
    return $this->hasMany(Guardian::class, 'student_id', 'student_id');
}



}
