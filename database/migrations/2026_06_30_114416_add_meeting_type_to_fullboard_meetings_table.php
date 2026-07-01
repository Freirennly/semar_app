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
        Schema::table('fullboard_meetings', function (Blueprint $table) {
            $table->enum('meeting_type', ['OFFLINE', 'ONLINE', 'HYBRID'])
                ->default('OFFLINE')
                ->after('submission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fullboard_meetings', function (Blueprint $table) {
            $table->dropColumn('meeting_type');
        });
    }
};