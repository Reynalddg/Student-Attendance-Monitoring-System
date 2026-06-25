<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
   
    protected $primaryKey = 'section_id';  
    public $timestamps = false;
    protected $fillable = ['grade_level', 'section_name', 'user_id', 'track_strand_id', 'date_archived'];
    

    public function adviser()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'section_id');
    }

   public function track_strand()
{
    return $this->belongsTo(Track::class, 'track_strand_id', 'track_strand_id');
}

public function studentEnrollments()
{
    return $this->hasMany(\App\Models\StudentEnrollment::class, 'section_id', 'section_id');
}

}
