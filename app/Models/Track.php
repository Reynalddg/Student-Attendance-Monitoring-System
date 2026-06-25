<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'track_strand_id';
    protected $fillable = ['track', 'strand','date_created', 'date_archived'];
}
