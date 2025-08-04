<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeQRAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $application;
    public $qrPath;

    public function __construct($employee, $application, $qrPath)
    {
        $this->employee = $employee;
        $this->application = $application;
        $this->qrPath = $qrPath;
    }

    public function build()
    {
        // Absolute path to file in storage
        $absoluteQrPath = storage_path('app/public/qr_employees/employee_' . $this->employee->id . '.png');

        return $this->subject('Your Employee QR Code')
            ->markdown('emails.employee.assigned')
            ->attach($absoluteQrPath, [
                'as' => 'qr_code.png',
                'mime' => 'image/png',
            ]);
    }
}
