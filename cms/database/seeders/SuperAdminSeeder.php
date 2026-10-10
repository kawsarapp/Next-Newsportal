<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Portal;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin — portal_id = null (access all portals)
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@newsportal.dev'],
            [
                'portal_id' => null,
                'name'      => 'Super Admin',
                'username'  => 'superadmin',
                'password'  => Hash::make('Admin@12345'),
                'status'    => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        // Portal Admin for demo portal
        $portal = Portal::where('slug', 'demo-portal')->first();

        if ($portal) {
            $portalAdmin = User::updateOrCreate(
                ['email' => 'editor@newsportal.dev'],
                [
                    'portal_id' => $portal->id,
                    'name'      => 'Chief Editor',
                    'username'  => 'chief_editor',
                    'password'  => Hash::make('Editor@12345'),
                    'status'    => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $portalAdmin->assignRole('chief_editor');

            // Sample reporter
            $reporter = User::updateOrCreate(
                ['email' => 'reporter@newsportal.dev'],
                [
                    'portal_id'     => $portal->id,
                    'name'          => 'Sample Reporter',
                    'username'      => 'reporter1',
                    'password'      => Hash::make('Reporter@12345'),
                    'reporter_type' => 'staff',
                    'status'        => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $reporter->assignRole('reporter');
        }

        $this->command->info('');
        $this->command->info('✅ Users seeded:');
        $this->command->info('   Super Admin  → admin@newsportal.dev / Admin@12345');
        $this->command->info('   Chief Editor → editor@newsportal.dev / Editor@12345');
        $this->command->info('   Reporter     → reporter@newsportal.dev / Reporter@12345');
        $this->command->info('');
        $this->command->warn('⚠️  Change passwords immediately in production!');
    }
}
