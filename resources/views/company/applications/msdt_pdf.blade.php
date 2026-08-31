<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan MSDT Kepemimpinan - {{ $application->user->name }}</title>
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

        .header { border-bottom: 3px solid #dc2626; padding-bottom: 8px; margin-bottom: 15px; }
        .company-name { font-size: 20px; font-weight: 800; color: #dc2626; margin: 0; letter-spacing: -0.5px; }
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

        .style-banner {
            background: #991b1b;
            color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
        }
        .style-badge { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 10px; display: inline-block; margin-bottom: 5px; }
        .style-title { font-size: 20px; font-weight: bold; margin: 5px 0; }
        .style-desc { font-size: 10px; opacity: 0.95; line-height: 1.4; max-width: 90%; margin: 0 auto; }

        .progress-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 10px 12px; border-radius: 6px; margin-bottom: 10px; }
        .progress-title { font-size: 11px; font-weight: bold; color: #1e293b; }
        .progress-score { float: right; font-size: 11px; font-weight: bold; color: #dc2626; }
        .progress-bg { background: #e2e8f0; height: 8px; border-radius: 4px; width: 100%; margin-top: 5px; margin-bottom: 4px; }
        .progress-bar { height: 8px; border-radius: 4px; background: #dc2626; }
        .progress-bar-to { background: #2563eb; }
        .progress-bar-ro { background: #0284c7; }
        .progress-bar-e { background: #16a34a; }
        .progress-note { font-size: 9px; color: #64748b; }

        .analysis-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 12px; border-radius: 6px; margin-top: 15px; }
        .analysis-title { font-size: 11px; font-weight: bold; color: #1e40af; margin-bottom: 6px; text-transform: uppercase; }
        .analysis-item { font-size: 10px; color: #1e3a8a; margin-bottom: 4px; line-height: 1.4; }

        .footer { position: fixed; bottom: -0.5cm; left: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>

    @php
        $rawData = $msdtResult->final_score ?? '{}';
        $msdtData = is_string($rawData) ? json_decode($rawData, true) : $rawData;
        
        $to_score = $msdtData['TO'] ?? 0; 
        $ro_score = $msdtData['RO'] ?? 0; 
        $e_score  = $msdtData['E'] ?? 0;  
        $style    = str_replace('"', '', $msdtData['style'] ?? 'Deserter');

        $styleTitles = [
            'Executive' => 'Executive (Eksekutif)',
            'Developer' => 'Developer (Pembina)',
            'Benevolent Autocrat' => 'Benevolent Autocrat (Otokrat Bijak)',
            'Bureaucrat' => 'Bureaucrat (Birokrat)',
            'Compromiser' => 'Compromiser (Kompromis)',
            'Missionary' => 'Missionary (Misionaris)',
            'Autocrat' => 'Autocrat (Otokrat Murni)',
            'Deserter' => 'Deserter (Pelarian)',
        ];
        $styleTitle = $styleTitles[$style] ?? $style;

        $styleDescriptions = [
            'Executive' => 'Pemimpin yang memiliki efektivitas tinggi. Mampu menyeimbangkan antara orientasi pada penyelesaian tugas dan perhatian pada hubungan antarpribadi dalam tim.',
            'Developer' => 'Pemimpin yang memprioritaskan pengembangan bawahan. Sangat peduli pada hubungan interpersonal dan memiliki rasa percaya tinggi pada kemampuan timnya.',
            'Benevolent Autocrat' => 'Pemimpin yang fokus utamanya adalah tugas dan hasil, namun tetap tahu bagaimana mengarahkan bawahan tanpa menimbulkan penolakan keras.',
            'Bureaucrat' => 'Pemimpin yang sangat patuh pada aturan, SOP, dan sistem. Memastikan seluruh operasional berjalan sesuai regulasi perusahaan.',
            'Compromiser' => 'Pemimpin yang peka terhadap pendapat dalam tim namun terkadang mengambil keputusan kompromi yang kurang tegas.',
            'Missionary' => 'Pemimpin yang sangat mengutamakan keharmonisan dan suasana ramah hingga kadang kurang tegas pada penyelesaian target.',
            'Autocrat' => 'Pemimpin yang memfokuskan 100% perhatian pada tugas secara instruksional dan menuntut hasil tanpa kompromi.',
            'Deserter' => 'Individu yang cenderung pasif dalam arahan, menghindari konflik, dan membiarkan tim berjalan secara mandiri.',
        ];
        $styleDesc = $styleDescriptions[$style] ?? $msdtResult->interpretation;
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
                        {{ $siteSettings->pdf_header_subtitle ?? 'Executive Summary - MSDT Leadership Assessment' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMASI KANDIDAT --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td><td class="value">: {{ $application->user->name }}</td>
            <td class="label">ID / Tgl Tes</td><td class="value">: MSDT-{{ $msdtResult->id }} / {{ $msdtResult->completed_at ? $msdtResult->completed_at->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Posisi Tujuan</td><td class="value">: {{ $application->job->title }}</td>
            <td class="label">Status Tes</td><td class="value">: Selesai (Completed)</td>
        </tr>
    </table>

    {{-- GAYA KEPEMIMPINAN UTAMA --}}
    <div class="section-title">A. Gaya Kepemimpinan Dominan</div>
    <div class="style-banner">
        <div class="style-badge">Profil Manajerial MSDT</div>
        <div class="style-title">{{ strtoupper($styleTitle) }}</div>
        <div class="style-desc">"{{ $styleDesc }}"</div>
    </div>

    {{-- SKOR DIMENSI KEPEMIMPINAN --}}
    <div class="section-title">B. Dimensi Metrik Kepemimpinan (3 Faktor Utama)</div>
    
    <div class="progress-box">
        <div class="progress-title">
            Orientasi Tugas (Task Orientation - TO)
            <span class="progress-score">{{ $to_score }} / 20</span>
        </div>
        <div class="progress-bg">
            <div class="progress-bar progress-bar-to" style="width: {{ min(100, ($to_score/20)*100) }}%;"></div>
        </div>
        <div class="progress-note">Tingkat fokus pada target kerja, efisiensi operasional, dan penyelesaian tugas tepat waktu.</div>
    </div>

    <div class="progress-box">
        <div class="progress-title">
            Orientasi Relasi (Relationships Orientation - RO)
            <span class="progress-score">{{ $ro_score }} / 20</span>
        </div>
        <div class="progress-bg">
            <div class="progress-bar progress-bar-ro" style="width: {{ min(100, ($ro_score/20)*100) }}%;"></div>
        </div>
        <div class="progress-note">Tingkat empati, komunikasi interaktif, pembinaan bawahan, dan perhatian pada keharmonisan tim.</div>
    </div>

    <div class="progress-box">
        <div class="progress-title">
            Efektivitas Situasional (Effectiveness - E)
            <span class="progress-score">{{ $e_score }} / 20</span>
        </div>
        <div class="progress-bg">
            <div class="progress-bar progress-bar-e" style="width: {{ min(100, ($e_score/20)*100) }}%;"></div>
        </div>
        <div class="progress-note">Kemampuan dalam menyesuaikan gaya kepemimpinan dengan tuntutan situasi dan kedewasaan tim.</div>
    </div>

    {{-- KESIMPULAN EVALUASI --}}
    <div class="section-title" style="margin-top: 15px;">C. Catatan Evaluasi Manajerial</div>
    <div class="analysis-box">
        <div class="analysis-title">Rekomendasi Penempatan & Pengelolaan</div>
        <div class="analysis-item"><b>• Orientasi Kerja:</b> {{ $to_score >= 10 ? 'Kandidat memiliki ketegasan tinggi dalam mengejar target operasional.' : 'Kandidat cenderung santai dalam menetapkan tenggat waktu tugas.' }}</div>
        <div class="analysis-item"><b>• Hubungan Antarpribadi:</b> {{ $ro_score >= 10 ? 'Kandidat mampu membangun komunikasi yang hangat dan merangkul bawahan.' : 'Kandidat lebih suka berfokus pada data/tugas dibanding interaksi sosial.' }}</div>
        <div class="analysis-item"><b>• Adaptabilitas Kepemimpinan:</b> {{ $e_score >= 10 ? 'Tingkat efektivitas situasional baik (mampu memimpin dalam berbagai dinamika tim).' : 'Membutuhkan pendampingan untuk meningkatkan efektivitas pengambilan keputusan.' }}</div>
    </div>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Portal Karir {{ $siteSettings->company_name ?? 'HerbaTech' }} dan bersifat rahasia.
    </div>

</body>
</html>
