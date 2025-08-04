<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeQRRemoved extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeName;
    public $application;

    public function __construct($employeeName, $application)
    {
        $this->employeeName = $employeeName;
        $this->application = $application;
    }

    public function build()
    {
        return $this->subject('You Have Been Removed from the Booth')
            ->markdown('emails.employee.removed');
    }
}
