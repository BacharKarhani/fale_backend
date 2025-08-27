<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
// إذا بدك تخليه على الطابور: implements \Illuminate\Contracts\Queue\ShouldQueue

class UserApprovalStatus extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public bool $approved;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, bool $approved)
    {
        $this->user = $user;
        $this->approved = $approved;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->approved
            ? 'Your account has been approved'
            : 'Your account status has changed';

        return $this->subject($subject)
            ->view('emails.user_approval_status');
    }
}
