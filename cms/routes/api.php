<?php

use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\DesignTokenController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\AreaController;
use App\Http\Controllers\Api\V1\SubeditorApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes — v1
| All routes here are portal-scoped via middleware
|--------------------------------------------------------------------------
*/

Route::prefix('v1')
    ->middleware(['portal.scope', 'throttle:public_api'])
    ->group(function () {

        // ── Health check ─────────────────────────────────────────────────────
        Route::get('health', fn () => response()->json([
            'success' => true,
            'portal'  => app('current.portal')?->slug,
            'time'    => now()->toISOString(),
        ]));

        // ── Posts ─────────────────────────────────────────────────────────────
        Route::prefix('posts')->controller(PostController::class)->group(function () {
            Route::get('/',          'index');           // GET /api/v1/posts
            Route::get('/breaking',  'breaking');        // GET /api/v1/posts/breaking
            Route::get('/featured',  'featured');        // GET /api/v1/posts/featured
            Route::get('/{slug}',    'show')->name('posts.show');            // GET /api/v1/posts/{slug}
            Route::post('/{id}/view','recordView');      // POST /api/v1/posts/{id}/view
        });

        // ── Categories ────────────────────────────────────────────────────────
        Route::prefix('categories')->controller(CategoryController::class)->group(function () {
            Route::get('/',         'index');            // GET /api/v1/categories
            Route::get('/tree',     'tree');             // GET /api/v1/categories/tree
            Route::get('/{slug}',   'show');             // GET /api/v1/categories/{slug}
            Route::get('/{slug}/posts', 'posts');        // GET /api/v1/categories/{slug}/posts
        });

        // ── Tags ──────────────────────────────────────────────────────────────
        Route::prefix('tags')->controller(TagController::class)->group(function () {
            Route::get('/',             'index');
            Route::get('/{slug}/posts', 'posts');
        });

        // ── Areas / Divisions ──────────────────────────────────────────────────
        Route::get('areas', [AreaController::class, 'index']);
        Route::get('divisions', [AreaController::class, 'index']);

        // ── Menus ─────────────────────────────────────────────────────────────
        Route::get('menus/{location}', [MenuController::class, 'show']); // GET /api/v1/menus/header

        // ── Settings (public-safe settings only) ──────────────────────────────
        Route::get('settings', [SettingController::class, 'public']);

        // ── Design Tokens ─────────────────────────────────────────────────────
        Route::get('design-tokens', [DesignTokenController::class, 'index']);

        // ── Static Pages ──────────────────────────────────────────────────────
        Route::get('pages/{slug}', [PageController::class, 'show']);

        // ── Homepage Dynamic Sections & Sidebar & Header/Footer & YouTube ───
        Route::get('homepage-sections', [\App\Http\Controllers\Dashboard\FrontendController::class, 'publicSections']);
        Route::get('sidebar-config', [\App\Http\Controllers\Dashboard\FrontendController::class, 'publicSidebar']);
        Route::get('header-footer-config', [\App\Http\Controllers\Dashboard\FrontendController::class, 'publicHeaderFooter']);
        Route::get('youtube-config', [\App\Http\Controllers\Dashboard\FrontendController::class, 'publicYouTube']);

        // ── Search ────────────────────────────────────────────────────────────
        Route::get('search', [SearchController::class, 'index'])
             ->middleware('throttle:search');
    });

// ── Subeditor24 Integration Endpoints ─────────────────────────────────────
Route::prefix('v1/subeditor')->controller(SubeditorApiController::class)->group(function () {
    Route::post('news', 'news');
    Route::get('categories', 'categories');
});

// Root aliases for Subeditor24 standard defaults
Route::controller(SubeditorApiController::class)->group(function () {
    Route::post('external-news-post', 'news');
    Route::get('get-categories', 'categories');
    Route::post('subeditor/news', 'news');
    Route::get('subeditor/categories', 'categories');
});
