<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add is_archived to document_templates
        Schema::table('document_templates', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('is_shown')->index();
            $table->index(['is_shown', 'is_archived'], 'dt_shown_archived_idx');
        });

        // 2. Make activity_logs.submission_id nullable
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('submission_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropIndex('dt_shown_archived_idx');
            $table->dropIndex(['is_archived']);
            $table->dropColumn('is_archived');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('submission_id')->nullable(false)->change();
        });
    }
};
