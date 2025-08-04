@component('mail::message')
# ❌ Hi {{ $employeeName }},

We regret to inform you that **your assignment for Booth Application #{{ $application->id }} has been removed**.

If this is a mistake, please contact your company coordinator or reply to this email.


Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
