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
            $table->string('ec_number')->nullable()->after('secretary_id');
            $table->foreignId('signatory_id')
                  ->nullable()
                  ->after('ec_number')
                  ->constrained('users')
                  ->nullOnDelete();
            $table->text('confirmed_title')->nullable()->after('signatory_id');
            $table->string('confirmed_researcher_name')->nullable()->after('confirmed_title');
            $table->timestamp('signed_at')->nullable()->after('confirmed_researcher_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropForeign(['signatory_id']);
            $table->dropColumn([
                'ec_number',
                'signatory_id',
                'confirmed_title',
                'confirmed_researcher_name',
                'signed_at',
            ]);
        });
    }
};
