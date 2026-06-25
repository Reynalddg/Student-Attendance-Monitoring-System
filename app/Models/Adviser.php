<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adviser extends Model
{
    use HasFactory;
     protected $primaryKey = 'adviser_id';
     public $timestamps = false;
     protected $fillable = ['first_name', 'middle_name', 'last_name', 'gender', 'phone_number', 'date_created', 'date_archived'];
}
