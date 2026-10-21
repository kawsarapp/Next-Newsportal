<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Dashboard Routes — Inertia.js + Vue 3
| All routes require auth + portal access
|--------------------------------------------------------------------------
*/

// ── Root Redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard.overview')
        : redirect()->route('login');
});

// ── Auth Routes ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('login',           [Dashboard\Auth\LoginController::class, 'show'])->name('login');
    Route::post('login',          [Dashboard\Auth\LoginController::class, 'store']);
    Route::get('login/2fa',       [Dashboard\Auth\TwoFactorController::class, 'show'])->name('2fa');
    Route::post('login/2fa',      [Dashboard\Auth\TwoFactorController::class, 'verify']);
});

Route::post('logout', [Dashboard\Auth\LoginController::class, 'destroy'])
     ->middleware('auth')
     ->name('logout');

// ── Dashboard (authenticated) ─────────────────────────────────────────────────
Route::prefix('dashboard')
    ->middleware(['auth', 'audit'])
    ->name('dashboard.')
    ->group(function () {

        // M-01 Overview / Command Center
        Route::get('/', [Dashboard\OverviewController::class, 'index'])->name('overview');
        Route::get('/stats', [Dashboard\OverviewController::class, 'stats'])->name('stats');
        Route::get('/realtime', [Dashboard\OverviewController::class, 'realtime'])->name('realtime');

        // M-02 News & Content
        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/',              [Dashboard\PostController::class, 'index'])->name('index');
            Route::get('/create',        [Dashboard\PostController::class, 'create'])->name('create');
            Route::post('/',             [Dashboard\PostController::class, 'store'])->name('store');
            Route::get('/{post}/edit',   [Dashboard\PostController::class, 'edit'])->name('edit');
            Route::put('/{post}',        [Dashboard\PostController::class, 'update'])->name('update');
            Route::delete('/{post}',     [Dashboard\PostController::class, 'destroy'])->name('destroy');
            Route::post('/{post}/restore',[Dashboard\PostController::class, 'restore'])->name('restore');

            // Status changes
            Route::post('/{post}/submit',   [Dashboard\PostController::class, 'submitForReview'])->name('submit');
            Route::post('/{post}/approve',  [Dashboard\PostController::class, 'approve'])->name('approve');
            Route::post('/{post}/publish',  [Dashboard\PostController::class, 'publish'])->name('publish');
            Route::post('/{post}/unpublish',[Dashboard\PostController::class, 'unpublish'])->name('unpublish');
            Route::post('/{post}/return',   [Dashboard\PostController::class, 'returnForRevision'])->name('return');
            Route::post('/{post}/schedule', [Dashboard\PostController::class, 'schedule'])->name('schedule');
            Route::post('/{post}/breaking', [Dashboard\PostController::class, 'toggleBreaking'])->name('breaking');

            // Revision history
            Route::get('/{post}/revisions', [Dashboard\PostController::class, 'revisions'])->name('revisions');

            // Bulk actions
            Route::post('/bulk',  [Dashboard\PostController::class, 'bulk'])->name('bulk');
        });

        // M-03 Editorial Workflow
        Route::prefix('editorial')->name('editorial.')->group(function () {
            Route::get('/',            [Dashboard\EditorialController::class, 'index'])->name('index');
            Route::get('/my-stories',  [Dashboard\EditorialController::class, 'myStories'])->name('my-stories');
            Route::get('/review-queue',[Dashboard\EditorialController::class, 'reviewQueue'])->name('review-queue');
            Route::get('/fact-check',  [Dashboard\EditorialController::class, 'factCheck'])->name('fact-check');
        });

        // M-04 Categories & Structure
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/',           [Dashboard\CategoryController::class, 'index'])->name('index');
            Route::get('/create',     [Dashboard\CategoryController::class, 'create'])->name('create');
            Route::post('/',          [Dashboard\CategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [Dashboard\CategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [Dashboard\CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [Dashboard\CategoryController::class, 'destroy'])->name('destroy');
            Route::post('/reorder',   [Dashboard\CategoryController::class, 'reorder'])->name('reorder');
        });

        Route::resource('tags',     Dashboard\TagController::class)->except(['show']);
        Route::resource('menus',    Dashboard\MenuController::class)->except(['show']);
        Route::post('menus/{menu}/items', [Dashboard\MenuController::class, 'saveItems'])->name('menus.items');

        // M-05 Reporter Management
        Route::prefix('reporters')->name('reporters.')->group(function () {
            Route::get('/',              [Dashboard\ReporterController::class, 'index'])->name('index');
            Route::get('/payments',      [\Modules\ReporterBilling\Controllers\BillingController::class, 'index'])->name('payments');
            Route::get('/{reporter}',    [Dashboard\ReporterController::class, 'show'])->whereNumber('reporter')->name('show');
            Route::put('/{reporter}',    [Dashboard\ReporterController::class, 'update'])->whereNumber('reporter')->name('update');
            Route::get('/{reporter}/id-card', [Dashboard\ReporterController::class, 'idCard'])->whereNumber('reporter')->name('id-card');
        });
        Route::resource('assignments', Dashboard\AssignmentController::class)->except(['show']);

        // M-06 Media Library
        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/',        [Dashboard\MediaController::class, 'index'])->name('index');
            Route::post('/upload', [Dashboard\MediaController::class, 'upload'])->name('upload')->middleware('throttle:media_upload');
            Route::delete('/{media}', [Dashboard\MediaController::class, 'destroy'])->name('destroy');
            Route::post('/{media}/update-meta', [Dashboard\MediaController::class, 'updateMeta'])->name('update-meta');
        });

        // M-07 SEO
        Route::prefix('seo')->name('seo.')->group(function () {
            Route::get('/',          [Dashboard\SeoController::class, 'index'])->name('index');
            Route::get('/sitemap',   [Dashboard\SeoController::class, 'sitemap'])->name('sitemap');
            Route::get('/redirects', [Dashboard\SeoController::class, 'redirects'])->name('redirects');
            Route::post('/redirects',[Dashboard\SeoController::class, 'storeRedirect'])->name('redirects.store');
            Route::get('/schema',    [Dashboard\SeoController::class, 'schema'])->name('schema');
        });

        // M-08 Social Media
        Route::prefix('social')->name('social.')->group(function () {
            Route::get('/',           [Dashboard\SocialController::class, 'index'])->name('index');
            Route::get('/accounts',   [Dashboard\SocialController::class, 'accounts'])->name('accounts');
            Route::post('/accounts',  [Dashboard\SocialController::class, 'storeAccount'])->name('accounts.store');
            Route::delete('/accounts/{account}', [Dashboard\SocialController::class, 'destroyAccount'])->name('accounts.destroy');
            Route::get('/queue',      [Dashboard\SocialController::class, 'queue'])->name('queue');
            Route::post('/publish',   [Dashboard\SocialController::class, 'publish'])->name('publish');
        });

        // M-08-B Social Photocard & Auto-Publisher
        Route::prefix('social-publisher')->name('social-publisher.')->group(function () {
            Route::get('/',                   [Dashboard\SocialPublisherController::class, 'index'])->name('index');
            Route::post('/publish',           [Dashboard\SocialPublisherController::class, 'publish'])->name('publish');
            Route::post('/re-share/{post}',   [Dashboard\SocialPublisherController::class, 'reShare'])->name('re-share');
            Route::post('/facebook-settings', [Dashboard\SocialPublisherController::class, 'saveFacebookSettings'])->name('facebook-settings');
            Route::post('/test-facebook',     [Dashboard\SocialPublisherController::class, 'testFacebookConnection'])->name('test-facebook');
        });

        // Subeditor24 Integration
        Route::prefix('subeditor')->name('subeditor.')->group(function () {
            Route::get('/', [Dashboard\SubeditorIntegrationController::class, 'index'])->name('index');
            Route::post('/settings', [Dashboard\SubeditorIntegrationController::class, 'updateSettings'])->name('settings.update');
            Route::post('/generate-token', [Dashboard\SubeditorIntegrationController::class, 'generateToken'])->name('token.generate');
            Route::delete('/logs', [Dashboard\SubeditorIntegrationController::class, 'clearLogs'])->name('logs.clear');
        });

        // M-09 AI Newsroom
        Route::prefix('ai')->name('ai.')->group(function () {
            Route::get('/',                    [Dashboard\AiController::class, 'index'])->name('index');
            Route::post('/headlines',          [Dashboard\AiController::class, 'headlines'])->name('headlines')->middleware('throttle:ai_requests');
            Route::post('/summary',            [Dashboard\AiController::class, 'summary'])->name('summary')->middleware('throttle:ai_requests');
            Route::post('/social-caption',     [Dashboard\AiController::class, 'socialCaption'])->name('social-caption')->middleware('throttle:ai_requests');
            Route::post('/grammar-check',      [Dashboard\AiController::class, 'grammarCheck'])->name('grammar-check')->middleware('throttle:ai_requests');
            Route::post('/translate',          [Dashboard\AiController::class, 'translate'])->name('translate')->middleware('throttle:ai_requests');
            Route::post('/seo-suggestions',    [Dashboard\AiController::class, 'seoSuggestions'])->name('seo-suggestions')->middleware('throttle:ai_requests');
            Route::get('/usage',               [Dashboard\AiController::class, 'usage'])->name('usage');
            Route::get('/settings',            [Dashboard\AiController::class, 'settings'])->name('settings');
            Route::put('/settings',            [Dashboard\AiController::class, 'updateSettings'])->name('settings.update');
        });

        // M-10 Analytics
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/',          [Dashboard\AnalyticsController::class, 'index'])->name('index');
            Route::get('/realtime',  [Dashboard\AnalyticsController::class, 'realtime'])->name('realtime');
            Route::get('/reports',   [Dashboard\AnalyticsController::class, 'reports'])->name('reports');
            Route::get('/export',    [Dashboard\AnalyticsController::class, 'export'])->name('export');
        });

        // M-11 Advertisement
        Route::prefix('ads')->name('ads.')->group(function () {
            Route::get('/',              [Dashboard\AdsController::class, 'index'])->name('index');
            Route::resource('slots',     Dashboard\AdSlotController::class)->except(['show']);
            Route::resource('campaigns', Dashboard\AdCampaignController::class)->except(['show']);
            Route::resource('advertisers', Dashboard\AdvertiserController::class)->except(['show']);
        });

        // M-12 Audience & Engagement
        Route::prefix('audience')->name('audience.')->group(function () {
            Route::get('/',          [Dashboard\AudienceController::class, 'index'])->name('index');
            Route::get('/comments',  [Dashboard\AudienceController::class, 'comments'])->name('comments');
            Route::post('/comments/{comment}/approve', [Dashboard\AudienceController::class, 'approveComment'])->name('comments.approve');
            Route::post('/comments/{comment}/spam',    [Dashboard\AudienceController::class, 'spamComment'])->name('comments.spam');
            Route::get('/subscribers',[Dashboard\AudienceController::class, 'subscribers'])->name('subscribers');
            Route::get('/push',      [Dashboard\AudienceController::class, 'pushNotifications'])->name('push');
            Route::post('/push',     [Dashboard\AudienceController::class, 'sendPush'])->name('push.send');
        });

        // M-13 Website & Frontend Management
        Route::prefix('frontend')->name('frontend.')->group(function () {
            Route::get('/',              [Dashboard\FrontendController::class, 'index'])->name('index');
            Route::get('/homepage',      [Dashboard\FrontendController::class, 'homepage'])->name('homepage');
            Route::post('/sections',     [Dashboard\FrontendController::class, 'storeSection'])->name('sections.store');
            Route::put('/sections/{section}', [Dashboard\FrontendController::class, 'updateSection'])->name('sections.update');
            Route::post('/sections/reorder', [Dashboard\FrontendController::class, 'reorderSections'])->name('sections.reorder');
            Route::patch('/sections/{section}/toggle', [Dashboard\FrontendController::class, 'toggleSection'])->name('sections.toggle');
            Route::delete('/sections/{section}', [Dashboard\FrontendController::class, 'destroySection'])->name('sections.destroy');
            Route::post('/sidebar',      [Dashboard\FrontendController::class, 'updateSidebar'])->name('sidebar.update');
            Route::post('/header-footer',[Dashboard\FrontendController::class, 'updateHeaderFooter'])->name('header-footer.update');
            Route::post('/youtube',      [Dashboard\FrontendController::class, 'updateYouTube'])->name('youtube.update');
            Route::get('/appearance',    [Dashboard\FrontendController::class, 'appearance'])->name('appearance');
            Route::post('/appearance',   [Dashboard\FrontendController::class, 'saveAppearance'])->name('appearance.save');
            Route::resource('pages',     Dashboard\StaticPageController::class)->except(['show']);
            Route::get('/feature-flags', [Dashboard\FrontendController::class, 'featureFlags'])->name('feature-flags');
            Route::post('/feature-flags',[Dashboard\FrontendController::class, 'saveFeatureFlags'])->name('feature-flags.save');
        });

        // M-14 Users & Roles
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',           [Dashboard\UserController::class, 'index'])->name('index');
            Route::get('/invite',     [Dashboard\UserController::class, 'invite'])->name('invite');
            Route::post('/invite',    [Dashboard\UserController::class, 'sendInvite'])->name('invite.send');
            Route::get('/{user}',     [Dashboard\UserController::class, 'show'])->name('show');
            Route::put('/{user}',     [Dashboard\UserController::class, 'update'])->name('update');
            Route::delete('/{user}',  [Dashboard\UserController::class, 'destroy'])->name('destroy');
            Route::get('/{user}/sessions', [Dashboard\UserController::class, 'sessions'])->name('sessions');
            Route::delete('/{user}/sessions/{session}', [Dashboard\UserController::class, 'killSession'])->name('sessions.kill');
        });
        Route::resource('roles', Dashboard\RoleController::class)->except(['show']);

        // M-15 System Settings
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/',           [Dashboard\SettingController::class, 'index'])->name('index');
            Route::put('/general',    [Dashboard\SettingController::class, 'updateGeneral'])->name('general');
            Route::put('/branding',   [Dashboard\SettingController::class, 'updateBranding'])->name('branding');
            Route::put('/email',      [Dashboard\SettingController::class, 'updateEmail'])->name('email');
            Route::put('/seo',        [Dashboard\SettingController::class, 'updateSeo'])->name('seo');
            Route::put('/integrations',[Dashboard\SettingController::class, 'updateIntegrations'])->name('integrations');
            Route::post('/test-email',[Dashboard\SettingController::class, 'testEmail'])->name('test-email');
            Route::get('/api-key',    [Dashboard\SettingController::class, 'apiKey'])->name('api-key');
            Route::post('/api-key/rotate', [Dashboard\SettingController::class, 'rotateApiKey'])->name('api-key.rotate');
            Route::resource('webhooks', Dashboard\WebhookController::class)->except(['show']);
            Route::get('/health',     [Dashboard\SettingController::class, 'health'])->name('health');
            Route::get('/jobs',       [Dashboard\SettingController::class, 'jobs'])->name('jobs');
        });

        // Module & Plugin Manager
        Route::prefix('modules')->name('modules.')->group(function () {
            Route::get('/',           [Dashboard\ModuleController::class, 'index'])->name('index');
            Route::post('/upload',    [Dashboard\ModuleController::class, 'upload'])->name('upload');
            Route::post('/{slug}/toggle', [Dashboard\ModuleController::class, 'toggle'])->name('toggle');
            Route::delete('/{slug}',  [Dashboard\ModuleController::class, 'destroy'])->name('destroy');
        });


        // M-16 Security & Audit
        Route::prefix('audit')->name('audit.')->group(function () {
            Route::get('/',           [Dashboard\AuditController::class, 'index'])->name('index');
            Route::get('/security',   [Dashboard\AuditController::class, 'security'])->name('security');
            Route::get('/logins',     [Dashboard\AuditController::class, 'logins'])->name('logins');
            Route::get('/export',     [Dashboard\AuditController::class, 'export'])->name('export');
        });

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/',     [Dashboard\ProfileController::class, 'index'])->name('index');
            Route::match(['put', 'post'], '/', [Dashboard\ProfileController::class, 'update'])->name('update');
            Route::put('/password', [Dashboard\ProfileController::class, 'updatePassword'])->name('password');
            Route::post('/2fa/enable',  [Dashboard\ProfileController::class, 'enable2fa'])->name('2fa.enable');
            Route::post('/2fa/disable', [Dashboard\ProfileController::class, 'disable2fa'])->name('2fa.disable');
            Route::post('/2fa/confirm', [Dashboard\ProfileController::class, 'confirm2fa'])->name('2fa.confirm');
        });

        // Super Admin only — Portal Management
        Route::prefix('portals')->name('portals.')->middleware('role:super_admin')->group(function () {
            Route::get('/',           [Dashboard\PortalController::class, 'index'])->name('index');
            Route::get('/create',     [Dashboard\PortalController::class, 'create'])->name('create');
            Route::post('/',          [Dashboard\PortalController::class, 'store'])->name('store');
            Route::get('/{portal}',   [Dashboard\PortalController::class, 'show'])->name('show');
            Route::put('/{portal}',   [Dashboard\PortalController::class, 'update'])->name('update');
            Route::post('/{portal}/suspend', [Dashboard\PortalController::class, 'suspend'])->name('suspend');
        });
    });

// ── Subeditor24 Root Aliases ──────────────────────────────────────────────
Route::post('/external-news-post', [App\Http\Controllers\Api\V1\SubeditorApiController::class, 'news']);
Route::get('/get-categories', [App\Http\Controllers\Api\V1\SubeditorApiController::class, 'categories']);
Route::post('/subeditor/news', [App\Http\Controllers\Api\V1\SubeditorApiController::class, 'news']);
Route::get('/subeditor/categories', [App\Http\Controllers\Api\V1\SubeditorApiController::class, 'categories']);

// ── Catch-all — send to Inertia (SPA) ────────────────────────────────────────
Route::get('/{any}', function () {
    return auth()->check()
        ? Inertia::render('Dashboard/ComingSoon', ['title' => 'Page Not Found'])
        : redirect()->route('login');
})->where('any', '^(?!api|horizon).*$')->name('fallback');
