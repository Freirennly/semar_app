<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_documents', function (Blueprint $table) {
            // Menambahkan foreign key yang merujuk ke tabel document_templates
            $table->foreignId('document_template_id')
                  ->nullable()
                  ->after('submission_id')
                  ->constrained('document_templates')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('submission_documents', function (Blueprint $table) {
            $table->dropForeign(['document_template_id']);
            $table->dropColumn('document_template_id');
        });
    }
};