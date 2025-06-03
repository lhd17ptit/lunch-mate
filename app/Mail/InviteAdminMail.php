<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $admin;
    public $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admin, $password)
    {
        $this->admin = $admin;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $link = config('app.url') . '/admin/login';

        return $this->subject('Mời tham gia - LunchMate')->markdown('email.admin.invite-admin', [
            'url' => $link,
            'admin' => $this->admin,
            'password' => $this->password,
        ]);
    }
}
