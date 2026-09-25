<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan DISC Assessment - {{ $application->user->name }}</title>
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

        .header { border-bottom: 3px solid #16a34a; padding-bottom: 8px; margin-bottom: 15px; }
        .company-name { font-size: 20px; font-weight: 800; color: #16a34a; margin: 0; letter-spacing: -0.5px; }
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

        .disc-banner {
            background: #166534;
            color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .disc-badge { font-size: 9px; text-transform: uppercase; font-weight: bold; background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 10px; display: inline-block; margin-bottom: 5px; }
        .disc-title { font-size: 18px; font-weight: bold; margin: 3px 0; }
        .disc-desc { font-size: 10px; opacity: 0.95; line-height: 1.4; }

        .factor-card { border: 1px solid #cbd5e1; padding: 10px; background: #f8fafc; border-radius: 6px; margin-bottom: 10px; }
        .factor-d { border-left: 4px solid #dc2626; }
        .factor-i { border-left: 4px solid #d97706; }
        .factor-s { border-left: 4px solid #16a34a; }
        .factor-c { border-left: 4px solid #2563eb; }
        
        .factor-title { font-size: 11px; font-weight: bold; color: #1e293b; }
        .factor-score { float: right; font-weight: bold; font-size: 11px; }
        .progress-bg { background: #e2e8f0; height: 6px; border-radius: 3px; width: 100%; margin-top: 5px; margin-bottom: 5px; }
        .progress-bar { height: 6px; border-radius: 3px; }
        .bg-d { background: #dc2626; }
        .bg-i { background: #d97706; }
        .bg-s { background: #16a34a; }
        .bg-c { background: #2563eb; }

        .factor-text { font-size: 9px; color: #475569; }

        .grid-table { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin-left: -10px; margin-right: -10px; }
        .grid-cell { width: 50%; vertical-align: top; }

        .footer { position: fixed; bottom: -0.5cm; left: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>

    @php
        $discData = is_array($discResult->final_score) ? $discResult->final_score : [];
        $d_score = $discData['D'] ?? 0;
        $i_score = $discData['I'] ?? 0;
        $s_score = $discData['S'] ?? 0;
        $c_score = $discData['C'] ?? 0;

        $scores = ['D' => $d_score, 'I' => $i_score, 'S' => $s_score, 'C' => $c_score];
        arsort($scores);
        $keys = array_keys($scores);
        $primary = $keys[0];
        $secondary = $keys[1] ?? $keys[0];
        $combo = $primary . $secondary;

        $profileTitle = "Campuran (Mixed Behavior)";
        $profileDesc = "Kandidat memiliki kepribadian situasional yang fleksibel dan adaptif dalam berbagai tantangan kerja.";
        $workStyle = "Fleksibel dan dapat bekerja dalam berbagai ritme tergantung tuntutan proyek.";
        $communication = "Mampu menyesuaikan gaya komunikasi berdasarkan konteks lawan bicara.";

        if($primary == 'D' && $secondary == 'I') {
            $profileTitle = "The Pioneer (Perintis) / Achiever";
            $profileDesc = "Sangat kompetitif, berorientasi pada tujuan, cepat bertindak, dan menyukai tantangan baru.";
            $workStyle = "Bergerak cepat, menuntut hasil instan, dan siap memimpin tim.";
            $communication = "Direct / to the point dan tegas.";
        } elseif($primary == 'D' && $secondary == 'C') {
            $profileTitle = "The Architect (Arsitek) / Director";
            $profileDesc = "Kombinasi antara tuntutan hasil yang tinggi dengan akurasi logika dan analisis masif.";
            $workStyle = "Menuntut kesempurnaan, efisiensi, dan standar kualitas tinggi.";
            $communication = "Sangat rasional, menggunakan fakta dan data konkret.";
        } elseif($primary == 'D') {
            $profileTitle = "The Boss (Komandan)";
            $profileDesc = "Sangat asertif dan dominan. Fokus pada penguasaan hasil dan efisiensi waktu.";
            $workStyle = "Tegas, mandiri, dan pengambil keputusan cepat.";
            $communication = "Singkat, instruksional, dan lugas.";
        } elseif($primary == 'I' && $secondary == 'D') {
            $profileTitle = "The Motivator (Motivator)";
            $profileDesc = "Penuh energi, antusias, karismatik, dan pandai meyakinkan orang lain.";
            $workStyle = "Dinamis, menyukai apresiasi, dan inovatif.";
            $communication = "Persuasif, bersemangat, dan ekspresif.";
        } elseif($primary == 'I' && $secondary == 'S') {
            $profileTitle = "The Coach (Konselor) / Peacemaker";
            $profileDesc = "Sangat hangat, ramah, dan peduli pada keharmonisan serta kebersamaan tim.";
            $workStyle = "Kolaboratif, suportif, dan menjaga suasana kondusif.";
            $communication = "Bersahabat, empati, dan mendengarkan aktif.";
        } elseif($primary == 'I') {
            $profileTitle = "The Inspirer (Bintang Sosial)";
            $profileDesc = "Ekstrovert, komunikatif, dan ceria dalam mencairkan suasana kerja.";
            $workStyle = "Interaktif dan menyukai kebebasan berkreasi.";
            $communication = "Verbal dan ramah.";
        } elseif($primary == 'S' && $secondary == 'C') {
            $profileTitle = "The Specialist (Spesialis / Teknisi)";
            $profileDesc = "Sangat stabil, presisi, dan konsisten. Menjaga ritme kerja dengan SOP yang jelas.";
            $workStyle = "Metodis, konsisten, dan minim kesalahan.";
            $communication = "Hati-hati dan terstruktur.";
        } elseif($primary == 'S') {
            $profileTitle = "The Steady (Sang Penstabil)";
            $profileDesc = "Tenang, sabar, loyal, dan tidak mudah terburu-buru dalam bertindak.";
            $workStyle = "Terjadwal dan menciptakan suasana damai.";
            $communication = "Kalem dan tidak agresif.";
        } elseif($primary == 'C' && $secondary == 'S') {
            $profileTitle = "The Perfectionist (Si Sempurna)";
            $profileDesc = "Berhati-hati, analitis, dan memegang teguh standar akurasi tinggi.";
            $workStyle = "Terorganisir rapi dan teliti pada detail kecil.";
            $communication = "Formal, presisi, dan berbasis data.";
        } elseif($primary == 'C') {
            $profileTitle = "The Analyst (Analis Murni)";
            $profileDesc = "Rasional, patuh pada aturan, dan fokus pada fakta objektif.";
            $workStyle = "Membutuhkan data lengkap dan fokus mendalam.";
            $communication = "Kaku, tepat, dan ilmiah.";
        }
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
                        {{ $siteSettings->pdf_header_subtitle ?? 'Executive Summary - DISC Behavioral Assessment' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMASI KANDIDAT --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td><td class="value">: {{ $application->user->name }}</td>
            <td class="label">ID / Tgl Tes</td><td class="value">: DISC-{{ $discResult->id }} / {{ $discResult->completed_at ? $discResult->completed_at->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Posisi Tujuan</td><td class="value">: {{ $application->job->title }}</td>
            <td class="label">Kode Profil Utama</td><td class="value">: {{ $primary }}{{ $secondary }}</td>
        </tr>
    </table>

    {{-- BANNER PROFIL --}}
    <div class="section-title">A. Kesimpulan Profil Perilaku (AI System Analysis)</div>
    <div class="disc-banner">
        <div class="disc-badge">KODE PROFIL: {{ $primary }}{{ $secondary }}</div>
        <div class="disc-title">{{ $profileTitle }}</div>
        <div class="disc-desc">"{{ $profileDesc }}"</div>
        <table style="width: 100%; margin-top: 10px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 8px;">
            <tr>
                <td style="width: 50%; font-size: 9px; color: #e2e8f0; border: none;"><b>Gaya Kerja:</b> {{ $workStyle }}</td>
                <td style="width: 50%; font-size: 9px; color: #e2e8f0; border: none;"><b>Gaya Komunikasi:</b> {{ $communication }}</td>
            </tr>
        </table>
    </div>

    {{-- DETAIL 4 KARAKTER DISC --}}
    <div class="section-title">B. Intensitas 4 Dimensi Karakter DISC</div>

    <table class="grid-table">
        <tr>
            <td class="grid-cell">
                <div class="factor-card factor-d">
                    <div class="factor-title">D - Dominance <span class="factor-score" style="color: #dc2626;">{{ $d_score }} Poin</span></div>
                    <div class="progress-bg"><div class="progress-bar bg-d" style="width: {{ min(100, ($d_score/40)*100) }}%;"></div></div>
                    <div class="factor-text">Dorongan untuk mengontrol, memimpin, bertindak tegas, dan berorientasi pada pencapaian hasil.</div>
                </div>
            </td>
            <td class="grid-cell">
                <div class="factor-card factor-i">
                    <div class="factor-title">I - Influence <span class="factor-score" style="color: #d97706;">{{ $i_score }} Poin</span></div>
                    <div class="progress-bg"><div class="progress-bar bg-i" style="width: {{ min(100, ($i_score/40)*100) }}%;"></div></div>
                    <div class="factor-text">Kecenderungan bersosialisasi, membujuk, optimis, dan membangun jaringan komunikasi.</div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="grid-cell">
                <div class="factor-card factor-s">
                    <div class="factor-title">S - Steadiness <span class="factor-score" style="color: #16a34a;">{{ $s_score }} Poin</span></div>
                    <div class="progress-bg"><div class="progress-bar bg-s" style="width: {{ min(100, ($s_score/40)*100) }}%;"></div></div>
                    <div class="factor-text">Kebutuhan akan konsistensi, kesabaran, loyalitas, dan dukungan stabilitas lingkungan kerja.</div>
                </div>
            </td>
            <td class="grid-cell">
                <div class="factor-card factor-c">
                    <div class="factor-title">C - Compliance <span class="factor-score" style="color: #2563eb;">{{ $c_score }} Poin</span></div>
                    <div class="progress-bg"><div class="progress-bar bg-c" style="width: {{ min(100, ($c_score/40)*100) }}%;"></div></div>
                    <div class="factor-text">Kepatuhan terhadap standar, akurasi data, kecermatan, dan logika analisis yang teratur.</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Portal Karir {{ $siteSettings->company_name ?? 'HerbaTech' }} dan bersifat rahasia.
    </div>

</body>
</html>
