<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SMSLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'sms_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',  
        'guardian_id',
        'message',
        'sent_at',
    ];

    // Relationship to the sender (User)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'user_id');
    }

    // Relationship to recipient user (optional)
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id', 'user_id');
    }

    // Relationship to guardian
    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id', 'guardian_id');
    }
}
