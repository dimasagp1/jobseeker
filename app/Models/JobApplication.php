<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    // --- KONSTANTA STATUS ---
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_TEST_INVITED = 'test_invited';
    const STATUS_TEST_IN_PROGRESS = 'test_in_progress';
    const STATUS_TEST_COMPLETED = 'test_completed';
    const STATUS_INTERVIEW = 'interview';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'job_id',
        'user_id',
        'cv_path',
        'birth_date',
        'cover_letter',
        'cover_letter_path',
        'status',
        'notes',
        'answers',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'answers' => 'array',
    ];

    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_PENDING          => 'Menunggu Review',
            self::STATUS_REVIEWED         => 'Sedang Ditinjau',
            self::STATUS_SHORTLISTED      => 'Lolos Seleksi Berkas',
            self::STATUS_TEST_INVITED     => 'Undangan Tes',
            self::STATUS_TEST_IN_PROGRESS => 'Sedang Mengerjakan Tes',
            self::STATUS_TEST_COMPLETED   => 'Tes Selesai',
            self::STATUS_INTERVIEW        => 'Wawancara',
            self::STATUS_ACCEPTED         => 'Diterima',
            self::STATUS_REJECTED         => 'Ditolak',
        ];
    }

    /**
     * Mengecek apakah pelamar sudah menyelesaikan SEMUA tes yang diwajibkan oleh lowongan
     */
    public function allTestsCompleted(): bool
    {
        $requiredTests = $this->job?->required_tests ?? ['kraepelin', 'disc', 'msdt', 'papi'];

        if (empty($requiredTests) || !is_array($requiredTests)) {
            return false;
        }

        // Cek Kraepelin jika diwajibkan
        if (in_array('kraepelin', $requiredTests)) {
            $kraepelinDone = $this->relationLoaded('kraepelinTest')
                ? ($this->kraepelinTest && $this->kraepelinTest->completed_at !== null)
                : $this->kraepelinTest()->whereNotNull('completed_at')->exists();

            if (!$kraepelinDone) {
                return false;
            }
        }

        // Cek tes psikologi lainnya (DISC, MSDT, PAPI)
        $completedTypes = $this->relationLoaded('psychologicalResults')
            ? $this->psychologicalResults->where('status', 'completed')->pluck('test_type')->map(fn($t) => strtolower(trim($t)))->toArray()
            : $this->psychologicalResults()->where('status', 'completed')->pluck('test_type')->map(fn($t) => strtolower(trim($t)))->toArray();

        foreach ($requiredTests as $test) {
            $test = strtolower(trim($test));
            if ($test === 'kraepelin') continue;

            if (!in_array($test, $completedTypes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Otomatis sinkronkan dan update status lamaran berdasarkan progres pengerjaan tes
     */
    public function checkAndUpdateTestStatus(): bool
    {
        $this->refresh();

        if ($this->allTestsCompleted()) {
            $this->update(['status' => self::STATUS_TEST_COMPLETED]);
            return true;
        } else {
            // Jika belum semua selesai, tapi sudah mulai mengerjakan salah satu tes
            $hasAnyProgress = $this->kraepelinTest()->exists() || $this->psychologicalResults()->exists();
            if ($hasAnyProgress && !in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_REJECTED, self::STATUS_INTERVIEW])) {
                $this->update(['status' => self::STATUS_TEST_IN_PROGRESS]);
            }
            return false;
        }
    }

    /**
     * Mendapatkan rincian progres pengerjaan tes untuk tampilan UI
     */
    public function getTestProgress(): array
    {
        $requiredTests = $this->job?->required_tests ?? ['kraepelin', 'disc', 'msdt', 'papi'];
        if (!is_array($requiredTests)) {
            $requiredTests = ['kraepelin', 'disc', 'msdt', 'papi'];
        }

        $totalRequired = count($requiredTests);
        if ($totalRequired === 0) {
            return [
                'completed' => 0,
                'total' => 0,
                'is_done' => true,
                'percent' => 100,
                'has_tests' => false,
            ];
        }

        $completedCount = 0;

        // Cek Kraepelin
        if (in_array('kraepelin', $requiredTests)) {
            $kraepelinDone = $this->relationLoaded('kraepelinTest')
                ? ($this->kraepelinTest && $this->kraepelinTest->completed_at !== null)
                : $this->kraepelinTest()->whereNotNull('completed_at')->exists();
            if ($kraepelinDone) {
                $completedCount++;
            }
        }

        // Cek Tes Psikologi (DISC, MSDT, PAPI)
        $completedPsyTypes = $this->relationLoaded('psychologicalResults')
            ? $this->psychologicalResults->where('status', 'completed')->pluck('test_type')->map(fn($t) => strtolower(trim($t)))->toArray()
            : $this->psychologicalResults()->where('status', 'completed')->pluck('test_type')->map(fn($t) => strtolower(trim($t)))->toArray();

        foreach ($requiredTests as $test) {
            $test = strtolower(trim($test));
            if ($test === 'kraepelin') continue;

            if (in_array($test, $completedPsyTypes)) {
                $completedCount++;
            }
        }

        $isDone = ($completedCount >= $totalRequired);
        $percent = $totalRequired > 0 ? round(($completedCount / $totalRequired) * 100) : 100;

        return [
            'completed' => $completedCount,
            'total' => $totalRequired,
            'is_done' => $isDone,
            'percent' => $percent,
            'has_tests' => true,
        ];
    }
    
    // --- RELATIONSHIPS ---

    public function kraepelinTest()
    {
        // Menggunakan latestOfMany() sudah sangat tepat untuk retake test
        return $this->hasOne(KraepelinTest::class)->latestOfMany();
    }

    public function psychologicalResults()
    {
        return $this->hasMany(PsychologicalTestResult::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- ACCESSORS (UI Logic) ---

    // Menambahkan Label Status agar bisa dipanggil di Blade via $application->status_label
    public function getStatusLabelAttribute(): string
    {
        return self::getAllStatuses()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 'warning',
            self::STATUS_REVIEWED         => 'secondary',
            self::STATUS_SHORTLISTED      => 'info',
            self::STATUS_TEST_INVITED     => 'primary',
            self::STATUS_TEST_IN_PROGRESS => 'warning',
            self::STATUS_TEST_COMPLETED   => 'success',
            self::STATUS_INTERVIEW        => 'dark',
            self::STATUS_ACCEPTED         => 'success',
            self::STATUS_REJECTED         => 'danger',
            default                       => 'light',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING          => 'fa-clock',
            self::STATUS_REVIEWED         => 'fa-eye',
            self::STATUS_SHORTLISTED      => 'fa-user-check',
            self::STATUS_TEST_INVITED     => 'fa-file-signature',
            self::STATUS_TEST_IN_PROGRESS => 'fa-spinner fa-spin',
            self::STATUS_TEST_COMPLETED   => 'fa-poll-h',
            self::STATUS_INTERVIEW        => 'fa-comments',
            self::STATUS_ACCEPTED         => 'fa-check-double',
            self::STATUS_REJECTED         => 'fa-times-circle',
            default                       => 'fa-info-circle',
        };
    }
}