<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama dokumen template (misal: "Format Protokol Etik")
            $table->string('file_path'); // Path penyimpanan file fisik
            $table->string('description')->nullable(); // Keterangan tambahan
            $table->boolean('is_required')->default(false); // true = Wajib, false = Opsional
            $table->boolean('is_shown')->default(true); // true = Tampil, false = Sembunyikan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};