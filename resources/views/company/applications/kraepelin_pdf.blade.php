<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Psikometri Kraepelin - {{ $application->user->name }}</title>
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
        
        /* Watermark Confidential */
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

        /* Header */
        .header { border-bottom: 3px solid #4338ca; padding-bottom: 8px; margin-bottom: 15px; }
        .company-name { font-size: 22px; font-weight: 800; color: #4338ca; margin: 0; letter-spacing: -1px; }
        .report-type { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-top: 3px; }
        .confidential { float: right; font-size: 9px; background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: bold; margin-top: -30px; }

        /* General Tables */
        table { width: 100%; border-collapse: collapse; }
        .info-table { margin-bottom: 15px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .info-table td { padding: 6px 12px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        .label { color: #64748b; font-weight: bold; width: 20%; }
        .value { color: #1e293b; font-weight: bold; width: 30%; }

        /* Section Titles */
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
        .sub-title { font-size: 11px; font-weight: bold; color: #4338ca; margin-bottom: 6px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px;}

        /* Text Colors */
        .text-success { color: #16a34a; }
        .text-danger { color: #dc2626; }
        .text-warning { color: #d97706; }
        .text-info { color: #0284c7; }

        /* Factor Cards (Grid) */
        .factor-cell { padding: 5px; width: 50%; vertical-align: top; }
        .factor-card { border: 1px solid #cbd5e1; padding: 10px; background: #f8fafc; border-radius: 4px; }
        .factor-card.panker { border-left: 4px solid #4338ca; }
        .factor-card.tianker { border-left: 4px solid #dc2626; }
        .factor-card.janker { border-left: 4px solid #f59e0b; }
        .factor-card.ganker { border-left: 4px solid #16a34a; }
        .factor-title { font-size: 10px; font-weight: bold; color: #334155; }
        .factor-score { float: right; font-weight: 900; font-size: 11px; }
        .factor-desc { font-size: 9px; margin-top: 5px; color: #475569; line-height: 1.4; }

        /* Progress Bars */
        .progress-wrapper { margin-bottom: 8px; }
        .progress-label { font-size: 9px; font-weight: bold; margin-bottom: 2px; display: block; }
        .progress-label span { float: right; }
        .progress-bg { background-color: #e2e8f0; border-radius: 4px; width: 100%; height: 7px; }
        .progress-bar { height: 7px; border-radius: 4px; }
        .bg-success { background-color: #22c55e; }
        .bg-primary { background-color: #4338ca; }
        .bg-warning { background-color: #f59e0b; }

        /* Bar Chart (CSS Only for PDF) */
        .chart-table { 
            width: 60%; /* Kurangi dari 85/80% menjadi 60% agar lebih ramping */
            margin: 10px auto; 
            border-bottom: 1px solid #94a3b8; 
        }
        .chart-table td { vertical-align: bottom; text-align: center; width: 20%; padding: 0; border: none; height: 60px; }
        .bar { background-color: #4338ca; width: 20px; margin: 0 auto; border-radius: 3px 3px 0 0; }
        .bar-label { font-size: 9px; font-weight: bold; margin-top: 5px; color: #64748b;}

        /* Summary Box */
        .summary-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 12px; margin-bottom: 15px; border-radius: 6px; }
        .summary-text { font-size: 10px; color: #1e3a8a; text-align: justify; line-height: 1.5; }

        /* Final Verdict Banner */
        .verdict-banner { padding: 12px; text-align: center; margin-top: 15px; border: 2px solid; border-radius: 6px; }
        .fit { background: #f0fdf4; border-color: #22c55e; color: #166534; }
        .marginal { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
        .unfit { background: #fef2f2; border-color: #ef4444; color: #991b1b; }
        .verdict-title { font-size: 9px; text-transform: uppercase; font-weight: bold; margin-bottom: 3px; }
        .verdict-value { font-size: 14px; font-weight: 900; }

        .footer { position: fixed; bottom: -0.5cm; left: 0; width: 100%; text-align: center; font-size: 8px; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 10px; }
    </style>
</head>
<body>

    @php
        // 1. Persiapan Data
        $chartData = is_string($test->results_chart) ? json_decode($test->results_chart, true) : $test->results_chart;
        if (!is_array($chartData)) $chartData = [];

        // 2. Data Distribusi
        $correct = $test->total_correct;
        $error = $test->total_answered - $test->total_correct;
        $skipped = max(0, $test->tianker - $error);

        // 3. Kalkulasi Persentase
        $accuracy = $test->total_answered > 0 ? round(($test->total_correct / $test->total_answered) * 100, 1) : 0;
        $pankerPerc = min(($test->panker / 25) * 100, 100);
        $jankerPerc = max(100 - ($test->janker * 6), 0);

        // 4. Kalkulasi Kuartal (Bar Chart Data)
        $quarters = [];
        for ($i = 0; $i < 50; $i += 10) {
            $slice = array_slice($chartData, $i, 10);
            $quarters[] = count($slice) > 0 ? round(array_sum($slice) / count($slice), 1) : 0;
        }
        $maxQuarter = count($quarters) > 0 ? max($quarters) : 1;
        if ($maxQuarter == 0) $maxQuarter = 1; // Mencegah division by zero error
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
                        @if(!isset($siteSettings->pdf_header_title))
                            <span style="font-size: 10px; font-weight: normal; color: #94a3b8;">| Human Resources Dept.</span>
                        @endif
                    </p>
                    <p class="report-type" style="margin: 3px 0 0 0;">
                        {{ $siteSettings->pdf_header_subtitle ?? 'Executive Summary - Kraepelin Assessment' }}
                    </p>
                </td>
            </tr>
        </table>
    </div>

    {{-- INFORMASI KANDIDAT --}}
    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td><td class="value">: {{ $application->user->name }}</td>
            <td class="label">ID / Tgl Tes</td><td class="value">: KR-{{ $test->id }} / {{ $test->completed_at->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="label">Posisi Tujuan</td><td class="value">: {{ $application->job->title }}</td>
            <td class="label">Durasi Tes</td><td class="value">: 1.500 Detik (50 Kolom)</td>
        </tr>
    </table>

    {{-- BAGIAN A: RINGKASAN --}}
    <div class="section-title">A. Ringkasan Evaluasi Sistem</div>
    <div class="summary-box">
        <div class="summary-text">
            Berdasarkan hasil pengerjaan, kandidat memiliki tingkat kecepatan kerja <b>{{ $test->panker >= 15 ? 'tinggi' : ($test->panker >= 10 ? 'sedang / rata-rata' : 'rendah') }}</b> 
            dengan tingkat ketelitian yang <b>{{ $test->tianker <= 5 ? 'sangat baik (jarang membuat kesalahan)' : ($test->tianker <= 15 ? 'cukup baik' : 'kurang (terburu-buru / ceroboh)') }}</b>. 
            Stabilitas emosi saat berada di bawah tekanan tergolong <b>{{ $test->janker <= 5 ? 'sangat stabil' : ($test->janker <= 12 ? 'cukup stabil' : 'mudah terpengaruh (fluktuatif)') }}</b>. 
            Secara keseluruhan, ketahanan kerja (stamina) kandidat menunjukkan tren yang <b>{{ $test->ganker >= 0 ? 'positif (mampu mempertahankan fokus dan ritme)' : 'negatif (rentan mengalami kelelahan pada tugas repetitif)' }}</b>.
        </div>
    </div>

    {{-- BAGIAN B: ANALISIS 4 FAKTOR (P-T-J-G) --}}
    <div class="section-title">B. Analisis Detail 4 Faktor Utama (P-T-J-G)</div>
    <table style="width: 100%; margin-bottom: 10px; table-layout: fixed;">
        <tr>
            {{-- PANKER --}}
            <td class="factor-cell">
                <div class="factor-card panker">
                    <div class="factor-title">PK (Kecepatan) <span class="factor-score" style="color:#4338ca;">{{ round($test->panker, 1) }} Baris</span></div>
                    <div class="factor-desc">
                        @if($test->panker >= 15) <span class="text-success fw-bold">Sangat Cepat.</span> Kapasitas produksi kerja sangat tinggi.
                        @elseif($test->panker >= 10) <span class="text-info fw-bold">Rata-rata.</span> Kecepatan kerja standar.
                        @else <span class="text-danger fw-bold">Lambat.</span> Butuh waktu ekstra untuk menyelesaikan tugas.
                        @endif
                    </div>
                </div>
            </td>
            {{-- TIANKER --}}
            <td class="factor-cell">
                <div class="factor-card tianker">
                    <div class="factor-title">TK (Ketelitian) <span class="factor-score" style="color:#dc2626;">{{ $test->tianker }} Error</span></div>
                    <div class="factor-desc">
                        @if($test->tianker <= 5) <span class="text-success fw-bold">Sangat Teliti.</span> Sangat fokus dan akurat.
                        @elseif($test->tianker <= 20) <span class="text-warning fw-bold">Cukup.</span> Tingkat kesalahan masih batas wajar.
                        @else <span class="text-danger fw-bold">Kurang Teliti.</span> Cenderung terburu-buru dan ceroboh.
                        @endif
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            {{-- JANKER --}}
            <td class="factor-cell">
                <div class="factor-card janker">
                    <div class="factor-title">JK (Stabilitas) <span class="factor-score" style="color:#f59e0b;">{{ $test->janker }} Poin</span></div>
                    <div class="factor-desc">
                        @if($test->janker <= 5) <span class="text-success fw-bold">Sangat Stabil.</span> Kuat menahan tekanan kerja.
                        @elseif($test->janker <= 12) <span class="text-info fw-bold">Konsisten.</span> Emosi kerja cukup stabil.
                        @else <span class="text-danger fw-bold">Fluktuatif.</span> Mudah tertekan dan moody dalam bekerja.
                        @endif
                    </div>
                </div>
            </td>
            {{-- GANKER --}}
            <td class="factor-cell">
                <div class="factor-card ganker">
                    <div class="factor-title">GK (Ketahanan) <span class="factor-score" style="color:#16a34a;">{{ $test->ganker > 0 ? '+'.$test->ganker : $test->ganker }} ({{ $test->ganker >= 0 ? 'Positif' : 'Negatif' }})</span></div>
                    <div class="factor-desc">
                        @if($test->ganker > 0) <span class="text-success fw-bold">Meningkat.</span> Stamina luar biasa di akhir waktu.
                        @elseif($test->ganker == 0) <span class="text-info fw-bold">Datar.</span> Daya tahan konsisten dari awal ke akhir.
                        @else <span class="text-danger fw-bold">Menurun.</span> Rentan lelah dan hilang fokus di akhir tugas.
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- BAGIAN C: DISTRIBUSI & METRIK KINERJA --}}
    <div class="section-title">C. Distribusi Jawaban & Metrik Kinerja</div>
    <table style="margin-bottom: 15px;">
        <tr>
            <td style="width: 45%; vertical-align: top; padding-right: 15px;">
                <table class="info-table" style="margin-top: 0; background: transparent; border: 1px solid #e2e8f0;">
                    <tr>
                        <td style="color: #64748b; padding: 4px 8px;">Total Input:</td>
                        <td style="text-align: right; font-weight: bold; padding: 4px 8px;">{{ $test->total_answered }}</td>
                    </tr>
                    <tr>
                        <td style="color: #166534; padding: 4px 8px;">Jawaban Benar:</td>
                        <td style="text-align: right; font-weight: bold; color: #166534; padding: 4px 8px;">{{ $correct }}</td>
                    </tr>
                    <tr>
                        <td style="color: #991b1b; padding: 4px 8px;">Salah Hitung:</td>
                        <td style="text-align: right; font-weight: bold; color: #991b1b; padding: 4px 8px;">{{ $error }}</td>
                    </tr>
                    <tr>
                        <td style="color: #b45309; padding: 4px 8px;">Hole (Lompatan):</td>
                        <td style="text-align: right; font-weight: bold; color: #b45309; padding: 4px 8px;">{{ $skipped }}</td>
                    </tr>
                </table>
            </td>
            
            <td style="width: 55%; vertical-align: top; padding-left: 10px;">
                <div>
                    <div class="progress-wrapper">
                        <div class="progress-label">Accuracy Rate (Ketelitian) <span style="color:#22c55e;">{{ $accuracy }}%</span></div>
                        <div class="progress-bg"><div class="progress-bar bg-success" style="width: {{ $accuracy }}%;"></div></div>
                    </div>
                    <div class="progress-wrapper">
                        <div class="progress-label">Speed Capacity (Kapasitas Kerja) <span style="color:#4338ca;">{{ round($pankerPerc) }}%</span></div>
                        <div class="progress-bg"><div class="progress-bar bg-primary" style="width: {{ $pankerPerc }}%;"></div></div>
                    </div>
                    <div class="progress-wrapper" style="margin-bottom: 0;">
                        <div class="progress-label">Stability Index (Konsistensi) <span style="color:#f59e0b;">{{ round($jankerPerc) }}%</span></div>
                        <div class="progress-bg"><div class="progress-bar bg-warning" style="width: {{ $jankerPerc }}%;"></div></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- BAGIAN D: GRAFIK KUARTAL --}}
    {{-- BAGIAN D: GRAFIK KUARTAL --}}
    <div class="section-title">D. Analisis Ritme Per Kuartal (Endurance Chart)</div>
    <div style="margin-bottom: 20px; text-align: center;">
        {{-- Tambahkan align="center" dan pastikan width-nya dikecilkan di sini --}}
        <table class="chart-table" align="center" style="width: 60%; margin: 0 auto;">
            <tr>
                @foreach($quarters as $index => $q)
                    @php 
                        // KUNCI MAKSIMAL TINGGI BAR ADALAH 40px
                        $barHeightPx = ($q / $maxQuarter) * 40; 
                        
                        if($barHeightPx < 2) {
                            $barHeightPx = 2;
                        }
                    @endphp
                    <td>
                        <div style="font-size: 9px; font-weight: bold; color: #4338ca; margin-bottom: 2px;">{{ $q }}</div>
                        <div class="bar" style="height: {{ $barHeightPx }}px;"></div>
                    </td>
                @endforeach
            </tr>
            <tr>
                <td class="bar-label">Kuartal 1<br>(1-10)</td>
                <td class="bar-label">Kuartal 2<br>(11-20)</td>
                <td class="bar-label">Kuartal 3<br>(21-30)</td>
                <td class="bar-label">Kuartal 4<br>(31-40)</td>
                <td class="bar-label">Kuartal 5<br>(41-50)</td>
            </tr>
        </table>
    </div>

    @php
        // Penentuan Kesimpulan Akhir
        if($accuracy > 90 && $test->panker > 12) {
            $verdictClass = 'fit';
            $verdictText = 'SANGAT DISARANKAN (HIGH ACHIEVER)';
            $verdictDesc = 'Memiliki kombinasi sempurna antara kecepatan tinggi dan ketelitian absolut.';
        } elseif($accuracy > 80 && $test->ganker >= 0) {
            $verdictClass = 'marginal';
            $verdictText = 'DAPAT DIPERTIMBANGKAN (STEADY WORKER)';
            $verdictDesc = 'Memiliki etos kerja yang stabil dan konsisten untuk pekerjaan operasional rutin.';
        } else {
            $verdictClass = 'unfit';
            $verdictText = 'PERLU EVALUASI LANJUT (RISK OF FATIGUE)';
            $verdictDesc = 'Menunjukkan gejala impulsif atau cepat lelah di bawah tekanan berdurasi panjang.';
        }
    @endphp

    {{-- KESIMPULAN --}}
    <div class="verdict-banner {{ $verdictClass }}">
        <div class="verdict-title">Rekomendasi Final Penempatan</div>
        <div class="verdict-value">{{ $verdictText }}</div>
        <div style="font-size: 9px; margin-top: 3px;">{{ $verdictDesc }}</div>
    </div>

    <div class="footer">
        <strong>Laporan Rahasia HRD HerbaTech Indonesia</strong><br>
        Dokumen dicetak secara otomatis oleh sistem pada {{ date('d/m/Y H:i:s') }}. Tidak memerlukan tanda tangan basah.
    </div>
</body>
</html>