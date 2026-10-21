<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Portal 1 Specific IDs ───────────────────────────────────────
        // Main categories:
        $p1MainIds = [1, 2, 3, 4, 5, 6, 7, 10, 11, 13, 14, 124, 125];
        DB::table('categories')
            ->whereIn('id', $p1MainIds)
            ->update([
                'parent_id' => null,
                'depth'     => 0,
            ]);

        // Under খেলা (id: 5): ক্রিকেট (102), ফুটবল (103)
        DB::table('categories')
            ->whereIn('id', [102, 103])
            ->update([
                'parent_id' => 5,
                'depth'     => 1,
            ]);

        // Under অন্যান্য (id: 125):
        $p1OthersSubIds = [255, 256, 257, 258, 259, 260, 261, 262, 263, 264];
        DB::table('categories')
            ->whereIn('id', $p1OthersSubIds)
            ->update([
                'parent_id' => 125,
                'depth'     => 1,
            ]);

        // ── 2. Portal 2 Specific IDs ───────────────────────────────────────
        // Main categories:
        $p2MainIds = [132, 133, 134, 207, 214, 218, 225, 230, 231, 237, 238, 243, 244];
        DB::table('categories')
            ->whereIn('id', $p2MainIds)
            ->update([
                'parent_id' => null,
                'depth'     => 0,
            ]);

        // Under খেলা (id: 214): ক্রিকেট (215), ফুটবল (216)
        DB::table('categories')
            ->whereIn('id', [215, 216])
            ->update([
                'parent_id' => 214,
                'depth'     => 1,
            ]);

        // Under অন্যান্য (id: 244):
        $p2OthersSubIds = [269, 270, 271, 272, 273, 274, 275, 276, 277, 278];
        DB::table('categories')
            ->whereIn('id', $p2OthersSubIds)
            ->update([
                'parent_id' => 244,
                'depth'     => 1,
            ]);

        // ── 3. General Fallback by Slug across all portals ──────────────────
        $portals = DB::table('portals')->pluck('id');
        if ($portals->isEmpty()) {
            $portals = collect([1, 2]);
        }

        foreach ($portals as $portalId) {
            $sports = DB::table('categories')
                ->where('portal_id', $portalId)
                ->where('slug', 'sports')
                ->first();

            if ($sports) {
                DB::table('categories')
                    ->where('id', $sports->id)
                    ->update(['parent_id' => null, 'depth' => 0]);

                DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->whereIn('slug', ['cricket', 'football'])
                    ->update([
                        'parent_id' => $sports->id,
                        'depth'     => 1,
                    ]);
            }

            $others = DB::table('categories')
                ->where('portal_id', $portalId)
                ->whereIn('slug', ['others', 'ananyo'])
                ->first();

            if ($others) {
                DB::table('categories')
                    ->where('id', $others->id)
                    ->update(['parent_id' => null, 'depth' => 0]);

                $othersSubSlugs = [
                    'shikkha', 'dhormo', 'swasthyo', 'krishi', 'press-release',
                    'ster-bisesh', 'abhawa', 'chakri', 'probas', 'campus',
                ];

                DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->whereIn('slug', $othersSubSlugs)
                    ->update([
                        'parent_id' => $others->id,
                        'depth'     => 1,
                    ]);
            }

            // Ensure saradesh and international are root
            DB::table('categories')
                ->where('portal_id', $portalId)
                ->whereIn('slug', ['saradesh', 'international', 'biswa'])
                ->update([
                    'parent_id' => null,
                    'depth'     => 0,
                ]);
        }
    }

    public function down(): void
    {
        // Revert subcategories depth/parent_id if needed
        DB::table('categories')
            ->whereIn('slug', ['cricket', 'football', 'shikkha', 'dhormo', 'swasthyo', 'krishi', 'press-release', 'ster-bisesh', 'abhawa', 'chakri', 'probas', 'campus'])
            ->update([
                'parent_id' => null,
                'depth'     => 0,
            ]);
    }
};
