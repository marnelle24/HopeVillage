<x-mail::message>
# Hello {{ $userName ?? 'there' }}!

You are receiving this email because we received a password reset request for your **{{ $appName }}** account.

<x-mail::button :url="$url" color="primary">
{{ __('Reset Password') }}
</x-mail::button>

This link will expire in **{{ $expireMinutes }} minutes**. If you did not request a password reset, you can safely ignore this email.

If you're having trouble clicking the "Reset Password" button, copy and paste this URL into your web browser: [{{ $url }}]({{ $url }})

Thanks,<br>
**{{ $appName }}** Team
</x-mail::message>
