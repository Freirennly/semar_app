<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->uuid('verification_token')->nullable()->unique();
        });

        // Backfill existing DONE submissions
        $doneSubmissions = DB::table('submissions')->where('status', 'DONE')->get();
        foreach ($doneSubmissions as $sub) {
            if (empty($sub->verification_token)) {
                DB::table('submissions')->where('id', $sub->id)->update([
                    'verification_token' => (string) Str::uuid(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });
    }
};
