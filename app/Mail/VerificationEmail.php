<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $verificationCode;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->verificationCode = $data['verificationCode'];
        $this->user = $data['user'];
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('كود التفعيل الخاص بك - ' . config('app.name'))
                    ->view('emails.verification')
                    ->with([
                        'verificationCode' => $this->verificationCode,
                        'user' => $this->user
                    ]);
    }
}
