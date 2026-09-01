<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Status Lamaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f6f9;
            padding: 30px 15px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            padding: 30px 25px;
            text-align: center;
            color: #ffffff;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px 25px;
            color: #334155;
            line-height: 1.6;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
        }
        .status-card {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 18px 20px;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 8px;
        }
        .bg-pending { background-color: #fef3c7; color: #b45309; }
        .bg-reviewed { background-color: #e0f2fe; color: #0369a1; }
        .bg-shortlisted { background-color: #dcfce7; color: #15803d; }
        .bg-test { background-color: #f3e8ff; color: #7e22ce; }
        .bg-interview { background-color: #ffedd5; color: #c2410c; }
        .bg-accepted { background-color: #dcfce7; color: #166534; }
        .bg-rejected { background-color: #ffe4e6; color: #be123c; }

        .notes-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 16px 20px;
            margin: 20px 0;
        }
        .notes-title {
            font-weight: 700;
            color: #1e40af;
            font-size: 14px;
            margin-bottom: 6px;
        }
        .btn-wrapper {
            text-align: center;
            margin: 30px 0 10px 0;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            padding: 14px 32px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 25px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h2>{{ config('app.name', 'Portal Rekrutmen') }}</h2>
                <p>Pemberitahuan Status Lamaran Pekerjaan</p>
            </div>

            <div class="content">
                <div class="greeting" style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 15px;">
                    Kepada Yth. Bapak/Ibu/Saudara/i {{ $application->user->name }},
                </div>

                @php
                    $jobTitle = $application->job->title ?? 'Posisi Pekerjaan';
                    $companyName = $application->job->company->company_name ?? $application->job->company->name ?? config('app.name');
                    $status = $application->status;

                    $badgeClass = match($status) {
                        'pending' => 'bg-pending',
                        'reviewed' => 'bg-reviewed',
                        'shortlisted' => 'bg-shortlisted',
                        'test_invited', 'test_in_progress', 'test_completed' => 'bg-test',
                        'interview' => 'bg-interview',
                        'accepted' => 'bg-accepted',
                        'rejected' => 'bg-rejected',
                        default => 'bg-reviewed'
                    };

                    $statusText = match($status) {
                        'pending' => 'Lamaran Berhasil Terkirim',
                        'reviewed' => 'Lamaran Sedang Ditinjau',
                        'shortlisted' => 'Lolos Seleksi Berkas',
                        'test_invited' => 'Undangan Tes Psikotes',
                        'test_in_progress' => 'Sedang Mengerjakan Tes',
                        'test_completed' => 'Tes Seleksi Selesai',
                        'interview' => 'Undangan Wawancara Kerja',
                        'accepted' => 'Diterima Bekerja',
                        'rejected' => 'Belum Lolos Seleksi',
                        default => ucfirst($status)
                    };
                @endphp

                <p style="margin-bottom: 15px;">Dengan hormat,</p>
                
                <p>Melalui surat elektronik ini, kami dari Tim Rekrutmen <strong>{{ $companyName }}</strong> menyampaikan pemberitahuan mengenai status terkini dari lamaran pekerjaan yang Anda ajukan untuk posisi <strong>{{ $jobTitle }}</strong>.</p>

                <div class="status-card">
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status Proses Rekrutmen:</div>
                    <span class="status-badge {{ $badgeClass }}">{{ $statusText }}</span>
                </div>

                @if($status === 'pending')
                    <p>Terima kasih atas minat Anda untuk bergabung dengan <strong>{{ $companyName }}</strong>. Berkas pendaftaran dan dokumen pendukung Anda telah berhasil diterima dalam sistem rekrutmen kami dan akan segera ditinjau oleh Tim HRD.</p>
                @elseif($status === 'reviewed')
                    <p>Kami menginformasikan bahwa berkas lamaran dan kualifikasi yang Anda kirimkan saat ini sedang dalam tahap peninjauan mendalam oleh Tim Rekrutmen dan Tim Manajemen terkait.</p>
                @elseif($status === 'shortlisted')
                    <p>Dengan senang hati kami sampaikan bahwa berdasarkan hasil verifikasi awal, kualifikasi Anda dinilai sesuai dengan kriteria yang kami butuhkan dan Anda dinyatakan <strong>Lolos Seleksi Berkas</strong>.</p>
                @elseif($status === 'test_invited')
                    <p>Kami mengundang Anda untuk mengikuti tahapan Asesmen & Psikotes Seleksi secara online. Mohon dapat masuk ke akun portal rekrutmen Anda untuk mengakses dan menyelesaikan pelaksanaan tes sesuai dengan instruksi yang tersedia.</p>
                @elseif($status === 'interview')
                    <p>Kami mengundang Anda untuk mengikuti tahapan Wawancara Kerja. Mohon cermati rincian jadwal, lokasi/media wawancara, serta petunjuk pelaksanaan yang kami sertakan pada bagian catatan di bawah ini.</p>
                @elseif($status === 'accepted')
                    <p>Berdasarkan hasil dari seluruh rangkaian proses seleksi yang telah dilaksanakan, kami dengan bangga menyampaikan bahwa Anda dinyatakan <strong>DITERIMA BEKERJA</strong> untuk posisi <strong>{{ $jobTitle }}</strong> di <strong>{{ $companyName }}</strong>. Tim HRD kami akan segera menghubungi Anda terkait penawaran kerja (Offering Letter) serta prosedur administrasi penerimaan karyawan baru.</p>
                @elseif($status === 'rejected')
                    <p>Terima kasih atas partisipasi dan apresiasi Anda terhadap proses seleksi di <strong>{{ $companyName }}</strong>. Setelah melalui pertimbangan yang cermat, dengan berat hati kami menginformasikan bahwa saat ini kami belum dapat melanjutkan lamaran Anda ke tahapan berikutnya. Kami sangat mengapresiasi waktu serta usaha yang telah Anda berikan dan mendoakan kesuksesan bagi karier Anda di masa mendatang.</p>
                @endif

                @if(!empty($notes))
                    <div class="notes-box">
                        <div class="notes-title">Catatan Tambahan dari Tim Rekrutmen:</div>
                        <div style="white-space: pre-line; color: #1e3a8a;">{{ $notes }}</div>
                    </div>
                @endif

                <p style="margin-top: 20px;">Bapak/Ibu/Saudara/i dapat memantau rincian perkembangan lamaran secara berkala melalui tautan di bawah ini:</p>

                <div class="btn-wrapper">
                    <a href="{{ route('seeker.applications.show', $application->id) }}" class="btn">Lihat Detail Lamaran</a>
                </div>

                <div style="margin-top: 30px; border-top: 1px solid #e2e8f0; pt-3; padding-top: 20px; font-size: 14px;">
                    <p class="mb-1">Atas perhatian dan kerja sama Anda, kami ucapkan terima kasih.</p>
                    <br>
                    <p class="mb-0">Hormat kami,</p>
                    <p class="fw-bold mb-0" style="font-weight: 700; color: #1e293b;">Tim Rekrutmen & HRD</p>
                    <p class="text-muted small mb-0" style="color: #64748b;">{{ $companyName }}</p>
                </div>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
                <p>Surat elektronik ini dikirimkan secara otomatis oleh sistem rekrutmen resmi. Mohon tidak membalas email ini secara langsung.</p>
            </div>
        </div>
    </div>
</body>
</html>
