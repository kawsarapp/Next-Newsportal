<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Portal;
use Illuminate\Database\Seeder;

class FullCategoryHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $portals = Portal::all();
        if ($portals->isEmpty()) {
            $portals = collect([new Portal(['id' => 1])]);
        }

        foreach ($portals as $portal) {
            $this->seedHierarchyForPortal($portal->id);
        }
    }

    private function seedHierarchyForPortal(int $portalId): void
    {
        // ── Root Categories Definition ──────────────────────────────────────────
        $structure = [
            [
                'name' => 'জাতীয়',
                'name_en' => 'National',
                'slug' => 'national',
                'order' => 1,
                'color' => '#dc2626',
            ],
            [
                'name' => 'রাজনীতি',
                'name_en' => 'Politics',
                'slug' => 'politics',
                'order' => 2,
                'color' => '#2563eb',
            ],
            [
                'name' => 'সারা দেশ',
                'name_en' => 'Saradesh',
                'slug' => 'saradesh',
                'order' => 3,
                'color' => '#059669',
                'children' => [],
            ],
            [
                'name' => 'বিশ্ব',
                'name_en' => 'World',
                'slug' => 'international',
                'order' => 4,
                'color' => '#0284c7',
                'children' => [
                    ['name' => 'এশিয়া', 'name_en' => 'Asia', 'slug' => 'asia'],
                    ['name' => 'মধ্যপ্রাচ্য', 'name_en' => 'Middle East', 'slug' => 'middle-east'],
                    ['name' => 'ইউরোপ', 'name_en' => 'Europe', 'slug' => 'europe'],
                    ['name' => 'আমেরিকা', 'name_en' => 'America', 'slug' => 'america'],
                    ['name' => 'আফ্রিকা', 'name_en' => 'Africa', 'slug' => 'africa'],
                    ['name' => 'অন্যান্য', 'name_en' => 'World Others', 'slug' => 'world-others'],
                ],
            ],
            [
                'name' => 'খেলা',
                'name_en' => 'Sports',
                'slug' => 'sports',
                'order' => 5,
                'color' => '#16a34a',
                'children' => [
                    ['name' => 'ক্রিকেট', 'name_en' => 'Cricket', 'slug' => 'cricket'],
                    ['name' => 'ফুটবল', 'name_en' => 'Football', 'slug' => 'football'],
                    ['name' => 'অন্যান্য', 'name_en' => 'Sports Others', 'slug' => 'sports-others'],
                ],
            ],
            [
                'name' => 'বিনোদন',
                'name_en' => 'Entertainment',
                'slug' => 'entertainment',
                'order' => 6,
                'color' => '#d946ef',
                'children' => [
                    ['name' => 'ঢালিউড', 'name_en' => 'Dhallywood', 'slug' => 'dhallywood'],
                    ['name' => 'বলিউড', 'name_en' => 'Bollywood', 'slug' => 'bollywood'],
                    ['name' => 'হলিউড', 'name_en' => 'Hollywood', 'slug' => 'hollywood'],
                    ['name' => 'ওটিটি', 'name_en' => 'OTT', 'slug' => 'ott'],
                    ['name' => 'টেলিভিশন', 'name_en' => 'Television', 'slug' => 'television'],
                    ['name' => 'সঙ্গীত', 'name_en' => 'Music', 'slug' => 'music'],
                ],
            ],
            [
                'name' => 'বাণিজ্য',
                'name_en' => 'Business',
                'slug' => 'economy',
                'order' => 7,
                'color' => '#ea580c',
                'children' => [
                    ['name' => 'ব্যাংক ও বিমা', 'name_en' => 'Bank & Insurance', 'slug' => 'bank-insurance'],
                    ['name' => 'পুঁজিবাজার', 'name_en' => 'Share Market', 'slug' => 'share-market'],
                    ['name' => 'শিল্প-বাণিজ্য', 'name_en' => 'Trade & Commerce', 'slug' => 'trade-commerce'],
                    ['name' => 'বাজেট', 'name_en' => 'Budget', 'slug' => 'budget'],
                ],
            ],
            [
                'name' => 'মতামত',
                'name_en' => 'Opinion',
                'slug' => 'opinion',
                'order' => 8,
                'color' => '#6366f1',
            ],
            [
                'name' => 'লাইফস্টাইল',
                'name_en' => 'Lifestyle',
                'slug' => 'lifestyle',
                'order' => 9,
                'color' => '#ec4899',
                'children' => [
                    ['name' => 'ফ্যাশন', 'name_en' => 'Fashion', 'slug' => 'fashion'],
                    ['name' => 'রূপচর্চা', 'name_en' => 'Beauty', 'slug' => 'beauty'],
                    ['name' => 'রেসিপি ও খাদ্য', 'name_en' => 'Recipe & Food', 'slug' => 'recipe'],
                    ['name' => 'ভ্রমণ', 'name_en' => 'Travel', 'slug' => 'travel'],
                    ['name' => 'সম্পর্ক', 'name_en' => 'Relationship', 'slug' => 'relationship'],
                ],
            ],
            [
                'name' => 'আইন ও আদালত',
                'name_en' => 'Law & Court',
                'slug' => 'law-and-court',
                'order' => 10,
                'color' => '#475569',
            ],
            [
                'name' => 'প্রযুক্তি',
                'name_en' => 'Technology',
                'slug' => 'technology',
                'order' => 11,
                'color' => '#0891b2',
                'children' => [
                    ['name' => 'গ্যাজেট', 'name_en' => 'Gadgets', 'slug' => 'gadget'],
                    ['name' => 'কৃত্রিম বুদ্ধিমত্তা', 'name_en' => 'AI', 'slug' => 'artificial-intelligence'],
                    ['name' => 'সাইবার নিরাপত্তা', 'name_en' => 'Cyber Security', 'slug' => 'cyber-security'],
                    ['name' => 'উদ্ভাবন', 'name_en' => 'Innovation', 'slug' => 'innovation'],
                ],
            ],
            [
                'name' => 'ভিডিও',
                'name_en' => 'Video',
                'slug' => 'video',
                'order' => 12,
                'color' => '#e11d48',
            ],
            [
                'name' => 'অন্যান্য',
                'name_en' => 'Others',
                'slug' => 'others',
                'order' => 13,
                'color' => '#64748b',
                'children' => [
                    ['name' => 'শিক্ষা', 'name_en' => 'Education', 'slug' => 'education'],
                    ['name' => 'ধর্ম', 'name_en' => 'Religion', 'slug' => 'religion'],
                    ['name' => 'স্বাস্থ্য', 'name_en' => 'Health', 'slug' => 'health'],
                    ['name' => 'কৃষি', 'name_en' => 'Agriculture', 'slug' => 'agriculture'],
                    ['name' => 'প্রেস রিলিজ', 'name_en' => 'Press Release', 'slug' => 'press-release'],
                    ['name' => 'স্টার বিশেষ', 'name_en' => 'Star Special', 'slug' => 'star-special'],
                    ['name' => 'আবহাওয়া', 'name_en' => 'Weather', 'slug' => 'weather'],
                    ['name' => 'চাকরি', 'name_en' => 'Jobs', 'slug' => 'jobs'],
                    ['name' => 'প্রবাস', 'name_en' => 'Expatriate', 'slug' => 'probash'],
                    ['name' => 'ক্যাম্পাস', 'name_en' => 'Campus', 'slug' => 'campus'],
                ],
            ],
        ];

        // Process insertion / updating recursively
        $this->saveLevel($structure, $portalId, null, 0);

        // Ensure leftover unassigned root categories are not displayed in main root navbar
        $allowedRootSlugs = [
            'national', 'politics', 'saradesh', 'international', 'sports',
            'entertainment', 'economy', 'opinion', 'lifestyle', 'law-and-court',
            'technology', 'video', 'others'
        ];

        Category::where('portal_id', $portalId)
            ->whereNull('parent_id')
            ->whereNotIn('slug', $allowedRootSlugs)
            ->update(['show_in_nav' => false]);
    }

    private function saveLevel(array $items, int $portalId, ?int $parentId, int $depth): void
    {
        foreach ($items as $index => $item) {
            $slug = $item['slug'];

            // Check if exists (including trashed)
            $cat = Category::withTrashed()
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
