<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Event QR Code</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body style="background:#f5f7fb; margin:0; padding:24px; font-family:Arial,Helvetica,sans-serif; color:#111;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.05);">
    <tr>
      <td style="padding:28px 28px 16px 28px;">
        <h1 style="margin:0 0 10px 0; font-size:22px; line-height:1.3; font-weight:700;">
          🎉 Welcome, <span style="color:#1976d2;">{{ $employee->name }}</span>!
        </h1>
        <p style="margin:0 0 8px 0;">Congratulations!</p>
        <p style="margin:0 0 18px 0;">
          You have been added as a <b>visitor</b> at
          <span style="background:#e3f4ff; color:#1976d2; padding:2px 10px; border-radius:8px; display:inline-block;">
            {{ $employee->company ?? 'LafeLeb' }}
          </span>.
        </p>

        <hr style="border:none;border-top:1px solid #e6eaf2; margin:16px 0 20px 0;">

        <h3 style="margin:0 0 10px 0; font-size:16px;">Here is your personal event QR code:</h3>

        <div style="text-align:center; margin:20px 0 24px;">
          {{-- Embed inline so it renders inside the email and can be scanned --}}
          <img
            src="{{ $message->embed($qrPath) }}"
            alt="Your QR Code"
            style="width:200px; max-width:80%; border:8px solid #fff; box-shadow:0 0 18px #c8e6ff; border-radius:16px; background:#fff; display:inline-block;"
          >
          <div style="margin-top:8px; color:#555; font-size:13px;">
            (Show this QR code at the event for entry)
          </div>
        </div>

        <hr style="border:none;border-top:1px solid #e6eaf2; margin:0 0 16px 0;">

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#eef5ff;border-left:4px solid #7aaef7;border-radius:6px;">
          <tr>
            <td style="padding:14px 16px;">
              <div style="font-weight:700; margin-bottom:6px;">Visitor Details:</div>
              <div><b>Name:</b> {{ $employee->name }}</div>
              @if($employee->email)
              <div><b>Email:</b> {{ $employee->email }}</div>
              @endif
              @if($employee->phone_number)
              <div><b>Phone:</b> {{ $employee->phone_number }}</div>
              @endif
              <div><b>Company:</b> {{ $employee->company ?? 'LafeLeb' }}</div>
            </td>
          </tr>
        </table>

        <div style="text-align:center; margin:22px 0 8px;">
          <a href="{{ config('app.url') }}"
             style="background:#111827;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px;display:inline-block;">
            View Event Details
          </a>
        </div>

        <p style="color:#555; font-size:13px; margin-top:16px;">
          If you have any questions, reply to this email.
        </p>
        <p style="font-weight:700; color:#1976d2;">LAFE Team</p>
      </td>
    </tr>
  </table>
</body>
</html>
