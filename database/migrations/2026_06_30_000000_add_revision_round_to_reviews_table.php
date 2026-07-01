<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop foreign keys first to allow dropping the index/unique constraint
            $table->dropForeign(['submission_id']);
            $table->dropForeign(['reviewer_id']);
            
            // Drop the unique constraint
            $table->dropUnique(['submission_id', 'reviewer_id']);
            
            // Add revision_round column
            $table->unsignedInteger('revision_round')->default(1)->after('reviewer_id');
            
            // Add the new unique constraint
            $table->unique(['submission_id', 'reviewer_id', 'revision_round']);
            
            // Recreate foreign keys
            $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();
            $table->foreign('reviewer_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['submission_id']);
            $table->dropForeign(['reviewer_id']);
            $table->dropUnique(['submission_id', 'reviewer_id', 'revision_round']);
            $table->dropColumn('revision_round');
            $table->unique(['submission_id', 'reviewer_id']);
            $table->foreign('submission_id')->references('id')->on('submissions')->cascadeOnDelete();
            $table->foreign('reviewer_id')->references('id')->on('users');
        });
    }
};
