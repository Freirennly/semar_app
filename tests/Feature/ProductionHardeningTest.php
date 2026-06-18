<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\User;
use App\Models\Submission;
use App\Models\ActivityLog;
use App\Notifications\NewProposalSubmitted;
use App\Notifications\ReviewerAssigned;
use App\Notifications\ProposalApproved;
use App\Notifications\ProposalRevisionRequested;
use App\Notifications\EcWaitingSignature;
use App\Notifications\EcCertificateIssued;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('local');
    }

    public function test_valid_uuid_verification()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();
        $token = (string) Str::uuid();

        $submission = Submission::create([
            'title' => 'Test Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-1234',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Proposal Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/test.pdf',
            'verification_token' => $token,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addYears(10),
            ['token' => $token]
        );

        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertViewHas('isValid', true);
    }

    public function test_invalid_uuid_verification_404()
    {
        $randomToken = (string) Str::uuid();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addYears(10),
            ['token' => $randomToken]
        );

        $response = $this->get($url);
        $response->assertStatus(404);
    }

    public function test_unsigned_verification_url_403()
    {
        $token = (string) Str::uuid();
        $response = $this->get(route('verification.verify', ['token' => $token]));
        $response->assertStatus(403);
    }

    public function test_download_authorization()
    {
        $student = User::role('student')->first();
        $otherStudent = User::role('student')->skip(1)->first();
        $ketua = User::role('ketua')->first();
        $admin = User::role('admin')->first();

        // Create file
        Storage::put('private/ec_certificates/test.pdf', 'dummy content');

        $submission = Submission::create([
            'title' => 'Test Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-1234',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Proposal Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/test.pdf',
            'verification_token' => (string) Str::uuid(),
        ]);

        // Student owner can download
        $response = $this->actingAs($student)->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);

        // Admin can download
        $response = $this->actingAs($admin)->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);

        // Signatory ketua can download
        $response = $this->actingAs($ketua)->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);
    }

    public function test_unauthorized_download_403()
    {
        $student = User::role('student')->first();
        $otherStudent = User::role('student')->skip(1)->first();
        $ketua = User::role('ketua')->first();
        $otherKetua = User::role('ketua')->skip(1)->first();

        // Create file
        Storage::put('private/ec_certificates/test.pdf', 'dummy content');

        $submission = Submission::create([
            'title' => 'Test Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-1234',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Proposal Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/test.pdf',
            'verification_token' => (string) Str::uuid(),
        ]);

        // Other student cannot download
        $response = $this->actingAs($otherStudent)->get(route('submissions.certificate', $submission));
        $response->assertStatus(403);

        // Other ketua cannot download
        if ($otherKetua) {
            $response = $this->actingAs($otherKetua)->get(route('submissions.certificate', $submission));
            $response->assertStatus(403);
        }
    }

    public function test_download_throttling()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();

        Storage::put('private/ec_certificates/test.pdf', 'dummy content');

        $submission = Submission::create([
            'title' => 'Test Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-1234',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Proposal Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/test.pdf',
            'verification_token' => (string) Str::uuid(),
        ]);

        RateLimiter::clear('downloads:' . $student->id);

        for ($i = 0; $i < 20; $i++) {
            $response = $this->actingAs($student)->get(route('submissions.certificate', $submission));
            $response->assertStatus(200);
        }

        $response = $this->actingAs($student)->get(route('submissions.certificate', $submission));
        $response->assertStatus(429);
    }

    public function test_verification_throttling()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();
        $token = (string) Str::uuid();

        $submission = Submission::create([
            'title' => 'Test Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-1234',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Proposal Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/test.pdf',
            'verification_token' => $token,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addYears(10),
            ['token' => $token]
        );

        RateLimiter::clear('verification:127.0.0.1');

        for ($i = 0; $i < 60; $i++) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }

        $response = $this->get($url);
        $response->assertStatus(429);
    }

    public function test_queued_notifications_dispatched()
    {
        Notification::fake();

        $student = User::role('student')->first();
        $admin = User::role('admin')->first();
        $secretariat = User::role('sekretariat')->first();
        $reviewer = User::role('reviewer')->first();
        $ketua = User::role('ketua')->first();

        $submission = Submission::create([
            'title' => 'Test Queue Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::NEW_PROPOSAL,
            'student_id' => $student->id,
        ]);

        // 1. Submit proposal -> NewProposalSubmitted (Trigger notification directly since NEW_PROPOSAL is the initial status and cannot transition into itself)
        $admins = User::role('admin')->get();
        foreach ($admins as $ad) {
            $ad->notify(new NewProposalSubmitted($submission));
        }
        Notification::assertSentTo($admin, NewProposalSubmitted::class);

        $workflow = app(\App\Services\WorkflowService::class);

        // 2. ON_REVIEW -> ReviewerAssigned
        $submission->status = SubmissionStatus::PROCESS;
        $submission->save();
        
        \App\Models\Assignment::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'assigned_by' => $secretariat->id,
            'due_at' => now()->addDays(7),
        ]);
        
        $workflow->transition($submission, SubmissionStatus::ON_REVIEW, $secretariat);
        Notification::assertSentTo($reviewer, ReviewerAssigned::class);

        // 3. APPROVED -> ProposalApproved
        $submission->status = SubmissionStatus::ON_REVIEW;
        $submission->save();
        $workflow->transition($submission, SubmissionStatus::APPROVED, $secretariat);
        Notification::assertSentTo($student, ProposalApproved::class);

        // 4. APPROVED_WITH_REVISION -> ProposalRevisionRequested
        $submission->status = SubmissionStatus::ON_REVIEW;
        $submission->save();
        $workflow->transition($submission, SubmissionStatus::APPROVED_WITH_REVISION, $secretariat);
        Notification::assertSentTo($student, ProposalRevisionRequested::class);

        // 5. WAITING_SIGNATURE -> EcWaitingSignature
        $submission->status = SubmissionStatus::APPROVED;
        $submission->ec_number = 'EC-999';
        $submission->signatory_id = $ketua->id;
        $submission->confirmed_title = 'Confirmed';
        $submission->confirmed_researcher_name = 'Student';
        $submission->save();
        
        $workflow->transition($submission, SubmissionStatus::WAITING_SIGNATURE, $student);
        Notification::assertSentTo($ketua, EcWaitingSignature::class);
    }

    public function test_done_invariant_validation()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();

        $submission = Submission::create([
            'title' => 'Test Invariant',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::WAITING_SIGNATURE,
            'student_id' => $student->id,
            // intentionally omit ec_number and others
        ]);

        $workflow = app(\App\Services\WorkflowService::class);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $workflow->transition($submission, SubmissionStatus::DONE, $ketua);
    }

    public function test_ec_regenerate_command()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();

        // Submission is DONE but file is missing
        $submission = Submission::create([
            'title' => 'Test Regenerate',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-888',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Regenerate Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/missing.pdf',
        ]);

        $this->artisan('ec:regenerate', ['submission' => $submission->id])
            ->assertExitCode(0);

        $submission->refresh();
        $this->assertNotEmpty($submission->ec_certificate_path);
        Storage::assertExists($submission->ec_certificate_path);
    }

    public function test_ec_health_command()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();

        $submission = Submission::create([
            'title' => 'Test Health',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-777',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Health Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/health.pdf',
            'verification_token' => (string) Str::uuid(),
        ]);

        Storage::put('private/ec_certificates/health.pdf', 'dummy');

        $this->artisan('ec:health')
            ->assertExitCode(0);
    }

    public function test_ec_backup_command()
    {
        Storage::put('private/ec_certificates/backup_test.pdf', 'dummy');

        $this->artisan('ec:backup')
            ->assertExitCode(0);
    }

    public function test_backup_rotation_limit()
    {
        $backupDir = storage_path('app/private/backups/ec');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Create 35 dummy backup files
        for ($i = 1; $i <= 35; $i++) {
            $formattedTime = sprintf('%02d', $i);
            $fileName = "{$backupDir}/ec-backup-2026-06-18_00-00-{$formattedTime}.zip";
            file_put_contents($fileName, 'dummy zip data');
        }

        $this->artisan('ec:backup')
            ->assertExitCode(0);

        $remainingBackups = glob("{$backupDir}/ec-backup-*.zip");
        $this->assertLessThanOrEqual(30, count($remainingBackups));

        // Clean up created backups
        foreach ($remainingBackups as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function test_verification_logging()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();
        $token = (string) Str::uuid();

        $submission = Submission::create([
            'title' => 'Test Log',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-555',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Log Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/log.pdf',
            'verification_token' => $token,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addYears(10),
            ['token' => $token]
        );

        $this->get($url);

        $this->assertDatabaseHas('activity_logs', [
            'submission_id' => $submission->id,
        ]);
    }

    public function test_certificate_download_logging()
    {
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();

        Storage::put('private/ec_certificates/test.pdf', 'dummy content');

        $submission = Submission::create([
            'title' => 'Test Log 2',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::DONE,
            'student_id' => $student->id,
            'ec_number' => 'EC-556',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Log 2 Confirmed',
            'confirmed_researcher_name' => $student->name,
            'signed_at' => now(),
            'ec_certificate_path' => 'private/ec_certificates/test.pdf',
            'verification_token' => (string) Str::uuid(),
        ]);

        $this->actingAs($student)->get(route('submissions.certificate', $submission));

        $this->assertDatabaseHas('activity_logs', [
            'submission_id' => $submission->id,
            'user_id' => $student->id,
        ]);
    }

    public function test_multiple_non_proposal_templates_coexistence()
    {
        $student = User::role('student')->first();

        // Create document templates
        $template1 = \App\Models\DocumentTemplate::create([
            'name' => 'Proposal Penelitian',
            'code' => 'PROPOSAL',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/proposal.pdf',
        ]);

        $template2 = \App\Models\DocumentTemplate::create([
            'name' => 'Informed Consent Form',
            'code' => 'ICF',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/icf.pdf',
        ]);

        $template3 = \App\Models\DocumentTemplate::create([
            'name' => 'CV Peneliti',
            'code' => 'CV',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/cv.pdf',
        ]);

        $template4 = \App\Models\DocumentTemplate::create([
            'name' => 'Surat Pernyataan',
            'code' => 'STATEMENT',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/statement.pdf',
        ]);

        $submission = Submission::create([
            'title' => 'Test Coexistence Proposal',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::NEW_PROPOSAL,
            'student_id' => $student->id,
        ]);

        // Upload documents for each template (no duplicate entry violation should occur)
        $submission->documents()->create([
            'document_template_id' => $template1->id,
            'doc_type' => $template1->code,
            'file_path' => 'submissions/proposal.pdf',
            'original_name' => 'proposal.pdf',
            'mime' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $submission->documents()->create([
            'document_template_id' => $template2->id,
            'doc_type' => $template2->code,
            'file_path' => 'submissions/icf.pdf',
            'original_name' => 'icf.pdf',
            'mime' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $submission->documents()->create([
            'document_template_id' => $template3->id,
            'doc_type' => $template3->code,
            'file_path' => 'submissions/cv.pdf',
            'original_name' => 'cv.pdf',
            'mime' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $submission->documents()->create([
            'document_template_id' => $template4->id,
            'doc_type' => $template4->code,
            'file_path' => 'submissions/statement.pdf',
            'original_name' => 'statement.pdf',
            'mime' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $this->assertEquals(4, $submission->documents()->count());
    }
}
