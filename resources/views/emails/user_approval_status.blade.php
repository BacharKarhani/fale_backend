<!doctype html>
<html>
  <body style="font-family: Arial, Helvetica, sans-serif; line-height:1.6; color:#222">
    <h2 style="margin:0 0 12px">Hello {{ $user->name }},</h2>

    @if($approved)
      <p>Your account has been <strong>approved</strong>. You can now log in and access your dashboard.</p>
    @else
      <p>Your account status has changed. Please contact support if you have questions.</p>
    @endif

    @if(!empty($user->company_name))
      <p>Company: <strong>{{ $user->company_name }}</strong></p>
    @endif

    <p style="margin-top:24px">Best regards,<br> {{ config('app.name') }}</p>
  </body>
</html>
