<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'register_title')) {
                $table->string('register_title')->nullable()->after('hero_cta_text');
            }
            if (!Schema::hasColumn('companies', 'register_description')) {
                $table->text('register_description')->nullable()->after('register_title');
            }
            if (!Schema::hasColumn('companies', 'guest_banner_title')) {
                $table->string('guest_banner_title')->nullable()->after('register_description');
            }
            if (!Schema::hasColumn('companies', 'guest_banner_description')) {
                $table->text('guest_banner_description')->nullable()->after('guest_banner_title');
            }
            if (!Schema::hasColumn('companies', 'guest_banner_image')) {
                $table->string('guest_banner_image')->nullable()->after('guest_banner_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'register_title',
                'register_description',
                'guest_banner_title',
                'guest_banner_description',
                'guest_banner_image',
            ]);
        });
    }
};
