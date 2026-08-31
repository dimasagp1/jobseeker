<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Lengkap Asesmen Psikometri - {{ $application->user->name }}</title>
    <style>
        @page { margin: 1cm 1.5cm; }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            color: #1e293b; 
            line-height: 1.4;
            margin: 0; 
            padding: 0; 
            background-color: #ffffff;
        }
        
        .watermark {
            position: fixed;
            top: 35%;
            left: 10%;
            transform: rotate(-45deg);
            font-size: 70px;
            color: rgba(226, 232, 240, 0.4);
            z-index: -1000;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header { border-bottom: 3px solid #0f172a; padding-bottom: 8px; margin-bottom: 15px; }
        .company-name { font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px; }
        .report-type { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-top: 3px; }
        .confidential { float: right; font-size: 9px; background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        .info-table { margin-bottom: 15px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .info-table td { padding: 6px 12px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; font-weight: bold; width: 20%; }
        .value { color: #1e293b; font-weight: bold; width: 30%; }

        .section-title { 
            font-size: 11px; 
            font-weight: bold; 
            color: #ffffff; 
            background: #0f172a; 
            padding: 5px 10px; 
            border-radius: 4px; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
        }

        .page-break {
            page-break-before: always;
        }

        /* Factor Cards / Boxes */
        .summary-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 10px 12px; border-radius: 6px; margin-bottom: 12px; }
        .summary-text { font-size: 10px; color: #334155; line-height: 1.5; }

        .progress-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 8px 10px; border-radius: 6px; margin-bottom: 8px; }
        .progress-title { font-size: 10px; font-weight: bold; color: #1e293b; }
        .progress-score { float: right; font-size: 10px; font-weight: bold; color: #0f172a; }
        .progress-bg { background: #e2e8f0; height: 6px; border-radius: 3px; width: 100%; margin-top: 4px; margin-bottom: 3px; }
        .progress-bar { height: 6px; border-radius: 3px; background: #2563eb; }

        .papi-table { width: 100%; margin-bottom: 15px; border: 1px solid #cbd5e1; }
        .papi-table th { background: #0f172a; color: #ffffff; font-size: 9px; padding: 5px; text-transform: uppercase; text-align: left; }
        .papi-table td { font-size: 9px; padding: 4px 6px; border-bottom: 1px solid #e2e8f0; }
        .papi-table tr:nth-child(even) { background: #f8fafc; }

        .grid-table { width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-left: -8px; margin-right: -8px; }
        .grid-cell { width: 50%; vertical-align: top; }

        .footer { position: fixed; bottom: -0.5cm; left: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>

    <div class="watermark">CONFIDENTIAL</div>

    {{-- HEADER UTAMA --}}
    <div class="header">
        <div class="confidential">DOKUMEN RAHASIA</div>
        <table style="width: 100%; border: none;">
            <tr>
                @if(isset($logoBase64) && $logoBase64)
                    <td style="width: 50px; vertical-align: middle; border: none; padding: 0 10px 0 0;">
                        <img src="{{ $logoBase64 }}" style="max-height: 38px; max-width: 120px; object-fit: contain;">
                    </td>
                @endif
                <td style="vertical-align: middle; border: none; padding: 0;">
                    <p class="company-name" style="margin: 0;">
                        {{ $siteSettings->pdf_header_title ?? ($siteSettings->company_name ?? 'HerbaTech') }}
                    </p>
                    <p class="report-type" style="margin: 3px 0 0 0;">
                        {{ $siteSettings->pdf_header_subtitle ?? 'Executive Summary - Laporan Lengkap Asesmen Psikometri' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMASI KANDIDAT --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td><td class="value">: {{ $application->user->name }}</td>
            <td class="label">Email Candidates</td><td class="value">: {{ $application->user->email }}</td>
        </tr>
        <tr>
            <td class="label">Posisi Melamar</td><td class="value">: {{ $application->job->title }}</td>
            <td class="label">Tgl Laporan</td><td class="value">: {{ now()->format('d M Y') }}</td>
        </tr>
    </table>

    {{-- DOKUMEN RINGKASAN REKAPITULASI --}}
    <div class="section-title">Ringkasan Kelengkapan Tes Psikotes</div>
    <table class="info-table mb-3">
        <tr>
            <td class="label">1. Tes Kraepelin</td>
            <td class="value" style="color: {{ $kraepelinTest ? '#16a34a' : '#dc2626' }};">
                : {{ $kraepelinTest ? 'Selesai (Completed)' : 'Belum Dikerjakan' }}
            </td>
            <td class="label">2. Tes DISC</td>
            <td class="value" style="color: {{ $discResult ? '#16a34a' : '#dc2626' }};">
                : {{ $discResult ? 'Selesai (Completed)' : 'Belum Dikerjakan' }}
            </td>
        </tr>
        <tr>
            <td class="label">3. Tes MSDT</td>
            <td class="value" style="color: {{ $msdtResult ? '#16a34a' : '#dc2626' }};">
                : {{ $msdtResult ? 'Selesai (Completed)' : 'Belum Dikerjakan' }}
            </td>
            <td class="label">4. Tes PAPI Kostick</td>
            <td class="value" style="color: {{ $papiResult ? '#16a34a' : '#dc2626' }};">
                : {{ $papiResult ? 'Selesai (Completed)' : 'Belum Dikerjakan' }}
            </td>
        </tr>
    </table>

    {{-- BAGIAN 1: KRAEPELIN --}}
    @if($kraepelinTest)
        <div class="section-title" style="background: #4338ca;">1. Hasil Tes Kraepelin (Kecepatan, Ketelitian, Stabilitas & Stamina)</div>
        <div class="summary-box">
            <div class="summary-text">
                Berdasarkan hasil pengerjaan, kandidat memiliki tingkat kecepatan kerja <b>{{ $kraepelinTest->panker >= 15 ? 'tinggi' : ($kraepelinTest->panker >= 10 ? 'sedang / rata-rata' : 'rendah') }}</b> 
                ({{ round($kraepelinTest->panker, 1) }} Baris) dengan tingkat ketelitian <b>{{ $kraepelinTest->tianker <= 5 ? 'sangat baik' : ($kraepelinTest->tianker <= 15 ? 'cukup baik' : 'kurang teliti') }}</b> 
                ({{ $kraepelinTest->tianker }} Error). Stabilitas emosi tergolong <b>{{ $kraepelinTest->janker <= 5 ? 'sangat stabil' : 'fluktuatif' }}</b> ({{ $kraepelinTest->janker }} Poin) 
                dan stamina <b>{{ $kraepelinTest->ganker >= 0 ? 'positif / konsisten' : 'menurun' }}</b> ({{ $kraepelinTest->ganker }}).
            </div>
        </div>
    @endif

    {{-- BAGIAN 2: DISC ASSESSMENT --}}
    @if($discResult)
        @php
            $discData = is_array($discResult->final_score) ? $discResult->final_score : [];
            $d_score = $discData['D'] ?? 0; $i_score = $discData['I'] ?? 0;
            $s_score = $discData['S'] ?? 0; $c_score = $discData['C'] ?? 0;
            $scores = ['D' => $d_score, 'I' => $i_score, 'S' => $s_score, 'C' => $c_score];
            arsort($scores); $keys = array_keys($scores); $primary = $keys[0]; $secondary = $keys[1] ?? $keys[0];
        @endphp
        <div class="section-title" style="background: #16a34a; margin-top: 15px;">2. Hasil Evaluasi Perilaku DISC (Kode Profil: {{ $primary }}{{ $secondary }})</div>
        <div class="summary-box">
            <div class="summary-text">
                <b>Profil Karakter Dominan:</b> {{ $primary }}{{ $secondary }}<br>
                <b>Skor Karakter:</b> D (Dominance): {{ $d_score }} | I (Influence): {{ $i_score }} | S (Steadiness): {{ $s_score }} | C (Compliance): {{ $c_score }}<br>
                <b>Interpretasi Singkat:</b> {{ $discResult->interpretation ?? 'Kandidat menunjukkan preferensi perilaku kerja sesuai pola grafik DISC di atas.' }}
            </div>
        </div>
    @endif

    {{-- PAGE BREAK UNTUK MSDT & PAPI --}}
    @if($msdtResult || $papiResult)
        <div class="page-break"></div>
    @endif

    {{-- BAGIAN 3: MSDT --}}
    @if($msdtResult)
        @php
            $rawData = $msdtResult->final_score ?? '{}';
            $msdtData = is_string($rawData) ? json_decode($rawData, true) : $rawData;
            $to_score = $msdtData['TO'] ?? 0; 
            $ro_score = $msdtData['RO'] ?? 0; 
            $e_score  = $msdtData['E'] ?? 0;  
            $style    = str_replace('"', '', $msdtData['style'] ?? 'Deserter');
        @endphp
        <div class="section-title" style="background: #dc2626;">3. Hasil MSDT Kepemimpinan (Gaya Manajerial: {{ strtoupper($style) }})</div>
        <div class="summary-box">
            <div class="summary-text">
                <b>Gaya Kepemimpinan Utama:</b> {{ strtoupper($style) }}<br>
                <b>Dimensi Metrik:</b> Orientasi Tugas (TO): {{ $to_score }}/20 | Orientasi Relasi (RO): {{ $ro_score }}/20 | Efektivitas (E): {{ $e_score }}/20<br>
                <b>Catatan Analisis:</b> {{ $msdtResult->interpretation ?? 'Analisis kepemimpinan menilai gaya interaksi manajerial dan efektivitas situasional kandidat.' }}
            </div>
        </div>
    @endif

    {{-- BAGIAN 4: PAPI KOSTICK --}}
    @if($papiResult)
        @php
            $rawData = $papiResult->final_score ?? '{}';
            $papiData = is_string($rawData) ? json_decode($rawData, true) : ($rawData ?? []);
            $dimensions = [
                'G' => 'Role Hard Worker', 'L' => 'Leadership Role', 'I' => 'Decision Making', 'T' => 'Paced Speed', 'V' => 'Vigor Energy',
                'S' => 'Social Extension', 'R' => 'Theoretical Type', 'D' => 'Detail Interest', 'C' => 'Organized Type', 'E' => 'Emotional Control',
                'N' => 'Need Finish Task', 'A' => 'Need to Achieve', 'P' => 'Need Control Others', 'X' => 'Need to be Noticed', 'B' => 'Need Belong Group',
                'O' => 'Need Affection', 'Z' => 'Need for Change', 'K' => 'Need Aggressive', 'F' => 'Need Support Authority', 'W' => 'Need Rules & SOP'
            ];
        @endphp
        <div class="section-title" style="background: #0284c7; margin-top: 15px;">4. Hasil PAPI Kostick (20 Dimensi Peran & Kebutuhan)</div>
        <table class="papi-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 70%;">Dimensi Psikologi PAPI Kostick</th>
                    <th style="width: 20%; text-align: center;">Skor (0-9)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dimensions as $code => $name)
                    @php $score = $papiData[$code] ?? 0; @endphp
                    <tr>
                        <td style="font-weight: bold; text-align: center;">{{ $code }}</td>
                        <td>{{ $name }}</td>
                        <td style="text-align: center; font-weight: bold; color: #0284c7;">{{ $score }} Poin</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dokumen Laporan Lengkap ini dihasilkan secara otomatis oleh Sistem Portal Karir {{ $siteSettings->company_name ?? 'HerbaTech' }} dan bersifat rahasia.
    </div>

</body>
</html>
