<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Portal;

return new class extends Migration
{
    public function up(): void
    {
        $portals = Portal::all();
        if ($portals->isEmpty()) {
            $portals = collect([new Portal(['id' => 1])]);
        }

        foreach ($portals as $portal) {
            $portalId = (int) $portal->id;

            // ── 1. Subcategories under 'অন্যান্য' (slug: others) ────────────────
            $others = Category::withoutGlobalScopes()
                ->where('portal_id', $portalId)
                ->whereIn('slug', ['others', 'ananyo'])
                ->first();

            if ($others) {
                $othersSubcategories = [
                    ['name' => 'শিক্ষা', 'name_en' => 'Shikkha', 'slug' => 'shikkha', 'order' => 1],
                    ['name' => 'ধর্ম', 'name_en' => 'Dhormo', 'slug' => 'dhormo', 'order' => 2],
                    ['name' => 'স্বাস্থ্য', 'name_en' => 'Swasthyo', 'slug' => 'swasthyo', 'order' => 3],
                    ['name' => 'কৃষি', 'name_en' => 'Krishi', 'slug' => 'krishi', 'order' => 4],
                    ['name' => 'প্রেস রিলিজ', 'name_en' => 'Press Release', 'slug' => 'press-release', 'order' => 5],
                    ['name' => 'স্টার বিশেষ', 'name_en' => 'Star Special', 'slug' => 'ster-bisesh', 'order' => 6],
                    ['name' => 'আবহাওয়া', 'name_en' => 'Abhawa', 'slug' => 'abhawa', 'order' => 7],
                    ['name' => 'চাকরি', 'name_en' => 'Chakri', 'slug' => 'chakri', 'order' => 8],
                    ['name' => 'প্রবাস', 'name_en' => 'Probas', 'slug' => 'probas', 'order' => 9],
                    ['name' => 'ক্যাম্পাস', 'name_en' => 'Campus', 'slug' => 'campus', 'order' => 10],
                ];

                foreach ($othersSubcategories as $sub) {
                    $existing = Category::withoutGlobalScopes()
                        ->withTrashed()
                        ->where('portal_id', $portalId)
                        ->where('slug', $sub['slug'])
                        ->first();

                    $data = [
                        'portal_id'   => $portalId,
                        'parent_id'   => $others->id,
                        'depth'       => 1,
                        'name'        => $sub['name'],
                        'name_en'     => $sub['name_en'],
                        'order'       => $sub['order'],
                        'type'        => 'section',
                        'is_active'   => true,
                        'show_in_nav' => true,
                    ];

                    if ($existing) {
                        if ($existing->trashed()) {
                            $existing->restore();
                        }
                        $existing->update($data);
                    } else {
                        $data['slug'] = $sub['slug'];
                        Category::withoutGlobalScopes()->create($data);
                    }
                }
            }

            // ── 2. Subcategories under 'বিশ্ব' (slug: international) ────────────
            $international = Category::withoutGlobalScopes()
                ->where('portal_id', $portalId)
                ->whereIn('slug', ['international', 'biswa'])
                ->first();

            if ($international) {
                $internationalSubcategories = [
                    ['name' => 'মধ্যপ্রাচ্য', 'name_en' => 'Middle East', 'slug' => 'modhyaprachyo', 'order' => 1],
                    ['name' => 'যুক্তরাষ্ট্র', 'name_en' => 'United States', 'slug' => 'juktorastro', 'order' => 2],
                    ['name' => 'এশিয়া', 'name_en' => 'Asia', 'slug' => 'asia', 'order' => 3],
                    ['name' => 'ইউরোপ', 'name_en' => 'Europe', 'slug' => 'europ', 'order' => 4],
                ];

                foreach ($internationalSubcategories as $sub) {
                    $existing = Category::withoutGlobalScopes()
                        ->withTrashed()
                        ->where('portal_id', $portalId)
                        ->where('slug', $sub['slug'])
                        ->first();

                    $data = [
                        'portal_id'   => $portalId,
                        'parent_id'   => $international->id,
                        'depth'       => 1,
                        'name'        => $sub['name'],
                        'name_en'     => $sub['name_en'],
                        'order'       => $sub['order'],
                        'type'        => 'section',
                        'is_active'   => true,
                        'show_in_nav' => true,
                    ];

                    if ($existing) {
                        if ($existing->trashed()) {
                            $existing->restore();
                        }
                        $existing->update($data);
                    } else {
                        $data['slug'] = $sub['slug'];
                        Category::withoutGlobalScopes()->create($data);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $slugs = [
            'shikkha', 'dhormo', 'swasthyo', 'krishi', 'press-release',
            'ster-bisesh', 'abhawa', 'chakri', 'probas', 'campus',
            'modhyaprachyo', 'juktorastro', 'asia', 'europ',
        ];

        Category::withoutGlobalScopes()
            ->whereIn('slug', $slugs)
            ->forceDelete();
    }
};
