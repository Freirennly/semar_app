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
        Schema::table('submissions', function (Blueprint $table) {
            // Menambahkan kolom secretary_id yang relasi ke tabel users (nullable karena opsional di awal draf)
            $table->foreignId('secretary_id')
                  ->nullable()
                  ->after('student_id')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Drop foreign key dan kolomnya jika rollback
            $table->dropForeign(['secretary_id']);
            $table->dropColumn('secretary_id');
        });
    }
};