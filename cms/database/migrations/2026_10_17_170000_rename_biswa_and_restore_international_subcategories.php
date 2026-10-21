<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Rename 'বিশ্ব' to 'আন্তর্জাতিক' for both portals ──────────
        DB::table('categories')
            ->whereIn('slug', ['international', 'biswa', 'antarjatik'])
            ->whereNull('parent_id')
            ->update([
                'name'    => 'আন্তর্জাতিক',
                'name_en' => 'International',
                'slug'    => 'international',
            ]);

        // Specifically ensure category id 2 and 207 are named 'আন্তর্জাতিক'
        DB::table('categories')
            ->whereIn('id', [2, 207])
            ->update([
                'name'    => 'আন্তর্জাতিক',
                'name_en' => 'International',
                'slug'    => 'international',
                'parent_id' => null,
                'depth'     => 0,
            ]);

        // ── 2. Re-create / Restore International subcategories ─────────────
        $intlSubcategories = [
            ['name' => 'মধ্যপ্রাচ্য', 'name_en' => 'Middle East', 'slug' => 'modhyaprachyo', 'order' => 1],
            ['name' => 'যুক্তরাষ্ট্র', 'name_en' => 'United States', 'slug' => 'juktorastro', 'order' => 2],
            ['name' => 'এশিয়া', 'name_en' => 'Asia', 'slug' => 'asia', 'order' => 3],
            ['name' => 'ইউরোপ', 'name_en' => 'Europe', 'slug' => 'europ', 'order' => 4],
        ];

        $portals = DB::table('portals')->pluck('id');
        if ($portals->isEmpty()) {
            $portals = collect([1, 2]);
        }

        foreach ($portals as $portalId) {
            $portalId = (int) $portalId;

            // Find root international category for this portal
            $intlCategory = DB::table('categories')
                ->where('portal_id', $portalId)
                ->whereNull('parent_id')
                ->where(function ($q) {
                    $q->where('slug', 'international')
                      ->orWhere('name', 'আন্তর্জাতিক')
                      ->orWhere('name', 'বিশ্ব');
                })
                ->first();

            if (!$intlCategory) {
                continue;
            }

            foreach ($intlSubcategories as $sub) {
                $existing = DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->where('slug', $sub['slug'])
                    ->first();

                if ($existing) {
                    DB::table('categories')
                        ->where('id', $existing->id)
                        ->update([
                            'portal_id'   => $portalId,
                            'parent_id'   => $intlCategory->id,
                            'depth'       => 1,
                            'name'        => $sub['name'],
                            'name_en'     => $sub['name_en'],
                            'order'       => $sub['order'],
                            'type'        => 'section',
                            'is_active'   => true,
                            'show_in_nav' => true,
                            'deleted_at'  => null,
                        ]);
                } else {
                    DB::table('categories')->insert([
                        'portal_id'   => $portalId,
                        'parent_id'   => $intlCategory->id,
                        'depth'       => 1,
                        'name'        => $sub['name'],
                        'name_en'     => $sub['name_en'],
                        'slug'        => $sub['slug'],
                        'order'       => $sub['order'],
                        'type'        => 'section',
                        'is_active'   => true,
                        'show_in_nav' => true,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }

            // Ensure sports subcategories under 'খেলা'
            $sports = DB::table('categories')
                ->where('portal_id', $portalId)
                ->whereNull('parent_id')
                ->where('slug', 'sports')
                ->first();

            if ($sports) {
                DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->whereIn('slug', ['cricket', 'football'])
                    ->update([
                        'parent_id'   => $sports->id,
                        'depth'       => 1,
                        'is_active'   => true,
                        'show_in_nav' => true,
                    ]);
            }

            // Ensure other subcategories under 'অন্যান্য'
            $others = DB::table('categories')
                ->where('portal_id', $portalId)
                ->whereNull('parent_id')
                ->whereIn('slug', ['others', 'ananyo'])
                ->first();

            if ($others) {
                $othersSubSlugs = [
                    'shikkha', 'dhormo', 'swasthyo', 'krishi', 'press-release',
                    'ster-bisesh', 'abhawa', 'chakri', 'probas', 'campus',
                ];

                DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->whereIn('slug', $othersSubSlugs)
                    ->update([
                        'parent_id'   => $others->id,
                        'depth'       => 1,
                        'is_active'   => true,
                        'show_in_nav' => true,
                    ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('categories')
            ->whereIn('slug', ['international'])
            ->whereNull('parent_id')
            ->update([
                'name'    => 'বিশ্ব',
                'name_en' => 'World',
            ]);

        DB::table('categories')
            ->whereIn('slug', ['modhyaprachyo', 'juktorastro', 'asia', 'europ'])
            ->delete();
    }
};
