<?php

namespace App\Mail;

use App\Models\OfficeInvitation;
use Illuminate\Mail\Mailable;

class OfficeInvitationMail extends Mailable
{
    public function __construct(public OfficeInvitation $invitation)
    {
    }

    public function build()
    {
        return $this->subject('Office management invitation')
            ->view('emails.office_invitation');
    }
}
