<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Portal;
use App\Domain\Portal\ValueObjects\ApiKey;

class PortalSeeder extends Seeder
{
    public function run(): void
    {
        // ── Default demo portal ────────────────────────────────────────────────
        $keyData = ApiKey::generate();

        $portal = Portal::firstOrCreate(
            ['slug' => 'demo-portal'],
            [
                'name'           => 'Demo News Portal',
                'domain'         => 'newsportal-cms.ddev.site',
                'api_key_prefix' => $keyData['prefix'],
                'api_key_hash'   => $keyData['hash'],
                'tagline'        => 'বাংলাদেশের সেরা নিউজ পোর্টাল',
                'language'       => 'bn',
                'timezone'       => 'Asia/Dhaka',
                'plan'           => 'pro',
                'status'         => 'active',
            ]
        );

        // ── Default settings ───────────────────────────────────────────────────
        $settings = [
            ['key' => 'site_name',           'value' => 'Demo News Portal',          'type' => 'string', 'group' => 'general'],
            ['key' => 'site_tagline',         'value' => 'বাংলাদেশের সেরা নিউজ পোর্টাল', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_email',           'value' => 'info@example.com',          'type' => 'string', 'group' => 'general'],
            ['key' => 'site_phone',           'value' => '+8801700000000',             'type' => 'string', 'group' => 'general'],
            ['key' => 'site_address',         'value' => 'ঢাকা, বাংলাদেশ',            'type' => 'string', 'group' => 'general'],
            ['key' => 'facebook_url',         'value' => 'https://facebook.com/',      'type' => 'string', 'group' => 'social'],
            ['key' => 'youtube_url',          'value' => 'https://youtube.com/',       'type' => 'string', 'group' => 'social'],
            ['key' => 'breaking_news_enabled','value' => 'true',                       'type' => 'boolean','group' => 'general'],
            ['key' => 'comments_enabled',     'value' => 'true',                       'type' => 'boolean','group' => 'general'],
            ['key' => 'default_meta_title',   'value' => 'Demo News Portal — সর্বশেষ সংবাদ', 'type' => 'string', 'group' => 'seo'],
            ['key' => 'default_meta_desc',    'value' => 'বাংলাদেশের সর্বশেষ সংবাদ পড়ুন।', 'type' => 'string', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            $portal->settings()->updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        // ── Default design tokens ─────────────────────────────────────────────
        $tokens = [
            ['token_key' => 'color-primary',       'token_value' => '#E63946', 'category' => 'color'],
            ['token_key' => 'color-secondary',      'token_value' => '#1D3557', 'category' => 'color'],
            ['token_key' => 'color-accent',         'token_value' => '#457B9D', 'category' => 'color'],
            ['token_key' => 'color-background',     'token_value' => '#FFFFFF', 'category' => 'color'],
            ['token_key' => 'color-text',           'token_value' => '#212529', 'category' => 'color'],
            ['token_key' => 'font-family-heading',  'token_value' => "'Hind Siliguri', sans-serif", 'category' => 'typography'],
            ['token_key' => 'font-family-body',     'token_value' => "'Noto Sans Bengali', sans-serif", 'category' => 'typography'],
            ['token_key' => 'font-size-base',       'token_value' => '16px',    'category' => 'typography'],
            ['token_key' => 'border-radius-card',   'token_value' => '8px',     'category' => 'spacing'],
            ['token_key' => 'header-height',        'token_value' => '64px',    'category' => 'layout'],
        ];

        foreach ($tokens as $token) {
            $portal->designTokens()->updateOrCreate(
                ['token_key' => $token['token_key']],
                $token
            );
        }

        // ── Enable all modules ─────────────────────────────────────────────────
        $modules = array_keys(config('cms.modules', []));
        foreach ($modules as $module) {
            $portal->modules()->updateOrCreate(
                ['module' => $module],
                ['enabled' => true]
            );
        }

        // ── Default categories ─────────────────────────────────────────────────
        $categories = [
            ['name' => 'জাতীয়',    'name_en' => 'National',       'slug' => 'national',       'order' => 1],
            ['name' => 'আন্তর্জাতিক','name_en' => 'International',  'slug' => 'international',  'order' => 2],
            ['name' => 'রাজনীতি',   'name_en' => 'Politics',        'slug' => 'politics',       'order' => 3],
            ['name' => 'অর্থনীতি',  'name_en' => 'Economy',         'slug' => 'economy',        'order' => 4],
            ['name' => 'খেলাধুলা',  'name_en' => 'Sports',          'slug' => 'sports',         'order' => 5],
            ['name' => 'বিনোদন',    'name_en' => 'Entertainment',   'slug' => 'entertainment',  'order' => 6],
            ['name' => 'প্রযুক্তি', 'name_en' => 'Technology',      'slug' => 'technology',     'order' => 7],
            ['name' => 'শিক্ষা',    'name_en' => 'Education',       'slug' => 'education',      'order' => 8],
            ['name' => 'স্বাস্থ্য', 'name_en' => 'Health',          'slug' => 'health',         'order' => 9],
            ['name' => 'মতামত',     'name_en' => 'Opinion',         'slug' => 'opinion',        'order' => 10],
        ];

        foreach ($categories as $cat) {
            $portal->categories()->updateOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['is_active' => true, 'show_in_nav' => true, 'type' => 'section'])
            );
        }

        $this->command->info("✅ Demo portal seeded.");
        $this->command->info("   API Key (save this!): {$keyData['raw']}");
        $this->command->info("   URL: https://newsportal-cms.ddev.site");
    }
}
