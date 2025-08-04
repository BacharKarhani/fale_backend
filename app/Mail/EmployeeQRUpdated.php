<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeQRUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $application;
    public $filename;

    public function __construct($employee, $application, $filename)
    {
        $this->employee = $employee;
        $this->application = $application;
        $this->filename = $filename;
    }

    public function build()
    {
        return $this->subject('Your Booth Info Has Been Updated - New QR Code')
            ->markdown('emails.employee.updated')
            ->attachFromStorageDisk('public', $this->filename, 'qr-code.png', [
                'mime' => 'image/png',
            ]);
    }
}
