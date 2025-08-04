@component('mail::message')

# ✏️ Your Booth Info Was Updated, <span style="color:#1976d2;">{{ $employee->name }}</span>!

Your details for Booth Application  
<span style="background:#e3f4ff; color:#1976d2; padding:2px 10px; border-radius:8px;">#{{ $application->id }}</span>  
were just **updated**.

---

<table role="presentation" width="100%">
    <tr>
        <td align="center" style="padding: 38px 0 25px 0;">
            <div style="display: inline-block; text-align: center;">
                <div style="margin-bottom:12px; font-size:18px;">
                    <strong>Here is your updated personal event QR code:</strong>
                </div>
                <img src="cid:qr-code.png" style="width:200px; border:8px solid #fff; box-shadow: 0 0 18px #c8e6ff; border-radius: 16px; background:#fff;">
                <div style="margin-top: 10px; color: #555; font-size: 13px;">(Show this QR code at the event for entry)</div>
            </div>
        </td>
    </tr>
</table>

---

@component('mail::panel')
**Application Details:**<br>
<b>Name:</b> {{ $employee->name }}<br>
@if($employee->email)
<b>Email:</b> {{ $employee->email }}<br>
@endif
@if($employee->phone_number)
<b>Phone:</b> {{ $employee->phone_number }}<br>
@endif
<b>Booth Application ID:</b> {{ $application->id }}
@endcomponent

---

@component('mail::button', ['url' => config('app.url'), 'color' => 'primary'])
View Event Details
@endcomponent

If you have any questions, reply to this email.

Best regards,  
<strong style="color:#1976d2;">LAFE Team</strong>

@endcomponent
