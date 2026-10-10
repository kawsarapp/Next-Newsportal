<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | CMS Identity
    |--------------------------------------------------------------------------
    */
    'name'    => env('CMS_NAME', 'Newsportal CMS'),
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'api' => [
        'version'        => 'v1',
        'prefix'         => 'api',
        'key_prefix'     => 'pk_live_',
        'key_length'     => 40,
        'grace_period_days' => 30, // old key stays valid after rotation
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting (requests per minute per portal/IP)
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'public_api'     => (int) env('RL_PUBLIC_API', 100),
        'search'         => (int) env('RL_SEARCH', 20),
        'dashboard'      => (int) env('RL_DASHBOARD', 500),
        'media_upload'   => (int) env('RL_MEDIA_UPLOAD', 10),
        'ai_requests'    => (int) env('RL_AI', 30),
        'auth_attempts'  => (int) env('RL_AUTH', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (seconds) — per endpoint type
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'posts_list'     => (int) env('CACHE_TTL_POSTS', 300),      // 5 min
        'post_detail'    => (int) env('CACHE_TTL_POST', 3600),      // 1 hr
        'categories'     => (int) env('CACHE_TTL_CATEGORIES', 3600),
        'settings'       => (int) env('CACHE_TTL_SETTINGS', 86400), // 24 hr
        'design_tokens'  => (int) env('CACHE_TTL_TOKENS', 86400),
        'menus'          => (int) env('CACHE_TTL_MENUS', 86400),
        'breaking_news'  => (int) env('CACHE_TTL_BREAKING', 60),    // 1 min
        'dashboard_stats'=> (int) env('CACHE_TTL_STATS', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    */
    'media' => [
        'disk'           => env('MEDIA_DISK', 'public'),
        'max_size_mb'    => (int) env('MEDIA_MAX_MB', 10),
        'allowed_mimes'  => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
        'allowed_video'  => ['video/mp4', 'video/webm'],
        'strip_exif'     => true,

        'variants' => [
            'lg'    => ['width' => 1200, 'height' => null,  'quality' => 85],
            'md'    => ['width' => 800,  'height' => null,  'quality' => 82],
            'sm'    => ['width' => 400,  'height' => null,  'quality' => 80],
            'thumb' => ['width' => 150,  'height' => 150,   'quality' => 75, 'fit' => 'crop'],
        ],

        'convert_to_webp' => true,
        'webp_quality'    => 85,
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules — global defaults (overridable per portal in DB)
    |--------------------------------------------------------------------------
    */
    'modules' => [
        'breaking_news'   => true,
        'featured_news'   => true,
        'editorial_flow'  => true,
        'live_blog'       => false,
        'video_gallery'   => true,
        'photo_gallery'   => true,
        'podcast'         => false,
        'e_paper'         => false,
        'polls'           => true,
        'comments'        => true,
        'newsletter'      => true,
        'push_notify'     => false,
        'ai_newsroom'     => true,
        'social_publish'  => true,
        'advertisement'   => true,
        'seo_tools'       => true,
        'reporter_mgmt'   => true,
        'payment_records' => false,
        'analytics'       => true,
        'audience'        => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Editorial Workflow Stages
    | Order matters — stages progress in this sequence
    |--------------------------------------------------------------------------
    */
    'workflow_stages' => [
        'draft',
        'pending_review',
        'fact_check',     // optional — enable per portal
        'copy_edit',      // optional
        'legal_review',   // optional
        'approved',
        'published',
    ],

    'workflow_optional_stages' => ['fact_check', 'copy_edit', 'legal_review'],

    /*
    |--------------------------------------------------------------------------
    | AI Configuration
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'default_provider' => env('AI_PROVIDER', 'openai'), // openai|gemini|local
        'require_human_review' => env('AI_REQUIRE_HUMAN_REVIEW', true),

        'openai' => [
            'model'       => env('OPENAI_MODEL', 'gpt-4o'),
            'max_tokens'  => (int) env('OPENAI_MAX_TOKENS', 2000),
        ],

        'gemini' => [
            'model'       => env('GEMINI_MODEL', 'gemini-1.5-pro'),
            'max_tokens'  => (int) env('GEMINI_MAX_TOKENS', 2000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */
    'security' => [
        'max_login_attempts'    => (int) env('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_minutes'       => (int) env('LOCKOUT_MINUTES', 30),
        'session_timeout_hours' => (int) env('SESSION_TIMEOUT', 8),
        'require_2fa_for'       => ['super_admin', 'portal_admin', 'chief_editor'],
        'password_min_length'   => 10,
        'force_https'           => env('FORCE_HTTPS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporter Types
    |--------------------------------------------------------------------------
    */
    'reporter_types' => [
        'staff'        => 'Staff Journalist',
        'district'     => 'District Correspondent',
        'upazila'      => 'Upazila Correspondent',
        'university'   => 'University Correspondent',
        'bureau_chief' => 'Bureau Chief',
        'freelance'    => 'Freelance Contributor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Platforms
    |--------------------------------------------------------------------------
    */
    'social_platforms' => [
        'facebook'  => ['name' => 'Facebook',  'icon' => 'facebook',  'max_caption' => 63206],
        'instagram' => ['name' => 'Instagram', 'icon' => 'instagram', 'max_caption' => 2200],
        'x'         => ['name' => 'X (Twitter)', 'icon' => 'x',       'max_caption' => 280],
        'telegram'  => ['name' => 'Telegram', 'icon' => 'telegram',   'max_caption' => 4096],
        'youtube'   => ['name' => 'YouTube',  'icon' => 'youtube',    'max_caption' => 5000],
    ],

    /*
    |--------------------------------------------------------------------------
    | Portal Plans & Limits
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'free' => [
            'max_users'         => 3,
            'max_posts_per_day' => 10,
            'storage_gb'        => 1,
            'ai_requests_month' => 50,
        ],
        'basic' => [
            'max_users'         => 10,
            'max_posts_per_day' => 50,
            'storage_gb'        => 5,
            'ai_requests_month' => 500,
        ],
        'pro' => [
            'max_users'         => 50,
            'max_posts_per_day' => 200,
            'storage_gb'        => 20,
            'ai_requests_month' => 2000,
        ],
        'enterprise' => [
            'max_users'         => -1,   // unlimited
            'max_posts_per_day' => -1,
            'storage_gb'        => 100,
            'ai_requests_month' => -1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Events to track
    |--------------------------------------------------------------------------
    */
    'audit_events' => [
        'post.created', 'post.updated', 'post.published', 'post.unpublished',
        'post.deleted', 'post.restored', 'post.status_changed',
        'user.created', 'user.updated', 'user.deleted', 'user.role_changed',
        'portal.settings_updated', 'portal.api_key_rotated',
        'media.uploaded', 'media.deleted',
        'auth.login', 'auth.logout', 'auth.failed', 'auth.2fa_enabled',
    ],

];
