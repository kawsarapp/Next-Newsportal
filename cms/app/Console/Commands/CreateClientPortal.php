<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Portal;
use App\Models\User;
use App\Domain\Portal\ValueObjects\ApiKey;
use Illuminate\Support\Str;

class CreateClientPortal extends Command
{
    protected $signature = 'portal:create {name} {slug?}';
    protected $description = 'Create a new client portal with fresh API keys and configuration';

    public function handle(): int
    {
        $name = $this->argument('name');
        $slug = $this->argument('slug') ?: Str::slug($name);

        $this->info("Creating client portal: [{$name}] (slug: {$slug})...");

        // Generate high-entropy API key
        $key = ApiKey::generate();

        $portal = Portal::create([
            'name' => $name,
            'slug' => $slug,
            'tagline' => 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক',
            'api_key_prefix' => $key['prefix'],
            'api_key_hash' => $key['hash'],
            'status' => 'active',
            'plan' => 'pro',
            'language' => 'bn',
            'timezone' => 'Asia/Dhaka',
        ]);

        $this->newLine();
        $this->line("=================================================");
        $this->info("🎉 CLIENT PORTAL CREATED SUCCESSFULLY!");
        $this->line("=================================================");
        $this->line("<comment>Portal ID:</comment>      " . $portal->id);
        $this->line("<comment>Portal Name:</comment>    " . $portal->name);
        $this->line("<comment>Portal Slug:</comment>    " . $portal->slug);
        $this->line("<comment>API Key (RAW):</comment>  <info>" . $key['raw'] . "</info>");
        $this->line("=================================================");
        $this->newLine();
        $this->comment("Next Step for Frontend (.env):");
        $this->line("NUXT_PUBLIC_API_BASE=https://newsportal-cms.ddev.site/api/v1");
        $this->line("NUXT_PUBLIC_PORTAL_KEY=" . $key['raw']);
        $this->line("NUXT_PUBLIC_SITE_NAME=" . $portal->name);
        $this->newLine();

        return self::SUCCESS;
    }
}
