<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LessonCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $reminderText;
    public $studentName;
    public $timezone;

    public function __construct(Appointment $appointment, $studentName, $reminderText, string $timezone = 'UTC')
    {
        $this->appointment = $appointment;
        $this->studentName = $studentName;
        $this->reminderText = $reminderText;
        $this->timezone = $timezone;
    }

    public function build()
    {
        return $this->subject('Class Scheduled Successfully')
            ->view('emails.lesson-created');
    }
}
