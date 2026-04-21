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

    public function isActive(): bool
    {
        return $this->status === 'published' &&
            (!$this->deadline || $this->deadline->isFuture() || $this->deadline->isToday());
    }

    public function isExpired(): bool
    {
        return $this->deadline && $this->deadline->isPast() && !$this->deadline->isToday();
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
