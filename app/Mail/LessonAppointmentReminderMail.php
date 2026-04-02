<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LessonAppointmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $reminderLabel,
        public string $recipientName
    ) {
    }

    public function envelope(): Envelope
    {
        $studentName = $this->appointment->student?->name ?? 'Student';

        return new Envelope(
            subject: "Lesson Reminder ({$this->reminderLabel}) - {$studentName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lesson-reminder',
        );
    }
}
