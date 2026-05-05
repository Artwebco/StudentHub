<p>Dear {{ $studentName }},</p>

<p>Your class has been successfully scheduled for <strong>{{ $appointment->starts_at->format('F j, Y H:i') }}</strong>.
</p>

<p>{{ $reminderText }}</p>

<p>If you have any questions or need to reschedule, please contact us.</p>

<p>Best regards,<br>
    {{ config('app.name') }}</p>