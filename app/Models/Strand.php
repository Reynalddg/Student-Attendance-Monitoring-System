<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strand extends Model
{
    
public $timestamps = false;
    protected $primaryKey = 'strand_id';
    protected $fillable = ['name','track_id' ,'date_created', 'date_archived'];

public function track()
    {
        return $this->belongsTo(Track::class, 'track_id', 'track_id');
    }
}

