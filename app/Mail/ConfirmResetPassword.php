<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $send_data;

    public function __construct($send_data)
    {
        $this->send_data = $send_data;
    }

    public function build()
    {
        return $this->subject('Konfirmasi Ubah Password Akun Anda')
                    ->view('reset_password');
    }
}
