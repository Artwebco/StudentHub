<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        private string $pdfContent,
        private string $pdfFileName,
        public string $recipientName
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Фактура ' . $this->invoice->invoice_number
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-created'
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->pdfFileName)
                ->withMime('application/pdf'),
        ];
    }
}
