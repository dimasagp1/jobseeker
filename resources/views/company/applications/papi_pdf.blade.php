<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PAPI Kostick - {{ $application->user->name }}</title>
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

        .header { border-bottom: 3px solid #0284c7; padding-bottom: 8px; margin-bottom: 15px; }
        .company-name { font-size: 20px; font-weight: 800; color: #0284c7; margin: 0; letter-spacing: -0.5px; }
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
            background: #1e293b; 
            padding: 5px 10px; 
            border-radius: 4px; 
            margin-bottom: 10px; 
            text-transform: uppercase; 
        }

        .papi-table { width: 100%; margin-bottom: 15px; border: 1px solid #cbd5e1; }
        .papi-table th { background: #0284c7; color: #ffffff; font-size: 10px; padding: 6px; text-transform: uppercase; text-align: left; }
        .papi-table td { font-size: 10px; padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
        .papi-table tr:nth-child(even) { background: #f8fafc; }
        .score-badge { font-weight: bold; color: #0284c7; background: #e0f2fe; padding: 2px 6px; border-radius: 3px; font-size: 10px; }

        .summary-box { background: #f0f9ff; border: 1px solid #bae6fd; padding: 12px; border-radius: 6px; margin-top: 10px; }
        .summary-title { font-size: 11px; font-weight: bold; color: #0369a1; margin-bottom: 4px; }
        .summary-text { font-size: 10px; color: #0c4a6e; line-height: 1.4; }

        .footer { position: fixed; bottom: -0.5cm; left: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>

    @php
        $rawData = $papiResult->final_score ?? '{}';
        $papiData = is_string($rawData) ? json_decode($rawData, true) : ($rawData ?? []);
        
        $dimensions = [
            'G' => ['name' => 'Role Hard Worker', 'desc' => 'Hasrat untuk bekerja keras & tekun'],
            'L' => ['name' => 'Leadership Role', 'desc' => 'Peran kepemimpinan & proyeksi pengaruh'],
            'I' => ['name' => 'Ease in Decision Making', 'desc' => 'Kemudahan membuat keputusan'],
            'T' => ['name' => 'Pace / Working Speed', 'desc' => 'Tempo & kecepatan menyelesaikan tugas'],
            'V' => ['name' => 'Vigor / Energy', 'desc' => 'Stamina fisik & tingkat energi kerja'],
            'S' => ['name' => 'Social Extension', 'desc' => 'Kebutuhan berinteraksi & bersosialisasi'],
            'R' => ['name' => 'Theoretical Type', 'desc' => 'Orientasi pada analisis teori & konsep'],
            'D' => ['name' => 'Interest in Working with Details', 'desc' => 'Ketelitian pada detail operasional'],
            'C' => ['name' => 'Organized Type', 'desc' => 'Tingkat keteraturan & perencanaan'],
            'E' => ['name' => 'Emotional Expressiveness', 'desc' => 'Pengungkapan emosi & keterbukaan'],
            'N' => ['name' => 'Need to Finish Task', 'desc' => 'Kebutuhan menyelesaikan tugas sendiri'],
            'A' => ['name' => 'Need to Achieve', 'desc' => 'Ambisi untuk sukses & berprestasi'],
            'P' => ['name' => 'Need to Control Others', 'desc' => 'Kebutuhan mengendalikan orang lain'],
            'X' => ['name' => 'Need to be Noticed', 'desc' => 'Kebutuhan mendapat perhatian'],
            'B' => ['name' => 'Need to Belong to Groups', 'desc' => 'Kebutuhan diterima dalam kelompok'],
            'O' => ['name' => 'Need for Closeness & Affection', 'desc' => 'Kebutuhan akan kehangatan hubungan'],
            'Z' => ['name' => 'Need for Change', 'desc' => 'Kebutuhan akan variasi & tantangan'],
            'K' => ['name' => 'Need to be Forceful', 'desc' => 'Ketegasan & sikap agresif positif'],
            'F' => ['name' => 'Need to Support Authority', 'desc' => 'Dukungan terhadap atasan & otoritas'],
            'W' => ['name' => 'Need for Rules & Supervision', 'desc' => 'Kebutuhan instruksi & struktur jelas'],
        ];
    @endphp

    <div class="watermark">CONFIDENTIAL</div>

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
                        {{ $siteSettings->pdf_header_subtitle ?? 'Executive Summary - PAPI Kostick Personality Test' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMASI KANDIDAT --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td><td class="value">: {{ $application->user->name }}</td>
            <td class="label">ID / Tgl Tes</td><td class="value">: PAPI-{{ $papiResult->id }} / {{ $papiResult->completed_at ? $papiResult->completed_at->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Posisi Tujuan</td><td class="value">: {{ $application->job->title }}</td>
            <td class="label">Status Tes</td><td class="value">: Selesai (Completed)</td>
        </tr>
    </table>

    {{-- TABEL 20 DIMENSI PAPI KOSTICK --}}
    <div class="section-title">A. Profil 20 Dimensi Peran & Kebutuhan Kerja</div>
    <table class="papi-table">
        <thead>
            <tr>
                <th style="width: 8%;">Kode</th>
                <th style="width: 32%;">Dimensi Peran / Kebutuhan</th>
                <th style="width: 48%;">Deskripsi Aspek Psikologi</th>
                <th style="width: 12%; text-align: center;">Skor (0-9)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dimensions as $code => $info)
                @php $score = $papiData[$code] ?? 0; @endphp
                <tr>
                    <td style="font-weight: bold; text-align: center;">{{ $code }}</td>
                    <td><b>{{ $info['name'] }}</b></td>
                    <td style="color: #475569;">{{ $info['desc'] }}</td>
                    <td style="text-align: center;"><span class="score-badge">{{ $score }} Poin</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- KESIMPULAN --}}
    <div class="section-title">B. Kesimpulan Profil Psikologi PAPI</div>
    <div class="summary-box">
        <div class="summary-title">Ringkasan Karakteristik Kerja Kandidat</div>
        <div class="summary-text">
            {{ $papiResult->interpretation ?? 'Profil PAPI Kostick mengukur kecenderungan perilaku dan peran kerja kandidat secara komprehensif untuk disesuaikan dengan kultur dan tuntutan posisi kerja.' }}
        </div>
    </div>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Portal Karir {{ $siteSettings->company_name ?? 'HerbaTech' }} dan bersifat rahasia.
    </div>

</body>
</html>
