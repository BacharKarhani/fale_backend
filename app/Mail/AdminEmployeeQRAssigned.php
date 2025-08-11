<?php

namespace App\Mail;

use App\Models\AdminEmployee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class AdminEmployeeQRAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public AdminEmployee $employee;
    /** e.g. 'qr_admin_employees/employee_1.png' on the 'public' disk */
    public string $filename;

    public function __construct(AdminEmployee $employee, string $filename)
    {
        $this->employee = $employee;
        $this->filename = $filename;
    }

    public function build()
    {
        // Absolute path for embedding inline
        $qrPath = Storage::disk('public')->path($this->filename);

        return $this->subject('Your Event QR Code')
            ->view('emails.admin-employee.assigned') // 👈 regular Blade view (not markdown)
            ->with([
                'employee' => $this->employee,
                'qrPath'   => $qrPath,  // pass absolute path; we’ll embed it in the view
            ])
            // Optional: also attach the PNG
            ->attachFromStorageDisk('public', $this->filename, 'qr-code.png', ['mime' => 'image/png']);
    }
}
