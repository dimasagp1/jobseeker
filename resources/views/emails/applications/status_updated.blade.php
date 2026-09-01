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
                <div class="greeting">Halo, {{ $application->user->name }}!</div>

                @php
                    $jobTitle = $application->job->title ?? 'Posisi Pekerjaan';
                    $companyName = $application->job->company->name ?? 'Perusahaan';
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
                        'reviewed' => 'Lamaran Sedang Ditinjau HRD',
                        'shortlisted' => 'Lolos Seleksi Berkas',
                        'test_invited' => 'Diundang Mengikuti Tes Psikotes',
                        'test_in_progress' => 'Sedang Mengerjakan Tes Seleksi',
                        'test_completed' => 'Tes Seleksi Telah Selesai',
                        'interview' => 'Diundang Tahap Wawancara',
                        'accepted' => 'Selamat! Anda Diterima Kerja',
                        'rejected' => 'Belum Lolos Seleksi',
                        default => ucfirst($status)
                    };
                @endphp

                <p>Ada pembaruan status pada lamaran pekerjaan yang Anda ajukan untuk posisi <strong>{{ $jobTitle }}</strong> di <strong>{{ $companyName }}</strong>.</p>

                <div class="status-card">
                    <div style="font-size: 13px; color: #64748b; font-weight: 600;">Status Terbaru:</div>
                    <span class="status-badge {{ $badgeClass }}">{{ $statusText }}</span>
                </div>

                @if($status === 'pending')
                    <p>Lamaran Anda telah kami terima dan akan segera ditinjau oleh tim rekrutmen. Silakan pantau perkembangan lamaran Anda secara berkala melalui sistem.</p>
                @elseif($status === 'reviewed')
                    <p>Tim HRD saat ini sedang meninjau dokumen dan kualifikasi yang Anda kirimkan. Kami akan segera menginformasikan langkah seleksi selanjutnya.</p>
                @elseif($status === 'shortlisted')
                    <p>Selamat! Kualifikasi Anda memenuhi syarat tahap awal dan berkas Anda berhasil lolos seleksi. Tim HRD akan segera menjadwalkan tahap tes atau wawancara.</p>
                @elseif($status === 'test_invited')
                    <p>Anda diundang untuk mengikuti tes asesmen/psikotes seleksi. Silakan masuk ke akun Anda untuk memulai tes pengerjaan online.</p>
                @elseif($status === 'interview')
                    <p>Selamat! Anda diundang untuk mengikuti sesi Wawancara Kerja. Silakan periksa rincian jadwal atau instruksi di bawah ini.</p>
                @elseif($status === 'accepted')
                    <p>Selamat! Berdasarkan hasil seluruh rangkaian proses seleksi, Anda dinyatakan <strong>DITERIMA</strong> untuk posisi ini. Tim HRD akan menghubungi Anda lebih lanjut terkait proses penawaran kerja (Offering Letter) dan pengurusan berkas.</p>
                @elseif($status === 'rejected')
                    <p>Terima kasih atas minat dan partisipasi Anda melamar di perusahaan kami. Mohon maaf saat ini kualifikasi Anda belum sesuai dengan kebutuhan posisi ini. Jangan berkecil hati dan tetap semangat dalam mencari peluang karier berikutnya.</p>
                @endif

                @if(!empty($notes))
                    <div class="notes-box">
                        <div class="notes-title">Catatan dari HRD / Perusahaan:</div>
                        <div style="white-space: pre-line; color: #1e3a8a;">{{ $notes }}</div>
                    </div>
                @endif

                <div class="btn-wrapper">
                    <a href="{{ route('seeker.applications.show', $application->id) }}" class="btn">Lihat Detail Lamaran Saya</a>
                </div>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name', 'Portal Rekrutmen') }}. All rights reserved.</p>
                <p>Email ini dikirimkan secara otomatis oleh sistem. Mohon tidak membalas email ini secara langsung.</p>
            </div>
        </div>
    </div>
</body>
</html>
