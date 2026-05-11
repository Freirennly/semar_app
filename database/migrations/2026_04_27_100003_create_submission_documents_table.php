<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('doc_type');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime')->default('application/pdf');
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();

            $table->unique(['submission_id', 'doc_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_documents');
    }
};
