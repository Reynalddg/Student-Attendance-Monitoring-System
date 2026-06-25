<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'academic_year_id';
    protected $fillable = ['name', 'start_date', 'end_date', "status" , 'students_promoted', 'date_created', 'date_archived'];

    
}
