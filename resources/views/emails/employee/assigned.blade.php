@component('mail::message')


# 🎉 Welcome, <span style="color:#1976d2;">{{ $employee->name }}</span>!

Congratulations!  
You have been assigned as an <b>visitor</b> for booth application  
<span style="background:#e3f4ff; color:#1976d2; padding:2px 10px; border-radius:8px;">#{{ $application->id }}</span>.

---

## Here is your personal event QR code:

<div style="text-align:center; margin:25px 0;">
    <img src="cid:qr_code.png" style="width:200px; border:8px solid #fff; box-shadow: 0 0 18px #c8e6ff; border-radius: 16px; background:#fff;">
    <br>
    <span style="display:inline-block; margin-top: 7px; color: #555; font-size: 13px;">(Show this QR code at the event for entry)</span>
</div>

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
