<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Models\ActivityLog;
use App\Models\DocumentTemplate;
use App\Models\Submission;
use App\Models\User;
use App\Services\DocumentIntegrityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentTemplateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\DummyUsersSeeder::class);
        Storage::fake('public');
    }

    // ─── 1. Create Template ──────────────────────────────────────

    public function test_admin_can_create_template()
    {
        $admin = User::role('admin')->first();

        $file = UploadedFile::fake()->create('template.pdf', 100, 'application/pdf');

        $response = $this->actingAs($admin)->post(route('admin.templates.store'), [
            'name' => 'New Test Template',
            'code' => 'TEST_TPL',
            'template_file' => $file,
            'description' => 'Test description',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $response->assertRedirect(route('admin.templates.index'));
        $this->assertDatabaseHas('document_templates', [
            'name' => 'New Test Template',
            'code' => 'TEST_TPL',
            'is_required' => true,
            'is_shown' => true,
        ]);
    }

    // ─── 2. Update Template ──────────────────────────────────────

    public function test_admin_can_update_template()
    {
        $admin = User::role('admin')->first();

        $template = DocumentTemplate::create([
            'name' => 'Original Name',
            'code' => 'ORIG',
            'file_path' => 'templates/original.pdf',
            'is_required' => false,
            'is_shown' => true,
        ]);

        Storage::disk('public')->put('templates/original.pdf', 'content');

        $response = $this->actingAs($admin)->put(route('admin.templates.update', $template), [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $response->assertRedirect(route('admin.templates.index'));
        $this->assertDatabaseHas('document_templates', [
            'id' => $template->id,
            'name' => 'Updated Name',
            'code' => 'ORIG', // Code must remain unchanged
            'is_required' => true,
        ]);
    }

    // ─── 3. Archive Template ─────────────────────────────────────

    public function test_admin_can_archive_template()
    {
        $admin = User::role('admin')->first();

        $template = DocumentTemplate::create([
            'name' => 'Archivable Template',
            'code' => 'ARCHIVE_TEST',
            'file_path' => 'templates/archive.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.templates.archive', $template));

        $response->assertRedirect(route('admin.templates.index'));

        $template->refresh();
        $this->assertTrue($template->is_archived);
        $this->assertFalse($template->is_shown);
    }

    // ─── 4. Restore Template ─────────────────────────────────────

    public function test_admin_can_restore_archived_template()
    {
        $admin = User::role('admin')->first();

        $template = DocumentTemplate::create([
            'name' => 'Restorable Template',
            'code' => 'RESTORE_TEST',
            'file_path' => 'templates/restore.pdf',
            'is_required' => true,
            'is_shown' => false,
            'is_archived' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.templates.restore', $template));

        $response->assertRedirect(route('admin.templates.index'));

        $template->refresh();
        $this->assertFalse($template->is_archived);
    }

    // ─── 5. Immutable Code Enforcement ───────────────────────────

    public function test_template_code_is_immutable_after_creation()
    {
        $template = DocumentTemplate::create([
            'name' => 'Immutable Code Template',
            'code' => 'IMMUTABLE',
            'file_path' => 'templates/immutable.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $template->code = 'CHANGED';
        $template->save();
    }

    // ─── 6. Integrity Service ────────────────────────────────────

    public function test_integrity_service_detects_missing_required_documents()
    {
        $student = User::role('student')->first();

        $template = DocumentTemplate::create([
            'name' => 'Required Doc',
            'code' => 'REQUIRED_DOC',
            'file_path' => 'templates/req.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $submission = Submission::create([
            'title' => 'Test Integrity',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::NEW_PROPOSAL,
            'student_id' => $student->id,
        ]);

        $service = new DocumentIntegrityService();
        $result = $service->validateSubmissionDocuments($submission);

        $this->assertGreaterThan(0, $result['missing_required']);
        $this->assertLessThan(100, $result['health_score']);
    }

    // ─── 7. documents:validate Command ───────────────────────────

    public function test_validate_documents_command_runs()
    {
        $this->artisan('documents:validate')
            ->assertSuccessful();
    }

    // ─── 8. documents:repair Dry Run ─────────────────────────────

    public function test_repair_documents_dry_run()
    {
        $this->artisan('documents:repair')
            ->assertExitCode(0);
    }

    // ─── 9. documents:repair --force ─────────────────────────────

    public function test_repair_documents_force_mode()
    {
        $this->artisan('documents:repair', ['--force' => true])
            ->assertExitCode(0);
    }

    // ─── 10. Student Forbidden ───────────────────────────────────

    public function test_student_cannot_access_template_management()
    {
        $student = User::role('student')->first();

        $response = $this->actingAs($student)->get(route('admin.templates.index'));
        $response->assertStatus(403);
    }

    // ─── 11. Reviewer Forbidden ──────────────────────────────────

    public function test_reviewer_cannot_access_template_management()
    {
        $reviewer = User::role('reviewer')->first();

        $response = $this->actingAs($reviewer)->get(route('admin.templates.index'));
        $response->assertStatus(403);
    }

    // ─── 12. Secretary Forbidden ─────────────────────────────────

    public function test_secretary_cannot_access_template_management()
    {
        $secretary = User::role('sekretariat')->first();

        $response = $this->actingAs($secretary)->get(route('admin.templates.index'));
        $response->assertStatus(403);
    }

    // ─── 13. Admin Archive Success ───────────────────────────────

    public function test_admin_archive_template_creates_activity_log()
    {
        $admin = User::role('admin')->first();

        $template = DocumentTemplate::create([
            'name' => 'Log Archive Test',
            'code' => 'LOG_ARCHIVE',
            'file_path' => 'templates/logarchive.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.templates.archive', $template));

        $this->assertDatabaseHas('activity_logs', [
            'new_status' => 'TEMPLATE_ARCHIVED',
            'user_id' => $admin->id,
        ]);
    }

    // ─── 14. Archived Template Hidden From Forms ─────────────────

    public function test_archived_templates_not_in_visible_scope()
    {
        DocumentTemplate::create([
            'name' => 'Active Template',
            'code' => 'ACTIVE',
            'file_path' => 'templates/active.pdf',
            'is_required' => true,
            'is_shown' => true,
            'is_archived' => false,
        ]);

        DocumentTemplate::create([
            'name' => 'Archived Template',
            'code' => 'ARCHIVED',
            'file_path' => 'templates/archived.pdf',
            'is_required' => true,
            'is_shown' => false,
            'is_archived' => true,
        ]);

        $visible = DocumentTemplate::visible()->get();

        $this->assertTrue($visible->contains('code', 'ACTIVE'));
        $this->assertFalse($visible->contains('code', 'ARCHIVED'));
    }

    // ─── 15. Dynamic Rendering ───────────────────────────────────

    public function test_dynamic_templates_render_on_create_page()
    {
        $student = User::role('student')->first();

        DocumentTemplate::create([
            'name' => 'Dynamic Template',
            'code' => 'DYNAMIC',
            'file_path' => 'templates/dynamic.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $response = $this->actingAs($student)->get(route('submissions.create'));
        $response->assertStatus(200);
        $response->assertSee('Dynamic Template');
    }

    // ─── 16. Dashboard Metrics ───────────────────────────────────

    public function test_admin_dashboard_displays_template_metrics()
    {
        $admin = User::role('admin')->first();

        DocumentTemplate::create([
            'name' => 'Metrics Test',
            'code' => 'METRICS',
            'file_path' => 'templates/metrics.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    // ─── 17. Activity Logs Written ───────────────────────────────

    public function test_activity_logs_written_on_template_creation()
    {
        $admin = User::role('admin')->first();

        $file = UploadedFile::fake()->create('tpl.pdf', 100, 'application/pdf');

        $this->actingAs($admin)->post(route('admin.templates.store'), [
            'name' => 'Log Test Template',
            'code' => 'LOG_TEST',
            'template_file' => $file,
            'is_required' => true,
            'is_shown' => true,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'new_status' => 'TEMPLATE_CREATED',
            'user_id' => $admin->id,
        ]);
    }

    // ─── 18. Template Usage Statistics ────────────────────────────

    public function test_template_usage_statistics()
    {
        $student = User::role('student')->first();

        $template = DocumentTemplate::create([
            'name' => 'Usage Stats Template',
            'code' => 'USAGE_STATS',
            'file_path' => 'templates/usage.pdf',
            'is_required' => true,
            'is_shown' => true,
        ]);

        $submission = Submission::create([
            'title' => 'Usage Test',
            'type' => 'Penelitian',
            'status' => SubmissionStatus::NEW_PROPOSAL,
            'student_id' => $student->id,
        ]);

        $submission->documents()->create([
            'document_template_id' => $template->id,
            'doc_type' => $template->code,
            'file_path' => 'submissions/usage.pdf',
            'original_name' => 'usage.pdf',
            'mime' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $this->assertEquals(1, $template->documents()->count());
        $this->assertEquals(1, $template->submissionCount());
        $this->assertTrue($template->isUsed());
    }
}
