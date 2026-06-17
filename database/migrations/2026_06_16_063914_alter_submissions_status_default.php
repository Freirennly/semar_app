<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('status')->default('NEW_PROPOSAL')->change();
        });

        $mapping = [
            'DRAFT' => 'NEW_PROPOSAL',
            'SUBMITTED' => 'NEW_PROPOSAL',
            'ASSIGNED_SECRETARIAT' => 'PROCESS',
            'READY_FOR_REVIEW' => 'PROCESS',
            'UNDER_REVIEW' => 'ON_REVIEW',
            'PENDING_DECISION' => 'ON_REVIEW',
            'APPROVED' => 'APPROVED',
            'RESUBMISSION' => 'RESUBMISSION',
            'DISAPPROVED' => 'REJECTED',
            'WAITING_TTD' => 'WAITING_SIGNATURE',
            'PUBLISHED' => 'DONE',
            'ARCHIVED' => 'DONE',
        ];

        foreach ($mapping as $old => $new) {
            DB::table('submissions')->where('status', $old)->update(['status' => $new]);
            DB::table('status_histories')->where('from_status', $old)->update(['from_status' => $new]);
            DB::table('status_histories')->where('to_status', $old)->update(['to_status' => $new]);
            DB::table('activity_logs')->where('old_status', $old)->update(['old_status' => $new]);
            DB::table('activity_logs')->where('new_status', $old)->update(['new_status' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->string('status')->default('DRAFT')->change();
        });

        $reverseMapping = [
            'NEW_PROPOSAL' => 'DRAFT',
            'PROCESS' => 'READY_FOR_REVIEW',
            'ON_REVIEW' => 'UNDER_REVIEW',
            'REJECTED' => 'DISAPPROVED',
            'WAITING_SIGNATURE' => 'WAITING_TTD',
            'DONE' => 'PUBLISHED',
        ];

        foreach ($reverseMapping as $new => $old) {
            DB::table('submissions')->where('status', $new)->update(['status' => $old]);
            DB::table('status_histories')->where('from_status', $new)->update(['from_status' => $old]);
            DB::table('status_histories')->where('to_status', $new)->update(['to_status' => $old]);
            DB::table('activity_logs')->where('old_status', $new)->update(['old_status' => $old]);
            DB::table('activity_logs')->where('new_status', $new)->update(['new_status' => $old]);
        }
    }
};
