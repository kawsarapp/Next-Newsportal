<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Portal;
use App\Models\PortalModule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use ZipArchive;
use Exception;

class ModuleManager
{
    protected string $modulesPath;

    public function __construct()
    {
        $this->modulesPath = base_path('modules');
    }

    /**
     * Get all installed modules from the modules/ folder.
     */
    public function getInstalledModules(): array
    {
        if (!File::exists($this->modulesPath)) {
            return [];
        }

        $directories = File::directories($this->modulesPath);
        $modules = [];

        foreach ($directories as $dir) {
            $manifestPath = $dir . '/module.json';
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true) ?: [];
                $slug = basename($dir);

                $modules[$slug] = array_merge([
                    'slug'        => $slug,
                    'name'        => $slug,
                    'version'     => '1.0.0',
                    'description' => '',
                    'author'      => 'Newsportal Engine',
                    'icon'        => 'cube',
                    'enabled'     => false,
                    'installed'   => true,
                ], $manifest);
            }
        }

        return $modules;
    }

    /**
     * Get module status for a specific portal.
     */
    public function getModulesForPortal(int $portalId): array
    {
        $installed = $this->getInstalledModules();

        $activeSettings = PortalModule::where('portal_id', $portalId)
            ->pluck('enabled', 'module')
            ->toArray();

        foreach ($installed as $slug => &$data) {
            $data['enabled'] = (bool) ($activeSettings[$slug] ?? false);
        }

        return array_values($installed);
    }

    /**
     * Toggle a module for a portal.
     */
    public function toggleModule(int $portalId, string $moduleSlug, bool $enabled): void
    {
        PortalModule::updateOrCreate(
            ['portal_id' => $portalId, 'module' => $moduleSlug],
            ['enabled' => $enabled]
        );

        cache()->forget("portal:{$portalId}:module:{$moduleSlug}");
    }

    /**
     * Install a new module from uploaded ZIP file.
     */
    public function installFromZip(string $zipFilePath): array
    {
        if (!class_exists('ZipArchive')) {
            throw new Exception('PHP ZipArchive extension is required.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath) !== true) {
            throw new Exception('Invalid or corrupted ZIP archive.');
        }

        // Verify if module.json exists inside root or top subfolder
        $manifestFound = false;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (str_ends_with($filename, 'module.json')) {
                $manifestFound = true;
                break;
            }
        }

        if (!$manifestFound) {
            $zip->close();
            throw new Exception('Invalid module structure: module.json manifest was not found.');
        }

        // Extract to temporary directory first
        $tempPath = storage_path('app/temp_modules/' . uniqid('mod_'));
        File::ensureDirectoryExists($tempPath);
        $zip->extractTo($tempPath);
        $zip->close();

        // Locate module.json to determine target slug
        $manifestFile = null;
        if (File::exists($tempPath . '/module.json')) {
            $manifestFile = $tempPath . '/module.json';
            $sourceDir = $tempPath;
        } else {
            $subdirs = File::directories($tempPath);
            if (!empty($subdirs) && File::exists($subdirs[0] . '/module.json')) {
                $manifestFile = $subdirs[0] . '/module.json';
                $sourceDir = $subdirs[0];
            } else {
                File::deleteDirectory($tempPath);
                throw new Exception('Could not locate module manifest file.');
            }
        }

        $manifest = json_decode(File::get($manifestFile), true);
        $slug = $manifest['slug'] ?? basename($sourceDir);

        $targetDir = $this->modulesPath . '/' . $slug;

        // Move to modules directory
        if (File::exists($targetDir)) {
            File::deleteDirectory($targetDir);
        }
        File::copyDirectory($sourceDir, $targetDir);
        File::deleteDirectory($tempPath);

        // Run migrations if exists
        $migrationsPath = $targetDir . '/Database/Migrations';
        if (File::exists($migrationsPath)) {
            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'modules/' . $slug . '/Database/Migrations',
                '--force' => true,
            ]);
        }

        return $manifest ?: ['slug' => $slug];
    }

    /**
     * Delete an uninstalled module completely.
     */
    public function deleteModule(string $moduleSlug): void
    {
        $targetDir = $this->modulesPath . '/' . $moduleSlug;
        if (File::exists($targetDir)) {
            File::deleteDirectory($targetDir);
        }

        PortalModule::where('module', $moduleSlug)->delete();
    }

    /**
     * Auto-discover and register active modules (Routes, Migrations, etc.)
     */
    public function bootstrapModules(): void
    {
        if (!File::exists($this->modulesPath)) {
            return;
        }

        $directories = File::directories($this->modulesPath);

        foreach ($directories as $dir) {
            $slug = basename($dir);

            // Register web routes
            $webRoutes = $dir . '/Routes/web.php';
            if (File::exists($webRoutes)) {
                Route::middleware(['web', 'auth'])
                    ->prefix('dashboard/m/' . $slug)
                    ->name('module.' . $slug . '.')
                    ->group($webRoutes);
            }

            // Register api routes
            $apiRoutes = $dir . '/Routes/api.php';
            if (File::exists($apiRoutes)) {
                Route::middleware(['api', 'portal.scope'])
                    ->prefix('api/v1/m/' . $slug)
                    ->group($apiRoutes);
            }
        }
    }
}
