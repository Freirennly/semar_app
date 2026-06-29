<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\User;
use App\Models\Submission;
use App\Models\ActivityLog;
use App\Models\DocumentTemplate;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_complete_automated_workflow()
    {
        // 1. Get users by role
        $student = User::role('student')->first();
        $admin = User::role('admin')->first();
        $secretariat = User::role('sekretariat')->first();
        $reviewer = User::role('reviewer')->first();
        $ketua = User::role('ketua')->first();

        $this->assertNotNull($student);
        $this->assertNotNull($admin);
        $this->assertNotNull($secretariat);
        $this->assertNotNull($reviewer);
        $this->assertNotNull($ketua);

        // Clear all initial notifications
        $admin->notifications()->delete();
        $secretariat->notifications()->delete();
        $reviewer->notifications()->delete();
        $student->notifications()->delete();

        // 2. Create a Document Template (Required) so Student hasAllDocuments is happy
        $template = DocumentTemplate::create([
            'name' => 'Proposal Penelitian',
            'description' => 'File proposal',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/test.pdf',
        ]);

        // Step 1: Mahasiswa submits proposal -> NEW_PROPOSAL
        $response = $this->actingAs($student)
            ->post(route('submissions.store'), [
                'title' => 'Penelitian Kanker Serviks Baru',
                'type' => 'Penelitian',
                'abstract' => 'Abstrak baru',
                'files' => [
                    $template->id => \Illuminate\Http\UploadedFile::fake()->create('proposal.pdf', 100)
                ]
            ]);

        $response->assertRedirect();
        $submission = Submission::first();
        $this->assertEquals(SubmissionStatus::NEW_PROPOSAL, $submission->status);

        // Verify Admin Notification for step 1
        $adminNotifications = $admin->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $adminNotifications);
        $this->assertEquals('Proposal Baru Diajukan', $adminNotifications->first()->data['title']);

        // Step 2: Secretariat does doc check -> PROCESS
        $admin->notifications()->delete(); // Clear
        
        $response = $this->actingAs($secretariat)
            ->post(route('doccheck.approve', $submission));

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::PROCESS, $submission->status);

        // Verify Activity Log for step 2
        $this->assertTrue(
            ActivityLog::where('submission_id', $submission->id)
                ->where('new_status', 'PROCESS')
                ->exists()
        );

        // Verify Student Notification for step 2
        $studentNotifications = $student->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Proposal Diproses', $studentNotifications->first()->data['title']);

        // Step 3: Secretariat assigns Reviewer -> ON_REVIEW
        $student->notifications()->delete(); // Clear
        
        $response = $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission), [
                'reviewer_id' => $reviewer->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // Verify Activity Log for step 3
        $this->assertTrue(
            ActivityLog::where('submission_id', $submission->id)
                ->where('new_status', 'ON_REVIEW')
                ->exists()
        );

        // Verify Reviewer Notification for step 3
        $revNotifications = $reviewer->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $revNotifications);
        $this->assertEquals('Penugasan Reviewer Baru', $revNotifications->first()->data['title']);

        // Step 4: Reviewer submits review -> remains ON_REVIEW
        $reviewer->notifications()->delete(); // Clear
        
        $response = $this->actingAs($reviewer)
            ->post(route('reviews.store', $submission), [
                'recommendation' => 'APPROVE',
                'notes' => 'Sangat bagus dan layak disetujui.',
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // Verify Admin Notification for step 4
        $adminNotifications = $admin->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $adminNotifications);
        $this->assertEquals('Hasil Review Masuk', $adminNotifications->first()->data['title']);

        // Step 5: Secretariat makes final decision -> APPROVED
        $admin->notifications()->delete(); // Clear
        
        $response = $this->actingAs($secretariat)
            ->post(route('decisions.store', $submission), [
                'decision' => 'APPROVED',
                'notes' => 'Disetujui sepenuhnya oleh KEP.',
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::APPROVED, $submission->status);

        // Verify Activity Log for step 5
        $this->assertTrue(
            ActivityLog::where('submission_id', $submission->id)
                ->where('new_status', 'APPROVED')
                ->exists()
        );

        // Verify Student Notification for step 5
        $studentNotifications = $student->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Keputusan Proposal Etik', $studentNotifications->first()->data['title']);

        // Step 5b: Admin creates EC draft -> status remains APPROVED
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.store-draft', $submission), [
                'ec_number' => 'EC/2026/001',
                'signatory_id' => $ketua->id,
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::APPROVED, $submission->status);
        $this->assertEquals('EC/2026/001', $submission->ec_number);
        $this->assertEquals($ketua->id, $submission->signatory_id);

        // Step 6: Student confirms EC -> WAITING_SIGNATURE
        $student->notifications()->delete(); // Clear

        $response = $this->actingAs($student)
            ->post(route('submissions.confirm', $submission), [
                'confirmed_title' => 'Judul Penelitian Kanker Terkonfirmasi',
                'confirmed_researcher_name' => 'Nama Peneliti Terkonfirmasi',
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_SIGNATURE, $submission->status);
        $this->assertEquals('Judul Penelitian Kanker Terkonfirmasi', $submission->confirmed_title);
        $this->assertEquals('Nama Peneliti Terkonfirmasi', $submission->confirmed_researcher_name);

        // Verify Chairman Notification for step 6
        $ketuaNotifications = $ketua->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $ketuaNotifications);
        $this->assertEquals('Menunggu Tanda Tangan Sertifikat', $ketuaNotifications->first()->data['title']);

        // Step 7: Ketua signs certificate -> DONE
        $ketua->notifications()->delete(); // Clear

        $response = $this->actingAs($ketua)
            ->post(route('submissions.sign', $submission));

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::DONE, $submission->status);
        $this->assertNotNull($submission->signed_at);

        // Verify Student Notification for step 7
        $studentNotifications = $student->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Sertifikat Laik Etik Terbit', $studentNotifications->first()->data['title']);
    }

    public function test_revision_and_resubmission_loop()
    {
        $student = User::role('student')->first();
        $secretariat = User::role('sekretariat')->first();
        $admin = User::role('admin')->first();

        $template = DocumentTemplate::create([
            'name' => 'Proposal Penelitian',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/test.pdf',
        ]);

        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Revisi',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::ON_REVIEW,
        ]);

        // Upload initial doc
        $submission->documents()->create([
            'document_template_id' => $template->id,
            'doc_type' => 'PROPOSAL',
            'file_path' => 'submissions/test.pdf',
            'original_name' => 'test.pdf',
            'mime' => 'application/pdf',
            'size' => 1024,
            'uploaded_by' => $student->id,
            'type' => 'file',
        ]);

        // 1. Secretariat decides APPROVED_WITH_REVISION
        $response = $this->actingAs($secretariat)
            ->post(route('decisions.store', $submission), [
                'decision' => 'APPROVED_WITH_REVISION',
                'notes' => 'Perlu revisi minor pada metode.',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::APPROVED_WITH_REVISION, $submission->status);

        // 2. Admin transitions to RESUBMISSION (to allow student input)
        $response = $this->actingAs($admin)
            ->put(route('admin.proposals.update', $submission->id), [
                'status' => 'RESUBMISSION',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::RESUBMISSION, $submission->status);

        // 3. Student uploads updated document / link
        $response = $this->actingAs($student)
            ->post(route('submissions.upload-document', $submission), [
                'document_template_id' => $template->id,
                'hyperlink' => 'https://drive.google.com/drive/folders/revised_link'
            ]);
        $response->assertRedirect();

        // 4. Student submits revision -> REVISED
        $response = $this->actingAs($student)
            ->post(route('submissions.submit', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::REVISED, $submission->status);

        // 5. Secretariat approves revision -> PROCESS
        $response = $this->actingAs($secretariat)
            ->post(route('doccheck.approve', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::PROCESS, $submission->status);
    }

    public function test_student_document_upload_pdf_or_link()
    {
        $student = User::role('student')->first();
        $template = DocumentTemplate::create([
            'name' => 'Informed Consent',
            'description' => 'File ICF',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/test_icf.pdf',
        ]);

        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Penelitian Baru',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::RESUBMISSION,
        ]);

        // Test uploading Google Drive link via update (edit save)
        $response = $this->actingAs($student)
            ->put(route('submissions.update', $submission), [
                'title' => 'Penelitian Baru Edit',
                'type' => 'Penelitian',
                'abstract' => 'Abstrak baru',
                'hyperlinks' => [
                    $template->id => 'https://drive.google.com/drive/folders/test_link'
                ]
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'id' => $submission->id,
            'title' => 'Penelitian Baru Edit',
        ]);
        $this->assertDatabaseHas('submission_documents', [
            'submission_id' => $submission->id,
            'document_template_id' => $template->id,
            'mime' => 'text/url',
            'file_path' => 'https://drive.google.com/drive/folders/test_link',
        ]);
        $this->assertEquals('link', $submission->refresh()->documents->firstWhere('document_template_id', $template->id)->type);

        // Test uploadDocument endpoint with hyperlink
        $response2 = $this->actingAs($student)
            ->post(route('submissions.upload-document', $submission), [
                'document_template_id' => $template->id,
                'hyperlink' => 'https://drive.google.com/drive/folders/new_link'
            ]);
        $response2->assertRedirect();
        $this->assertDatabaseHas('submission_documents', [
            'submission_id' => $submission->id,
            'document_template_id' => $template->id,
            'mime' => 'text/url',
            'file_path' => 'https://drive.google.com/drive/folders/new_link',
        ]);
        $this->assertEquals('link', $submission->refresh()->documents->firstWhere('document_template_id', $template->id)->type);
    }

    public function test_document_check_return_to_draft_and_resubmit()
    {
        $student = User::role('student')->first();
        $secretariat = User::role('sekretariat')->first();

        $template = DocumentTemplate::create([
            'name' => 'Proposal Penelitian',
            'is_required' => true,
            'is_shown' => true,
            'file_path' => 'templates/test.pdf',
        ]);

        // 1. Student submits proposal -> NEW_PROPOSAL
        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Verifikasi Dokumen',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::NEW_PROPOSAL,
        ]);

        $submission->documents()->create([
            'document_template_id' => $template->id,
            'doc_type' => 'PROPOSAL',
            'file_path' => 'submissions/test.pdf',
            'original_name' => 'test.pdf',
            'mime' => 'application/pdf',
            'size' => 1024,
            'uploaded_by' => $student->id,
            'type' => 'file',
        ]);

        // 2. Secretariat returns to draft -> RESUBMISSION
        $response = $this->actingAs($secretariat)
            ->post(route('doccheck.return', $submission), [
                'note' => 'Dokumen pendukung belum lengkap.',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::RESUBMISSION, $submission->status);

        // 3. Student uploads updated document / link
        $response = $this->actingAs($student)
            ->post(route('submissions.upload-document', $submission), [
                'document_template_id' => $template->id,
                'hyperlink' => 'https://drive.google.com/drive/folders/updated_link'
            ]);
        $response->assertRedirect();

        // 4. Student submits revision -> REVISED
        $response = $this->actingAs($student)
            ->post(route('submissions.submit', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::REVISED, $submission->status);

        // 5. Secretariat approves document check -> PROCESS
        $response = $this->actingAs($secretariat)
            ->post(route('doccheck.approve', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::PROCESS, $submission->refresh()->status);
    }

    public function test_multiple_reviewers_assignment_notifications()
    {
        $secretariat = User::role('sekretariat')->first();
        $student = User::role('student')->first();
        
        $reviewers = User::role('reviewer')->limit(3)->get();
        if ($reviewers->count() < 3) {
            for ($i = $reviewers->count(); $i < 3; $i++) {
                $r = User::create([
                    'name' => "Reviewer Test {$i}",
                    'email' => "reviewer_test_{$i}@example.com",
                    'password' => bcrypt('password'),
                ]);
                $r->assignRole('reviewer');
            }
            $reviewers = User::role('reviewer')->limit(3)->get();
        }

        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Multi Reviewer',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::PROCESS,
        ]);

        foreach ($reviewers as $rev) {
            $rev->notifications()->delete();
        }

        // 1. Assign Reviewer 1 -> transitions status to ON_REVIEW
        $response1 = $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission), [
                'reviewer_id' => $reviewers[0]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response1->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // Verify Reviewer 1 notified
        $this->assertCount(1, $reviewers[0]->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get());
        $this->assertEquals('Penugasan Reviewer Baru', $reviewers[0]->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->first()->data['title']);

        // 2. Assign Reviewer 2 -> status remains ON_REVIEW
        $response2 = $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission), [
                'reviewer_id' => $reviewers[1]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response2->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // Verify Reviewer 2 notified
        $this->assertCount(1, $reviewers[1]->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get());
        $this->assertEquals('Penugasan Reviewer Baru', $reviewers[1]->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->first()->data['title']);

        // 3. Assign Reviewer 3 -> status remains ON_REVIEW
        $response3 = $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission), [
                'reviewer_id' => $reviewers[2]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response3->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // Verify Reviewer 3 notified
        $this->assertCount(1, $reviewers[2]->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->get());
        $this->assertEquals('Penugasan Reviewer Baru', $reviewers[2]->unreadNotifications()->where('type', \App\Notifications\SubmissionWorkflowNotification::class)->first()->data['title']);
    }

    public function test_submission_returns_to_process_when_last_reviewer_removed()
    {
        $secretariat = User::role('sekretariat')->first();
        $student = User::role('student')->first();
        $reviewers = User::role('reviewer')->limit(2)->get();
        if ($reviewers->count() < 2) {
            for ($i = $reviewers->count(); $i < 2; $i++) {
                $r = User::create([
                    'name' => "Reviewer Test {$i}",
                    'email' => "reviewer_test_{$i}@example.com",
                    'password' => bcrypt('password'),
                ]);
                $r->assignRole('reviewer');
            }
            $reviewers = User::role('reviewer')->limit(2)->get();
        }

        // --- Scenario 1: One reviewer assigned and removed ---
        $submission1 = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Test Status Hanging 1',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::PROCESS,
        ]);

        $response = $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission1), [
                'reviewer_id' => $reviewers[0]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response->assertRedirect();
        $submission1->refresh();

        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission1->status);

        $assignment1 = $submission1->assignments->first();

        $response = $this->actingAs($secretariat)
            ->delete(route('assignments.destroy', $assignment1));
        $response->assertRedirect();
        $submission1->refresh();

        $this->assertEquals(SubmissionStatus::PROCESS, $submission1->status);


        // --- Scenario 2: Two reviewers assigned, removed one by one ---
        $submission2 = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Test Status Hanging 2',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::PROCESS,
        ]);

        $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission2), [
                'reviewer_id' => $reviewers[0]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);

        $this->actingAs($secretariat)
            ->post(route('assignments.store', $submission2), [
                'reviewer_id' => $reviewers[1]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);

        $submission2->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission2->status);
        $this->assertCount(2, $submission2->assignments);

        $assignmentA = $submission2->assignments->where('reviewer_id', $reviewers[0]->id)->first();
        $assignmentB = $submission2->assignments->where('reviewer_id', $reviewers[1]->id)->first();

        $response = $this->actingAs($secretariat)
            ->delete(route('assignments.destroy', $assignmentA));
        $response->assertRedirect();
        $submission2->refresh();

        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission2->status);

        $response = $this->actingAs($secretariat)
            ->delete(route('assignments.destroy', $assignmentB));
        $response->assertRedirect();
        $submission2->refresh();

        $this->assertEquals(SubmissionStatus::PROCESS, $submission2->status);
    }

    public function test_student_cannot_confirm_before_draft_exists()
    {
        $student = User::role('student')->first();
        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Test Confirmation',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::APPROVED, // Approved, but ec_number is NULL
        ]);

        $response = $this->actingAs($student)
            ->post(route('submissions.confirm', $submission), [
                'confirmed_title' => 'Test Confirmation',
                'confirmed_researcher_name' => 'Student Name',
            ]);

        $response->assertSessionHas('error');
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::APPROVED, $submission->status);
    }

    public function test_ketua_cannot_sign_incomplete_draft()
    {
        $ketua = User::role('ketua')->first();
        $submission = Submission::create([
            'student_id' => User::role('student')->first()->id,
            'title' => 'Test Sign',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::WAITING_SIGNATURE,
            'signatory_id' => $ketua->id,
            // Incomplete draft fields: ec_number is null
            'ec_number' => null, 
        ]);

        $response = $this->actingAs($ketua)
            ->post(route('submissions.sign', $submission));

        $response->assertSessionHas('error');
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_SIGNATURE, $submission->status);
    }

    public function test_admin_cannot_create_draft_for_non_approved_submissions()
    {
        $admin = User::role('admin')->first();
        $student = User::role('student')->first();
        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Test Draft Non Approved',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::PROCESS, // non-APPROVED
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.store-draft', $submission), [
                'ec_number' => 'EC/999',
                'signatory_id' => User::role('ketua')->first()->id,
            ]);

        $response->assertStatus(403);
        $submission->refresh();
        $this->assertNull($submission->ec_number);
    }

    public function test_non_ketua_users_cannot_sign()
    {
        $admin = User::role('admin')->first();
        $student = User::role('student')->first();
        $ketua = User::role('ketua')->first();
        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Test Sign Restriction',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::WAITING_SIGNATURE,
            'ec_number' => 'EC/123',
            'signatory_id' => $ketua->id,
            'confirmed_title' => 'Test Sign Restriction',
            'confirmed_researcher_name' => 'Student Name',
        ]);

        // Non-ketua (e.g. admin) tries to sign
        $response = $this->actingAs($admin)
            ->post(route('submissions.sign', $submission));

        $response->assertStatus(403);
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_SIGNATURE, $submission->status);
    }

    public function test_phase4_ethical_clearance_lifecycle()
    {
        // Setup Roles and Users
        $student = User::role('student')->first();
        $admin = User::role('admin')->first();
        $secretariat = User::role('sekretariat')->first();
        
        $ketuaA = User::role('ketua')->first();
        // Create Ketua B
        $ketuaB = User::factory()->create();
        $ketuaB->assignRole('ketua');

        // Create another student
        $otherStudent = User::factory()->create();
        $otherStudent->assignRole('student');

        // Create a submission in APPROVED status
        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Lifecycle Test Proposal',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::APPROVED,
        ]);

        // 1. Admin creates EC draft
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.store-draft', $submission), [
                'ec_number' => 'EC/LIFECYCLE/001',
                'signatory_id' => $ketuaA->id,
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals('EC/LIFECYCLE/001', $submission->ec_number);
        $this->assertEquals($ketuaA->id, $submission->signatory_id);

        // 2. Student confirms EC data -> WAITING_SIGNATURE
        $response = $this->actingAs($student)
            ->post(route('submissions.confirm', $submission), [
                'confirmed_title' => 'Confirmed Lifecycle Test Title',
                'confirmed_researcher_name' => 'Confirmed Lifecycle Student',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_SIGNATURE, $submission->status);

        // 3. Test Draft Immutability: Admin tries to store draft again
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.store-draft', $submission), [
                'ec_number' => 'EC/LIFECYCLE/EDITED',
                'signatory_id' => $ketuaA->id,
            ]);
        $response->assertStatus(403);

        // 4. Test Signatory Integrity: Ketua B (not assigned) tries to sign
        $response = $this->actingAs($ketuaB)
            ->post(route('submissions.sign', $submission));
        $response->assertStatus(403);

        // 5. Selected Signatory (Ketua A) signs -> transitions to DONE
        $response = $this->actingAs($ketuaA)
            ->post(route('submissions.sign', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::DONE, $submission->status);

        // 6. Test Certificate Generation & Storage
        $this->assertNotEmpty($submission->ec_certificate_path);
        $this->assertTrue(\Illuminate\Support\Facades\Storage::exists($submission->ec_certificate_path));

        // 7. Test Download Permissions
        // Admin: allowed
        $response = $this->actingAs($admin)
            ->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);

        // Secretariat: allowed
        $response = $this->actingAs($secretariat)
            ->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);

        // Owner Student: allowed
        $response = $this->actingAs($student)
            ->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);

        // Assigned Ketua A: allowed
        $response = $this->actingAs($ketuaA)
            ->get(route('submissions.certificate', $submission));
        $response->assertStatus(200);

        // Unassigned Ketua B: forbidden
        $response = $this->actingAs($ketuaB)
            ->get(route('submissions.certificate', $submission));
        $response->assertStatus(403);

        // Other Student: forbidden
        $response = $this->actingAs($otherStudent)
            ->get(route('submissions.certificate', $submission));
        $response->assertStatus(403);

        // 8. Test Public Verification Page
        // Generate valid temporary signed URL
        $validUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addYear(),
            ['token' => $submission->verification_token]
        );
        $response = $this->get($validUrl);
        $response->assertStatus(200);
        $response->assertSee('Validated / Terverifikasi');
        $response->assertSee('EC/LIFECYCLE/001');

        // Access without valid signature
        $invalidUrl = route('verification.verify', ['token' => $submission->verification_token]);
        $response = $this->get($invalidUrl);
        $response->assertStatus(403);

        // Access with valid signature but invalid status (revert to PROCESS)
        $submission->status = SubmissionStatus::PROCESS;
        $submission->save();

        $response = $this->get($validUrl);
        $response->assertStatus(200);
        $response->assertSee('Certificate Not Valid');

        // Clean up stored file
        if (\Illuminate\Support\Facades\Storage::exists($submission->ec_certificate_path)) {
            \Illuminate\Support\Facades\Storage::delete($submission->ec_certificate_path);
        }
    }
}
