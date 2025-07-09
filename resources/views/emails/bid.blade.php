@component('mail::message')
# Bid Notification

Dear {{ $name }},

This email is to notify you that your bid with ID {{ $bid->id }} on tender {{ $tender->title }} has been {{ $bid->status }}.


Please check your email for further instructions.

Thank you for using our system.

Best regards,
{{ config('app.name') }}

@endcomponent
