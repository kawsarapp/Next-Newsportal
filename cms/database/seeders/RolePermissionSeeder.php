<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ────────────────────────────────────────────────────────
        $permissions = [
            // Posts
            'post.view', 'post.create', 'post.edit', 'post.delete',
            'post.publish', 'post.approve', 'post.schedule',
            'post.mark_breaking', 'post.mark_featured',

            // Editorial
            'editorial.review', 'editorial.fact_check',
            'editorial.copy_edit', 'editorial.legal_review',
            'editorial.assign',

            // Categories
            'category.view', 'category.create', 'category.edit', 'category.delete',

            // Media
            'media.view', 'media.upload', 'media.delete',

            // Users
            'user.view', 'user.create', 'user.edit', 'user.delete', 'user.invite',

            // Reporters
            'reporter.view', 'reporter.manage', 'reporter.payment',

            // SEO
            'seo.view', 'seo.manage',

            // Social
            'social.view', 'social.publish', 'social.manage_accounts',

            // AI
            'ai.use', 'ai.manage_settings',

            // Analytics
            'analytics.view',

            // Ads
            'ads.view', 'ads.manage',

            // Settings
            'settings.view', 'settings.manage',

            // Audit
            'audit.view',

            // Portal (super admin only)
            'portal.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ── Roles & Their Permissions ──────────────────────────────────────────

        // 1. Super Admin — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. Portal Admin — all except portal management
        $portalAdmin = Role::firstOrCreate(['name' => 'portal_admin', 'guard_name' => 'web']);
        $portalAdmin->givePermissionTo(array_filter($permissions, fn($p) => $p !== 'portal.manage'));

        // 3. Chief Editor
        $chiefEditor = Role::firstOrCreate(['name' => 'chief_editor', 'guard_name' => 'web']);
        $chiefEditor->givePermissionTo([
            'post.view', 'post.create', 'post.edit', 'post.delete',
            'post.publish', 'post.approve', 'post.schedule',
            'post.mark_breaking', 'post.mark_featured',
            'editorial.review', 'editorial.fact_check', 'editorial.copy_edit',
            'editorial.legal_review', 'editorial.assign',
            'category.view', 'category.create', 'category.edit',
            'media.view', 'media.upload', 'media.delete',
            'reporter.view', 'reporter.manage',
            'seo.view', 'seo.manage',
            'social.view', 'social.publish',
            'ai.use', 'analytics.view', 'audit.view',
        ]);

        // 4. Editor
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editor->givePermissionTo([
            'post.view', 'post.create', 'post.edit',
            'post.approve', 'post.schedule', 'post.mark_breaking', 'post.mark_featured',
            'editorial.review', 'editorial.fact_check', 'editorial.copy_edit', 'editorial.assign',
            'category.view', 'media.view', 'media.upload',
            'reporter.view', 'seo.view', 'social.view', 'social.publish',
            'ai.use', 'analytics.view',
        ]);

        // 5. Sub Editor
        $subEditor = Role::firstOrCreate(['name' => 'sub_editor', 'guard_name' => 'web']);
        $subEditor->givePermissionTo([
            'post.view', 'post.create', 'post.edit',
            'editorial.review', 'editorial.copy_edit',
            'category.view', 'media.view', 'media.upload',
            'ai.use',
        ]);

        // 6. Reporter
        $reporter = Role::firstOrCreate(['name' => 'reporter', 'guard_name' => 'web']);
        $reporter->givePermissionTo([
            'post.view', 'post.create', 'post.edit',
            'media.view', 'media.upload',
            'ai.use',
        ]);

        // 7. Contributor (guest reporter)
        $contributor = Role::firstOrCreate(['name' => 'contributor', 'guard_name' => 'web']);
        $contributor->givePermissionTo([
            'post.view', 'post.create',
            'media.view', 'media.upload',
        ]);

        // 8. Advertiser
        $advertiser = Role::firstOrCreate(['name' => 'advertiser', 'guard_name' => 'web']);
        $advertiser->givePermissionTo([
            'ads.view',
        ]);

        $this->command->info('✅ Roles and permissions seeded successfully.');
    }
}
