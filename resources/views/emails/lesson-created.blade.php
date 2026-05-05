<p>Dear {{ $studentName }},</p>

<p>Your class has been successfully scheduled for
    <strong>{{ $appointment->starts_at->setTimezone($timezone)->format('F j, Y H:i') }}</strong> ({{ $timezone }}).
</p>

<p>{{ $reminderText }}</p>

<p>If you have any questions or need to reschedule, please contact us.</p>

<p>Best regards,<br>
    {{ config('app.name') }}</p>
