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
        $adminNotifications = $admin->unreadNotifications()->get();
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
        $studentNotifications = $student->unreadNotifications()->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Proposal Diproses', $studentNotifications->first()->data['title']);

        // Step 3: Admin/Ketua assigns Reviewer -> ON_REVIEW
        $student->notifications()->delete(); // Clear
        
        $response = $this->actingAs($ketua)
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
        $revNotifications = $reviewer->unreadNotifications()->get();
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
        $adminNotifications = $admin->unreadNotifications()->get();
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
        $studentNotifications = $student->unreadNotifications()->get();
        $this->assertCount(1, $studentNotifications);
        $this->assertEquals('Keputusan Proposal Etik', $studentNotifications->first()->data['title']);

        // Step 6: Student confirms EC -> WAITING_SIGNATURE
        $student->notifications()->delete(); // Clear

        $response = $this->actingAs($student)
            ->post(route('submissions.confirm', $submission));

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::WAITING_SIGNATURE, $submission->status);

        // Verify Chairman Notification for step 6
        $ketuaNotifications = $ketua->unreadNotifications()->get();
        $this->assertCount(1, $ketuaNotifications);
        $this->assertEquals('Menunggu Tanda Tangan Sertifikat', $ketuaNotifications->first()->data['title']);

        // Step 7: Admin signs certificate -> DONE
        $ketua->notifications()->delete(); // Clear

        $response = $this->actingAs($admin)
            ->put(route('admin.proposals.update', $submission->id), [
                'status' => 'DONE',
            ]);

        $response->assertRedirect();
        $submission->refresh();
        $this->assertEquals(SubmissionStatus::DONE, $submission->status);

        // Verify Student Notification for step 7
        $studentNotifications = $student->unreadNotifications()->get();
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
}
