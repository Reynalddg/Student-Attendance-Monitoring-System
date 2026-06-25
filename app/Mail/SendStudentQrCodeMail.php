<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendStudentQrCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $qrCodePath;

    public function __construct($name, $qrCodePath)
    {
        $this->name = $name;
        $this->qrCodePath = $qrCodePath;
    }

    public function build()
    {
        return $this->subject('Your Student QR Code - Talavera Senior High School')
                    ->view('email.student_qr_code')
                    ->attach($this->qrCodePath, [
                        'as' => 'student_qrcode.png', // update to .png
                        'mime' => 'image/png',        // update MIME type
                    ]);
    }
}


