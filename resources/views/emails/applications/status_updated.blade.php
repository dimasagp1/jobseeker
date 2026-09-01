<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Status Lamaran Pekerjaan</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 15px;
        }
        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            padding: 35px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 6px 0 0 0;
            font-size: 13px;
            opacity: 0.9;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .content {
            padding: 35px 30px;
            line-height: 1.7;
        }
        .greeting {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 18px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            margin: 22px 0;
            border: 1px solid #e2e8f0;
        }
        .meta-table td {
            padding: 12px 18px;
            font-size: 13px;
            border-bottom: 1px solid #f1f5f9;
        }
        .meta-table td.label {
            font-weight: 600;
            color: #64748b;
            width: 40%;
            background-color: #f1f5f9;
        }
        .meta-table td.value {
            font-weight: 700;
            color: #1e293b;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .bg-pending { background-color: #fef3c7; color: #92400e; }
        .bg-reviewed { background-color: #e0f2fe; color: #075985; }
        .bg-shortlisted { background-color: #dcfce7; color: #166534; }
        .bg-test { background-color: #f3e8ff; color: #6b21a8; }
        .bg-interview { background-color: #ffedd5; color: #9a3412; }
        .bg-accepted { background-color: #dcfce7; color: #14532d; }
        .bg-rejected { background-color: #ffe4e6; color: #9f1239; }

        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            font-size: 14px;
        }
        .notes-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        .notes-title {
            font-weight: 700;
            color: #1e40af;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0 20px 0;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            padding: 14px 36px;
            border-radius: 50px;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h2>{{ config('app.name', 'Portal Rekrutmen') }}</h2>
                <p>Surat Pemberitahuan Hasil Seleksi Rekrutmen</p>
            </div>

            <div class="content">
                <div class="greeting">
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
                        'pending' => 'Lamaran Berhasil Diterima Sistem',
                        'reviewed' => 'Dalam Tahap Evaluasi HRD',
                        'shortlisted' => 'Lolos Seleksi Berkas / Kualifikasi',
                        'test_invited' => 'Undangan Tes Asesmen & Psikotes',
                        'test_in_progress' => 'Sedang Pelaksanaan Tes',
                        'test_completed' => 'Tes Asesmen Selesai',
                        'interview' => 'Undangan Wawancara Kerja',
                        'accepted' => 'Diterima Bekerja (Accepted)',
                        'rejected' => 'Belum Lolos Seleksi (Rejected)',
                        default => ucfirst($status)
                    };
                @endphp

                <p style="margin-bottom: 16px;">Dengan hormat,</p>

                <p>Sehubungan dengan proses penerimaan tenaga kerja di <strong>{{ $companyName }}</strong>, bersama surat elektronik ini kami dari Tim Rekrutmen menyampaikan pembaruan status atas berkas pendaftaran kerja yang Anda ajukan.</p>

                {{-- Tabel Ringkasan Lamaran --}}
                <table class="meta-table">
                    <tr>
                        <td class="label">Nomor Registrasi</td>
                        <td class="value">#APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Posisi Dilamar</td>
                        <td class="value">{{ $jobTitle }}</td>
                    </tr>
                    <tr>
                        <td class="label">Perusahaan / Unit</td>
                        <td class="value">{{ $companyName }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Pengajuan</td>
                        <td class="value">{{ $application->created_at->translatedFormat('d F Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td class="label">Status Tahapan saat Ini</td>
                        <td class="value">
                            <span class="status-badge {{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                </table>

                {{-- Penjelasan Detail Berdasarkan Status --}}
                <div class="info-box">
                    @if($status === 'pending')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Penerimaan Dokumen</strong>
                        <p style="margin: 0;">Kami telah menerima seluruh data identitas, kuesioner pra-seleksi, serta berkas lampiran Anda. Saat ini pendaftaran Anda terdaftar dalam antrean verifikasi administrasi awal oleh Tim HRD. Estimasi proses verifikasi berkas berlangsung selama 1-3 hari kerja.</p>

                    @elseif($status === 'reviewed')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Evaluasi Dokumen & Kualifikasi</strong>
                        <p style="margin: 0;">Berkas Curriculum Vitae (CV), kuesioner pra-seleksi, serta riwayat pengalaman kerja Anda sedang dievaluasi secara cermat oleh Tim Rekrutmen dan Manajemen Teknis untuk menilai kesesuaian kualifikasi dengan kebutuhan posisi ini.</p>

                    @elseif($status === 'shortlisted')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Lolos Kualifikasi Berkas</strong>
                        <p style="margin: 0;">Dengan senang hati kami beritahukan bahwa kualifikasi dan pengalaman kerja Anda dinyatakan <strong>MEMENUHI SYARAT KUALIFIKASI</strong> pada tahap seleksi berkas. Tim Rekrutmen kami akan segera mengalokasikan jadwal tes seleksi atau wawancara untuk Anda.</p>

                    @elseif($status === 'test_invited')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Undangan Pelaksanaan Tes Asesmen & Psikotes</strong>
                        <p style="margin: 0;">Anda diundang untuk mengikuti tahapan Tes Seleksi Asesmen & Psikotes secara online (terdiri dari tes ketahanan kerja Kraepelin, kepribadian DISC, kepemimpinan MSDT, dan PAPI Kostick). Mohon persiapkan perangkat komputer/laptop dengan koneksi internet yang stabil dan masuk ke akun portal Anda untuk memulai pengerjaan tes.</p>

                    @elseif($status === 'interview')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Undangan Sesi Wawancara Kerja</strong>
                        <p style="margin: 0;">Selamat! Anda melangkah ke tahapan Wawancara Kerja (Interview). Mohon perhatikan secara teliti instruksi, waktu, serta lokasi/link wawancara yang telah ditetapkan oleh tim HRD pada bagian catatan di bawah ini.</p>

                    @elseif($status === 'accepted')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Penerimaan Bekerja (Accepted)</strong>
                        <p style="margin: 0;">Berdasarkan hasil penilaian komprehensif dari seluruh rangkaian seleksi administrasi, tes psikotes, dan wawancara, kami dengan bangga menyatakan bahwa Anda <strong>DITERIMA BEKERJA</strong> untuk posisi <strong>{{ $jobTitle }}</strong> di <strong>{{ $companyName }}</strong>. Tim HRD kami akan segera menghubungi Anda terkait penandatanganan Dokumen Penawaran Kerja (Offering Letter) dan tahap persiapan *onboarding*.</p>

                    @elseif($status === 'rejected')
                        <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Detail Tahapan: Pemberitahuan Hasil Seleksi</strong>
                        <p style="margin: 0;">Kami menyampaikan terima kasih yang sebesar-besarnya atas apresiasi dan waktu yang Anda luangkan dalam mengikuti proses rekrutmen di perusahaan kami. Setelah melalui pertimbangan yang cermat, kami menginformasikan bahwa saat ini kami belum dapat melanjutkan pendaftaran Anda ke tahapan berikutnya. Keputusan ini tidak mengurangi penghargaan kami atas potensi yang Anda miliki, dan kami mendorong Anda untuk tetap memantau peluang karier lainnya di portal kami di masa mendatang.</p>
                    @endif
                </div>

                {{-- Catatan Tambahan Khusus dari HRD --}}
                @if(!empty($notes))
                    <div class="notes-box">
                        <div class="notes-title">📌 Instruksi Tambahan dari Tim HRD:</div>
                        <div style="white-space: pre-line; color: #1e3a8a; font-size: 13.5px; line-height: 1.6;">{{ $notes }}</div>
                    </div>
                @endif

                <p style="margin-top: 24px; font-size: 13.5px;">Bapak/Ibu/Saudara/i dapat memeriksa status terbaru, jadwal, serta rincian dokumen Anda secara langsung melalui Portal Rekrutmen resmi kami dengan mengklik tombol di bawah ini:</p>

                <div class="btn-wrapper">
                    <a href="{{ route('seeker.applications.show', $application->id) }}" class="btn">Masuk ke Portal & Lihat Detail Lamaran</a>
                </div>

                {{-- Penutup Formal --}}
                <div style="margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 22px; font-size: 13.5px;">
                    <p class="mb-2">Demikian informasi ini kami sampaikan. Atas perhatian dan kerja sama Bapak/Ibu/Saudara/i, kami ucapkan terima kasih.</p>
                    <br>
                    <p style="margin-bottom: 4px;">Hormat kami,</p>
                    <p style="font-weight: 700; color: #0f172a; margin: 0; font-size: 14px;">Tim Rekrutmen & Department HRD</p>
                    <p style="color: #64748b; margin: 2px 0 0 0;">{{ $companyName }}</p>
                </div>
            </div>

            {{-- Footer Informasi Support --}}
            <div class="footer">
                <p style="margin-bottom: 6px;">&copy; {{ date('Y') }} <strong>{{ $companyName }}</strong>. All rights reserved.</p>
                <p style="margin: 0;">Surat elektronik ini dikirimkan secara otomatis oleh Sistem Manajemen Rekrutmen Resmi. Mohon tidak membalas email ini secara langsung.</p>
            </div>
        </div>
    </div>
</body>
</html>
