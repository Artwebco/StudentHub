<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lesson Reminder</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <h2 style="margin-bottom: 8px;">Lesson Reminder</h2>
    <p style="margin-top: 0; color: #4b5563;">This is your {{ $reminderLabel }} reminder.</p>

    <div style="margin: 16px 0; padding: 14px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f9fafb;">
        <p style="margin: 0 0 8px;"><strong>Student:</strong> {{ $appointment->student?->name ?? 'N/A' }}</p>
        <p style="margin: 0 0 8px;"><strong>Teacher/Admin:</strong> {{ $appointment->admin?->name ?? 'N/A' }}</p>
        <p style="margin: 0 0 8px;"><strong>Start:</strong>
            {{ optional($appointment->starts_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</p>
        <p style="margin: 0;"><strong>End:</strong>
            {{ optional($appointment->ends_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</p>
    </div>

    @if ($appointment->note)
        <p><strong>Note:</strong> {{ $appointment->note }}</p>
    @endif

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>

</html>