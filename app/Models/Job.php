<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'category_id',
        'location_id',
        'title',
        'slug',
        'department',      // Penambahan baru
        'work_setting',    // Penambahan baru (on_site, hybrid, remote)
        'description',
        'requirements',
        'responsibilities',
        'salary_min',
        'salary_max',
        'salary_type',
        'salary_currency',
        'is_salary_visible', // Penambahan baru
        'job_type',
        'experience_level',
        'education_level',
        'deadline',
        'vacancy',
        'status',
        'is_featured',
        'is_remote',
        'views',
        'required_tests',
    ];

    protected $casts = [
        'salary_min'        => 'decimal:2',
        'salary_max'        => 'decimal:2',
        'deadline'          => 'date',
        'is_featured'       => 'boolean',
        'is_remote'         => 'boolean',
        'is_salary_visible' => 'boolean', // Casting untuk keamanan data boolean
        'vacancy'           => 'integer',
        'views'             => 'integer',
        'required_tests' => 'array',
    ];

    // --- Relationships ---

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(JobLocation::class, 'location_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    // --- Scope Methods (Query Helpers) ---

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->published()
            ->where(function ($q) {
                $q->whereNull('deadline')
                    ->orWhere('deadline', '>=', now());
            });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->published();
    }

    // --- Accessors & Helpers ---

    /**
     * Format tampilan gaji yang lebih dinamis dan mendukung privasi (is_salary_visible).
     */
    public function getSalaryFormattedAttribute(): string
    {
        // Jika perusahaan memilih untuk menyembunyikan gaji
        if (!$this->is_salary_visible) {
            return 'Gaji Kompetitif';
        }

        if (!$this->salary_min && !$this->salary_max) {
            return 'Gaji Kompetitif / Negosiasi';
        }

        $currency = $this->salary_currency ?? 'IDR';
        $formatter = new \NumberFormatter('id_ID', \NumberFormatter::CURRENCY);

        $typeMap = [
            'monthly' => 'per bulan',
            'hourly'  => 'per jam',
            'yearly'  => 'per tahun',
            'project' => 'per proyek',
        ];

        $displayType = $typeMap[strtolower($this->salary_type)] ?? $this->salary_type;

        if ($this->salary_min && $this->salary_max) {
            return $formatter->formatCurrency($this->salary_min, $currency) . ' - ' .
                $formatter->formatCurrency($this->salary_max, $currency) . ' / ' . $displayType;
        }

        if ($this->salary_min) {
            return 'Mulai ' . $formatter->formatCurrency($this->salary_min, $currency) . ' / ' . $displayType;
        }

        return 'Hingga ' . $formatter->formatCurrency($this->salary_max, $currency) . ' / ' . $displayType;
    }

    /**
     * Helper untuk mendapatkan label badge status tempat kerja.
     */
    public function getWorkSettingLabelAttribute(): string
    {
        return [
            'on_site' => 'On-site (Di Kantor)',
            'hybrid'  => 'Hybrid',
            'remote'  => 'Remote (Jarak Jauh)',
        ][$this->work_setting] ?? 'On-site';
    }

    /**
     * Label pengalaman yang mudah dibaca pengguna (Indonesian formatted).
     */
    public function getFormattedExperienceLevelAttribute(): string
    {
        $value = strtolower((string)$this->experience_level);

        $map = [
            'entry_level'        => 'Fresh Graduate / Entry Level',
            'entry'              => 'Fresh Graduate / Entry Level',
            'fresh_graduate'     => 'Fresh Graduate',
            '1_3_years'          => '1 - 3 Tahun',
            '1_3'                => '1 - 3 Tahun',
            '3_5_years'          => '3 - 5 Tahun',
            '3_5'                => '3 - 5 Tahun',
            'more_than_5_years'  => 'Lebih dari 5 Tahun',
            '5_plus_years'       => 'Diatas 5 Tahun',
            'junior'             => 'Junior (1 - 2 Tahun)',
            'mid'                => 'Mid Level (2 - 4 Tahun)',
            'senior'             => 'Senior (Diatas 5 Tahun)',
            'lead'               => 'Lead Level',
            'manager'            => 'Managerial',
        ];

        if (isset($map[$value])) {
            return $map[$value];
        }

        if (empty($value)) {
            return 'Semua Tingkat';
        }

        return ucwords(str_replace(['_', '-'], ' ', $value));
    }

    /**
     * Total pelamar akurat (dengan fallback ke database count jika withCount tidak dipakai).
     */
    public function getApplicationsCountAttribute($value): int
    {
        if ($value !== null) {
            return (int) $value;
        }
        
        if ($this->relationLoaded('applications')) {
            return $this->applications->count();
        }

        return $this->applications()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'published' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        if (!$this->deadline) {
            return false;
        }
        return Carbon::today()->gt(Carbon::parse($this->deadline)->startOfDay());
    }

    /**
     * Label sisa waktu / status kedaluwarsa lowongan.
     */
    public function getRemainingTimeLabelAttribute(): string
    {
        if (!$this->deadline) {
            return 'Tanpa Batas';
        }

        $today = Carbon::today();
        $deadlineDate = Carbon::parse($this->deadline)->startOfDay();

        if ($today->gt($deadlineDate)) {
            return 'Kedaluwarsa';
        }

        if ($today->eq($deadlineDate)) {
            return 'Hari Ini (Terakhir)';
        }

        $daysLeft = (int) $today->diffInDays($deadlineDate);
        return $daysLeft . ' Hari';
    }

    /**
     * HTML formatted badge sisa waktu / kedaluwarsa lowongan.
     */
    public function getRemainingTimeHtmlAttribute(): string
    {
        if (!$this->deadline) {
            return '<span class="text-muted fw-bold">Tanpa Batas</span>';
        }

        $today = Carbon::today();
        $deadlineDate = Carbon::parse($this->deadline)->startOfDay();

        if ($today->gt($deadlineDate)) {
            return '<span class="text-danger fw-bold">Kedaluwarsa</span>';
        }

        if ($today->eq($deadlineDate)) {
            return '<span class="text-warning fw-bold">Hari Ini (Terakhir)</span>';
        }

        $daysLeft = (int) $today->diffInDays($deadlineDate);
        return '<span class="text-success fw-bold">' . $daysLeft . ' Hari</span>';
    }

    /**
     * Otomatis ubah lowongan aktif menjadi closed jika deadline sudah lewat.
     */
    public static function closeExpiredJobs(): int
    {
        return static::query()
            ->where('status', 'published')
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->update(['status' => 'closed']);
    }
}
