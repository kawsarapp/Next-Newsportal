<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Portal;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrictCategoryHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $portals = Portal::all();
        if ($portals->isEmpty()) {
            $portals = collect([new Portal(['id' => 1])]);
        }

        foreach ($portals as $portal) {
            $this->seedStrictHierarchyForPortal((int) $portal->id);
        }
    }

    private function seedStrictHierarchyForPortal(int $portalId): void
    {
        // ── 1. Define Strict Category Hierarchy ───────────────────────────────
        $structure = [
            [
                'name' => 'জাতীয়',
                'name_en' => 'National',
                'slug' => 'national',
                'order' => 1,
                'color' => '#dc2626',
                // STRICT: No subcategories
            ],
            [
                'name' => 'রাজনীতি',
                'name_en' => 'Politics',
                'slug' => 'politics',
                'order' => 2,
                'color' => '#2563eb',
                // STRICT: No subcategories
            ],
            [
                'name' => 'সারা দেশ',
                'name_en' => 'Saradesh',
                'slug' => 'saradesh',
                'order' => 3,
                'color' => '#059669',
                // Area (Divisions & Districts) separated to divisions and districts tables
                'children' => [],
            ],
            [
                'name' => 'বিশ্ব',
                'name_en' => 'World',
                'slug' => 'international',
                'order' => 4,
                'color' => '#0284c7',
                'children' => [
                    ['name' => 'মধ্যপ্রাচ্য', 'name_en' => 'Middle East', 'slug' => 'modhyaprachyo', 'order' => 1],
                    ['name' => 'যুক্তরাষ্ট্র', 'name_en' => 'United States', 'slug' => 'juktorastro', 'order' => 2],
                    ['name' => 'এশিয়া', 'name_en' => 'Asia', 'slug' => 'asia', 'order' => 3],
                    ['name' => 'ইউরোপ', 'name_en' => 'Europe', 'slug' => 'europ', 'order' => 4],
                ],
            ],
            [
                'name' => 'খেলা',
                'name_en' => 'Sports',
                'slug' => 'sports',
                'order' => 5,
                'color' => '#16a34a',
                // STRICT: EXACTLY 3 subcategories: cricket, football, sports-others
                'children' => [
                    ['name' => 'ক্রিকেট', 'name_en' => 'Cricket', 'slug' => 'cricket', 'order' => 1],
                    ['name' => 'ফুটবল', 'name_en' => 'Football', 'slug' => 'football', 'order' => 2],
                    ['name' => 'অন্যান্য', 'name_en' => 'Others', 'slug' => 'sports-others', 'order' => 3],
                ],
            ],
            [
                'name' => 'বিনোদন',
                'name_en' => 'Entertainment',
                'slug' => 'entertainment',
                'order' => 6,
                'color' => '#d946ef',
                // STRICT: No subcategories
            ],
            [
                'name' => 'বাণিজ্য',
                'name_en' => 'Business',
                'slug' => 'economy',
                'order' => 7,
                'color' => '#ea580c',
                // STRICT: No subcategories
            ],
            [
                'name' => 'মতামত',
                'name_en' => 'Opinion',
                'slug' => 'opinion',
                'order' => 8,
                'color' => '#6366f1',
                // STRICT: No subcategories
            ],
            [
                'name' => 'লাইফস্টাইল',
                'name_en' => 'Lifestyle',
                'slug' => 'lifestyle',
                'order' => 9,
                'color' => '#ec4899',
                // STRICT: No subcategories
            ],
            [
                'name' => 'আইন ও আদালত',
                'name_en' => 'Law & Court',
                'slug' => 'law-and-court',
                'order' => 10,
                'color' => '#475569',
                // STRICT: No subcategories
            ],
            [
                'name' => 'প্রযুক্তি',
                'name_en' => 'Technology',
                'slug' => 'technology',
                'order' => 11,
                'color' => '#0891b2',
                // STRICT: No subcategories
            ],
            [
                'name' => 'ভিডিও',
                'name_en' => 'Video',
                'slug' => 'video',
                'order' => 12,
                'color' => '#e11d48',
                // STRICT: No subcategories
            ],
            [
                'name' => 'অন্যান্য',
                'name_en' => 'Others',
                'slug' => 'others',
                'order' => 13,
                'color' => '#64748b',
                'children' => [
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
                ],
            ],
        ];

        // ── 2. Determine Allowed Slugs ────────────────────────────────────────
        $allowedRootSlugs = [
            'national', 'politics', 'saradesh', 'international', 'sports',
            'entertainment', 'economy', 'opinion', 'lifestyle', 'law-and-court',
            'technology', 'video', 'others'
        ];

        $allowedSportsSlugs = ['cricket', 'football', 'sports-others'];
        $allowedInternationalSlugs = ['modhyaprachyo', 'juktorastro', 'asia', 'europ'];
        $allowedOthersSlugs = [
            'shikkha', 'dhormo', 'swasthyo', 'krishi', 'press-release',
            'ster-bisesh', 'abhawa', 'chakri', 'probas', 'campus'
        ];

        $allowedDivisionSlugs = [
            'dhaka', 'chattogram', 'rajshahi', 'khulna',
            'barishal', 'sylhet', 'rangpur', 'mymensingh'
        ];

        $allowedDistrictSlugs = [];
        foreach ($structure[2]['children'] as $div) {
            foreach ($div['children'] as $dist) {
                $allowedDistrictSlugs[] = $dist['slug'];
            }
        }

        $allAllowedSlugs = array_merge(
            $allowedRootSlugs,
            $allowedDivisionSlugs,
            $allowedDistrictSlugs,
            $allowedSportsSlugs,
            $allowedInternationalSlugs,
            $allowedOthersSlugs
        );

        // ── 3. Force-Delete any categories outside strict allowed list ────────
        $fallbackOthers = Category::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->where('slug', 'others')
            ->first();

        $unwanted = Category::withoutGlobalScopes()
            ->withTrashed()
            ->where('portal_id', $portalId)
            ->whereNotIn('slug', $allAllowedSlugs)
            ->get();

        foreach ($unwanted as $u) {
            if ($fallbackOthers) {
                Post::withoutGlobalScopes()
                    ->where('category_id', $u->id)
                    ->update(['category_id' => $fallbackOthers->id]);
            }
            $u->forceDelete();
        }

        // Purge any trashed categories
        Category::withoutGlobalScopes()
            ->onlyTrashed()
            ->where('portal_id', $portalId)
            ->forceDelete();

        // ── 4. Upsert Strict Categories Hierarchy ─────────────────────────────
        $this->saveLevel($structure, $portalId, null, 0);

        // ── 5. Ensure root visibility & navigation rules ───────────────────────
        Category::withoutGlobalScopes()
            ->where('portal_id', $portalId)
            ->whereNull('parent_id')
            ->whereIn('slug', $allowedRootSlugs)
            ->update([
                'is_active' => true,
                'show_in_nav' => true,
            ]);
    }

    private function saveLevel(array $items, int $portalId, ?int $parentId, int $depth): void
    {
        foreach ($items as $index => $item) {
            $slug = $item['slug'];

            $cat = Category::withoutGlobalScopes()
                ->withTrashed()
                ->where('portal_id', $portalId)
                ->where('slug', $slug)
                ->first();

            $data = [
                'portal_id'   => $portalId,
                'parent_id'   => $parentId,
                'depth'       => $depth,
                'name'        => $item['name'],
                'name_en'     => $item['name_en'] ?? null,
                'order'       => $item['order'] ?? ($index + 1),
                'color'       => $item['color'] ?? null,
                'type'        => $depth === 2 ? 'district' : 'section',
                'is_active'   => true,
                'show_in_nav' => true,
            ];

            if ($cat) {
                if ($cat->trashed()) {
                    $cat->restore();
                }
                $cat->update($data);
            } else {
                $data['slug'] = $slug;
                $cat = Category::create($data);
            }

            if (!empty($item['children'])) {
                $this->saveLevel($item['children'], $portalId, $cat->id, $depth + 1);
            }
        }
    }
}
