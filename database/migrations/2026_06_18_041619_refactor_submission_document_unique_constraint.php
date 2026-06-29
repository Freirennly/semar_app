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
        Schema::table('submission_documents', function (Blueprint $table) {
            $table->unique(
                ['submission_id', 'document_template_id'],
                'submission_template_unique'
            );

            $table->dropUnique(
                'submission_documents_submission_id_doc_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('submission_documents', function (Blueprint $table) {
            $table->unique(
                ['submission_id', 'doc_type'],
                'submission_documents_submission_id_doc_type_unique'
            );

            $table->dropUnique('submission_template_unique');
        });
    }
};
