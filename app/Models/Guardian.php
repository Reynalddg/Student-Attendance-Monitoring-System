<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    use HasFactory;

    protected $primaryKey = 'guardian_id';  
    public $timestamps = false;

    protected $fillable = [
        'guardian_type',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'relation',
        'phone_number',
        'student_id',
        'date_created',
        'date_archived'
    ];

   public function student() {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Get all guardians of students in a specific section
    public static function guardiansInSection($sectionId)
    {
        return self::whereHas('student.enrollments', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId)
              ->whereNull('date_archived'); // optional if archived
        })->get();
    }

 
}
