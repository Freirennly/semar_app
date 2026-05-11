<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Student permissions
        $studentPerms = [
            'submission.create',
            'submission.view_own',
            'submission.update_own_draft',
            'submission.submit_own',
            'document.upload_own',
            'document.delete_own',
            'document.view_own',
        ];

        // Reviewer permissions
        $reviewerPerms = [
            'submission.view_assigned',
            'review.create',
            'review.update_own',
            'review.submit_own',
        ];

        // Ketua permissions
        $ketuaPerms = [
            'submission.view_all',
            'assignment.manage',
        ];

        // Sekretariat permissions
        $sekretariatPerms = [
            'submission.view_all',
            'doccheck.manage',
            'decision.make',
        ];

        // Admin permissions
        $adminPerms = [
            'user.manage',
            'role.manage',
            'permission.manage',
            'audit.view',
        ];

        $allPerms = array_unique(array_merge(
            $studentPerms, $reviewerPerms, $ketuaPerms, $sekretariatPerms, $adminPerms
        ));

        foreach ($allPerms as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        Role::findOrCreate('student', 'web')->syncPermissions($studentPerms);
        Role::findOrCreate('reviewer', 'web')->syncPermissions($reviewerPerms);
        Role::findOrCreate('ketua', 'web')->syncPermissions($ketuaPerms);
        Role::findOrCreate('sekretariat', 'web')->syncPermissions($sekretariatPerms);
        Role::findOrCreate('admin', 'web')->syncPermissions($adminPerms);
    }
}
