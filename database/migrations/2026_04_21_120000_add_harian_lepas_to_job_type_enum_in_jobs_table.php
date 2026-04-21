<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE jobs MODIFY job_type ENUM('full_time', 'part_time', 'contract', 'freelance', 'internship', 'harian_lepas') NOT NULL DEFAULT 'full_time'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE jobs MODIFY job_type ENUM('full_time', 'part_time', 'contract', 'freelance', 'internship') NOT NULL DEFAULT 'full_time'");
    }
};
