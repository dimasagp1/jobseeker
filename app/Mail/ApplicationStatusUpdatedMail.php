<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;
    public ?string $notes;

    public function __construct(JobApplication $application, ?string $notes = null)
    {
        $this->application = $application;
        $this->notes = $notes;
    }

    public function envelope(): Envelope
    {
        $jobTitle = $this->application->job->title ?? 'Lowongan';
        $companyName = $this->application->job->company->name ?? 'Perusahaan';

        $subject = match ($this->application->status) {
            'pending'          => "Konfirmasi Lamaran Kerja: {$jobTitle} - {$companyName}",
            'reviewed'         => "Update Lamaran: Ditinjau - {$jobTitle}",
            'shortlisted'      => "Selamat! Anda Lolos Seleksi Berkas - {$jobTitle}",
            'test_invited'     => "Undangan Tes Seleksi (Psikotes) - {$jobTitle}",
            'test_in_progress' => "Status Tes Seleksi: Sedang Dikerjakan - {$jobTitle}",
            'test_completed'   => "Tes Seleksi Selesai: Terima Kasih - {$jobTitle}",
            'interview'        => "Undangan Wawancara Kerja - {$jobTitle}",
            'accepted'         => "Selamat! Anda Diterima Kerja di {$companyName}",
            'rejected'         => "Pemberitahuan Hasil Seleksi - {$jobTitle}",
            default            => "Pembaruan Status Lamaran Kerja - {$jobTitle}",
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.applications.status_updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
