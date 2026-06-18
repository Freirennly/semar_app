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
            $table->index('ec_number');
            $table->index('signatory_id');
            $table->index('signed_at');

            $table->index(['status', 'signatory_id']);
            $table->index(['status', 'ec_certificate_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['ec_number']);
            $table->dropIndex(['signatory_id']);
            $table->dropIndex(['signed_at']);

            $table->dropIndex(['status', 'signatory_id']);
            $table->dropIndex(['status', 'ec_certificate_path']);
        });
    }
};
