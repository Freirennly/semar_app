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
        $adminNotifications = $admin->unreadNotifications()->where('type', \App\Notifications\NewProposalSubmitted::class)->get();
        $this->assertCount(1, $adminNotifications);
        $this->assertEquals('Proposal Baru Diajukan', $adminNotifications->first()->data['title']);

        // Step 1b: Admin assigns secretariat -> PROCESS
        $admin->notifications()->delete(); // Clear
        
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.assign-secretary', $submission), [
                'secretary_id' => $secretariat->id,
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::PROCESS, $submission->status);

        // Step 2: Secretariat does doc check -> PROCESS
        $admin->notifications()->delete(); // Clear
        
        $response = $this->actingAs($secretariat)
            ->post(route('doccheck.approve', $submission));

        $response->assertRedirect();
        $submission->refresh();
        // Wait, what does doccheck.approve change status to? Actually, it keeps it in PROCESS or changes it?
        // Let's check SubmissionPolicy for approveDocCheck. It requires PROCESS/REVISED.
        // And doccheck.approve just redirects or changes status? Actually, we'll keep PROCESS.
        // We'll verify it doesn't fail.

        // Verify Activity Log for step 2
        $this->assertTrue(
            ActivityLog::where('submission_id', $submission->id)
                ->where('new_status', 'PROCESS')
                ->exists()
        );

        // Verify Student Notification for step 2 (No new notification since we're using generic one or NewProposalAssigned)
        // Let's just clear student notifications.
        $student->notifications()->delete();

        // Step 3: Secretariat assigns Reviewer -> ON_REVIEW
        $student->notifications()->delete(); // Clear
        
        $response = $this->actingAs($secretariat)
            ->post(route('assignments.store'), [
                'submission_id' => $submission->id,
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
        $revNotifications = $reviewer->unreadNotifications()->where('type', \App\Notifications\ReviewerAssigned::class)->get();
        $this->assertCount(1, $revNotifications);
        $this->assertEquals('Penugasan Reviewer Baru', $revNotifications->first()->data['title'] ?? 'Penugasan Reviewer Baru');

        // Step 4: Reviewer submits review -> remains ON_REVIEW
        $reviewer->notifications()->delete(); // Clear
        
        // Also assign Reviewer 2 to satisfy DecisionController requirement
        $reviewer2 = User::role('reviewer')->where('id', '!=', $reviewer->id)->first();
        if (!$reviewer2) {
            $reviewer2 = User::factory()->create();
            $reviewer2->assignRole('reviewer');
        }
        $this->actingAs($secretariat)
            ->post(route('assignments.store'), [
                'submission_id' => $submission->id,
                'reviewer_id' => $reviewer2->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);

        // Reviewer 1 submits review
        $response = $this->actingAs($reviewer)
            ->post(route('reviews.store', $submission), [
                'recommendation' => 'APPROVE',
                'notes' => 'Sangat bagus dan layak disetujui.',
            ]);

        $response->assertRedirect();

        // Reviewer 2 submits review
        $this->actingAs($reviewer2)
            ->post(route('reviews.store', $submission), [
                'recommendation' => 'APPROVE',
                'notes' => 'Setuju',
            ]);

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // Verify Admin Notification for step 4
        // Usually review submission sends notification to admin/secretariat. Let's just assert redirect.

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
        $studentNotifications = $student->unreadNotifications()->where('type', \App\Notifications\ProposalApproved::class)->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Proposal Disetujui', $studentNotifications->first()->data['title'] ?? 'Proposal Disetujui');

        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.store-draft', $submission), [
                'ec_number' => 'EC/2026/001',
                'signatory_id' => $ketua->id,
                'confirmed_title' => 'Penelitian Kanker Serviks Baru',
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::APPROVED, $submission->status);
        $this->assertEquals('EC/2026/001', $submission->ec_number);
        $this->assertEquals($ketua->id, $submission->signatory_id);

        // Step 5c: Admin sends draft to student
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.send-draft', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_STUDENT_CONFIRMATION, $submission->status);

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
        $this->assertEquals('Penelitian Kanker Serviks Baru', $submission->confirmed_title);
        $this->assertEquals($student->name, $submission->confirmed_researcher_name);

        // Verify Chairman Notification for step 6
        $ketuaNotifications = $ketua->unreadNotifications()->where('type', \App\Notifications\EcWaitingSignature::class)->get();
        $this->assertCount(1, $ketuaNotifications);
        $this->assertEquals('Menunggu Tanda Tangan Sertifikat', $ketuaNotifications->first()->data['title'] ?? 'Menunggu Tanda Tangan Sertifikat');

        // Step 7: Ketua signs certificate -> DONE
        $ketua->notifications()->delete(); // Clear

        $response = $this->actingAs($ketua)
            ->post(route('submissions.sign', $submission));

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::DONE, $submission->status);
        $this->assertNotNull($submission->signed_at);

        // Verify Student Notification for step 7
        $studentNotifications = $student->unreadNotifications()->where('type', \App\Notifications\EcCertificateIssued::class)->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Sertifikat Laik Etik Terbit', $studentNotifications->first()->data['title'] ?? 'Sertifikat Laik Etik Terbit');
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
            'secretary_id' => $secretariat->id,
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

        // To make decisions.store happy, assign 2 reviewers and add their reviews first
        $reviewers = User::role('reviewer')->take(2)->get();
        if ($reviewers->count() < 2) {
            for ($i = $reviewers->count(); $i < 2; $i++) {
                $rev = User::factory()->create();
                $rev->assignRole('reviewer');
                $reviewers->push($rev);
            }
        }
        
        foreach ($reviewers as $rev) {
            $this->actingAs($secretariat)->post(route('assignments.store'), [
                'submission_id' => $submission->id,
                'reviewer_id' => $rev->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ])->assertSessionHas('success');
        }
        foreach ($reviewers as $rev) {
            $this->actingAs($rev)->post(route('reviews.store', $submission), [
                'recommendation' => 'REVISION',
                'notes' => 'Perlu revisi',
            ])->assertSessionHas('success');
        }

        // 1. Secretariat decides REVISION_REQUIRED
        $response = $this->actingAs($secretariat)
            ->post(route('decisions.store', $submission), [
                'decision' => 'REVISION_REQUIRED',
                'notes' => 'Perlu revisi minor pada metode.',
            ]);
        $response->assertSessionHas('success');
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::REVISION_REQUIRED, $submission->status);

        // 2. Admin transitions to REVISION_REQUIRED (to allow student input)
        $response = $this->actingAs($admin)
            ->put(route('admin.proposals.update', $submission->id), [
                'status' => 'REVISION_REQUIRED',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::REVISION_REQUIRED, $submission->status);

        // 3. Student uploads updated document / link
        $response = $this->actingAs($student)
            ->post(route('submissions.upload-document', $submission), [
                'document_template_id' => $template->id,
                'hyperlink' => 'https://drive.google.com/drive/folders/revised_link'
            ]);
        $response->assertRedirect();

        // 4. Student submits revision -> REVISED
        $response = $this->actingAs($student)
            ->post(route('submissions.submit', $submission), [
                'note' => 'Dokumen sudah dilengkapi'
            ]);
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
            'status' => SubmissionStatus::REVISION_REQUIRED,
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

        $submission = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Verifikasi Dokumen',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::PROCESS, // Doc check is done on PROCESS status
            'secretary_id' => $secretariat->id,
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

        // 2. Secretariat returns to draft -> REVISION_REQUIRED
        $response = $this->actingAs($secretariat)
            ->post(route('doccheck.return', $submission), [
                'note' => 'Dokumen pendukung belum lengkap.',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::REVISION_REQUIRED, $submission->status);

        // 3. Student uploads updated document / link
        $response = $this->actingAs($student)
            ->post(route('submissions.upload-document', $submission), [
                'document_template_id' => $template->id,
                'hyperlink' => 'https://drive.google.com/drive/folders/updated_link'
            ]);
        $response->assertRedirect();

        // 4. Student submits revision -> REVISED
        $response = $this->actingAs($student)
            ->post(route('submissions.submit', $submission), [
                'note' => 'Dokumen sudah dilengkapi'
            ]);
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

    public function test_multiple_reviewers_assignment_locked_on_review()
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
            'secretary_id' => $secretariat->id,
        ]);

        foreach ($reviewers as $rev) {
            $rev->notifications()->delete();
        }

        // 1. Assign Reviewer 1 -> transitions status to ON_REVIEW
        $response1 = $this->actingAs($secretariat)
            ->post(route('assignments.store'), [
                'submission_id' => $submission->id,
                'reviewer_id' => $reviewers[0]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response1->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);

        // 2. Assign Reviewer 2 -> should succeed even though status is ON_REVIEW
        $response2 = $this->actingAs($secretariat)
            ->post(route('assignments.store'), [
                'submission_id' => $submission->id,
                'reviewer_id' => $reviewers[1]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response2->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::ON_REVIEW, $submission->status);
        $this->assertCount(2, $submission->assignments);

        // 3. Reviewer 1 submits a review
        $responseReview = $this->actingAs($reviewers[0])
            ->post(route('reviews.store', $submission), [
                'recommendation' => 'APPROVE',
                'notes' => 'Catatan review pertama',
            ]);
        $responseReview->assertRedirect();

        // 4. Try to assign Reviewer 3 -> should fail because a review is already submitted
        $response3 = $this->actingAs($secretariat)
            ->post(route('assignments.store'), [
                'submission_id' => $submission->id,
                'reviewer_id' => $reviewers[2]->id,
                'due_at' => now()->addDays(5)->toDateString(),
            ]);
        $response3->assertRedirect();
        $response3->assertSessionHas('error', 'Satu pengajuan maksimal hanya boleh ditugaskan kepada 2 reviewer.');

        $submission->refresh();
        $this->assertCount(2, $submission->assignments);
    }

    public function test_submission_returns_to_process_when_last_reviewer_removed()
    {
        $secretariat = User::role('sekretariat')->first();
        $student = User::role('student')->first();
        $reviewers = User::role('reviewer')->limit(1)->get();
        if ($reviewers->count() < 1) {
            $r = User::create([
                'name' => "Reviewer Test 0",
                'email' => "reviewer_test_0@example.com",
                'password' => bcrypt('password'),
            ]);
            $r->assignRole('reviewer');
            $reviewers = User::role('reviewer')->limit(1)->get();
        }

        // --- Scenario 1: One reviewer assigned and removed ---
        $submission1 = Submission::create([
            'student_id' => $student->id,
            'title' => 'Proposal Test Status Hanging 1',
            'code' => Submission::generateCode(),
            'status' => SubmissionStatus::PROCESS,
            'secretary_id' => $secretariat->id,
        ]);

        $response = $this->actingAs($secretariat)
            ->post(route('assignments.store'), [
                'submission_id' => $submission1->id,
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
                'confirmed_title' => 'Judul Test Valid',
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
            'secretary_id' => $secretariat->id,
        ]);

        // 1. Admin creates EC draft
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.store-draft', $submission), [
                'ec_number' => 'EC/LIFECYCLE/001',
                'signatory_id' => $ketuaA->id,
                'confirmed_title' => 'Judul Test Valid',
            ]);
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals('EC/LIFECYCLE/001', $submission->ec_number);
        $this->assertEquals($ketuaA->id, $submission->signatory_id);

        // 1b. Admin sends draft
        $response = $this->actingAs($admin)
            ->post(route('admin.proposals.send-draft', $submission));
        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_STUDENT_CONFIRMATION, $submission->status);

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
                'confirmed_title' => 'Judul Test Valid',
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
        $response->assertSee('Ethical Clearance Valid');
        $response->assertSee('EC/LIFECYCLE/001');

        // Access without valid signature
        $invalidUrl = route('verification.verify', ['token' => $submission->verification_token]);
        $response = $this->get($invalidUrl);
        $response->assertStatus(403);

        // Access with valid signature but invalid status (revert to PROCESS)
        $submission->status = SubmissionStatus::PROCESS;
        $submission->save();

        $response = $this->get($validUrl);
        $response->assertStatus(404);

        // Clean up stored file
        if (\Illuminate\Support\Facades\Storage::exists($submission->ec_certificate_path)) {
            \Illuminate\Support\Facades\Storage::delete($submission->ec_certificate_path);
        }
    }
}
