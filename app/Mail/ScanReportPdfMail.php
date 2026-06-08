<?php

namespace App\Mail;

use App\Models\ScanResult;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ScanReportPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ScanResult $scan,
        private readonly string $pdfContent,
        private readonly string $fileName
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bao cao ket qua chan doan - ' . $this->scan->patient->patient_code
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.scan_report_pdf',
            with: [
                'scan' => $this->scan,
                'patient' => $this->scan->patient,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->fileName)
                ->withMime('application/pdf'),
        ];
    }
}
