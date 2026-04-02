<?php

namespace App\Console\Commands;

use App\Mail\LessonAppointmentReminderMail;
use App\Models\Appointment;
use App\Models\AppointmentSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendLessonAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send 24-hour and 30-minute reminder emails for scheduled lessons';

    public function handle(): int
    {
        $settings = AppointmentSetting::query()->first();
        $firstReminderMinutes = (int) ($settings?->first_reminder_minutes_before ?? 1440);
        $secondReminderMinutes = (int) ($settings?->second_reminder_minutes_before ?? 30);

        if ($secondReminderMinutes >= $firstReminderMinutes) {
            $secondReminderMinutes = max(5, $firstReminderMinutes - 5);
        }

        $this->sendRemindersForWindow(
            column: 'reminder_24h_sent_at',
            fromMinutes: max(0, $firstReminderMinutes - 5),
            toMinutes: $firstReminderMinutes + 5,
            label: $this->formatReminderLabel($firstReminderMinutes)
        );

        $this->sendRemindersForWindow(
            column: 'reminder_30m_sent_at',
            fromMinutes: max(0, $secondReminderMinutes - 5),
            toMinutes: $secondReminderMinutes + 5,
            label: $this->formatReminderLabel($secondReminderMinutes)
        );

        return self::SUCCESS;
    }

    private function sendRemindersForWindow(string $column, int $fromMinutes, int $toMinutes, string $label): void
    {
        $windowStart = now()->addMinutes($fromMinutes);
        $windowEnd = now()->addMinutes($toMinutes);

        $appointments = Appointment::query()
            ->with(['student:id,name,email', 'admin:id,name,email'])
            ->where('status', 'scheduled')
            ->whereNull($column)
            ->whereBetween('starts_at', [$windowStart, $windowEnd])
            ->get();

        foreach ($appointments as $appointment) {
            if (!$appointment instanceof Appointment) {
                continue;
            }

            $recipients = collect([
                [
                    'email' => $appointment->student?->email,
                    'name' => $appointment->student?->name ?? 'Student',
                ],
                [
                    'email' => $appointment->admin?->email,
                    'name' => $appointment->admin?->name ?? 'Admin',
                ],
            ])
                ->filter(fn(array $recipient) => filled($recipient['email']))
                ->unique('email')
                ->values();

            foreach ($recipients as $recipient) {
                $mailable = new LessonAppointmentReminderMail($appointment, $label, $recipient['name']);

                try {
                    Mail::to($recipient['email'])->queue($mailable);
                } catch (Throwable $e) {
                    // Fallback for environments without active queue workers/drivers.
                    Mail::to($recipient['email'])->send($mailable);
                }
            }

            $appointment->forceFill([
                $column => now(),
            ])->save();

            $this->info("Sent {$label} reminder for appointment #{$appointment->id}");
        }
    }

    private function formatReminderLabel(int $minutes): string
    {
        if ($minutes % 1440 === 0) {
            $days = (int) ($minutes / 1440);

            return $days === 1 ? '1 day' : "{$days} days";
        }

        if ($minutes % 60 === 0) {
            $hours = (int) ($minutes / 60);

            return $hours === 1 ? '1 hour' : "{$hours} hours";
        }

        return $minutes === 1 ? '1 minute' : "{$minutes} minutes";
    }
}
