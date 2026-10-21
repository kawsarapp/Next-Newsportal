<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        $portals = DB::table('portals')->pluck('id');
        if ($portals->isEmpty()) {
            $portals = collect([1, 2]);
        }

        $now = Carbon::now();

        foreach ($portals as $portalId) {
            $portalId = (int) $portalId;

            // Helper to get or create root category
            $getRootId = function (string $slug, string $name, int $order = 0) use ($portalId, $now): ?int {
                $cat = DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->whereNull('parent_id')
                    ->where(function ($q) use ($slug, $name) {
                        $q->where('slug', $slug)
                          ->orWhere('name', $name);
                    })
                    ->first();

                if ($cat) {
                    return (int) $cat->id;
                }

                return (int) DB::table('categories')->insertGetId([
                    'portal_id'   => $portalId,
                    'parent_id'   => null,
                    'depth'       => 0,
                    'name'        => $name,
                    'name_en'     => ucfirst($slug),
                    'slug'        => $slug,
                    'order'       => $order,
                    'is_active'   => 1,
                    'show_in_nav' => 1,
                    'type'        => 'section',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            };

            // Helper to insert or update subcategory
            $upsertSubcategory = function (
                string $name,
                string $nameEn,
                string $slug,
                int $parentId,
                int $depth,
                int $order,
                bool $showInNav = false
            ) use ($portalId, $now): int {
                $existing = DB::table('categories')
                    ->where('portal_id', $portalId)
                    ->where(function ($q) use ($slug, $name, $parentId) {
                        $q->where('slug', $slug)
                          ->orWhere(function ($q2) use ($name, $parentId) {
                              $q2->where('name', $name)->where('parent_id', $parentId);
                          });
                    })
                    ->first();

                if ($existing) {
                    DB::table('categories')
                        ->where('id', $existing->id)
                        ->update([
                            'name'        => $name,
                            'name_en'     => $nameEn,
                            'slug'        => $slug,
                            'parent_id'   => $parentId,
                            'depth'       => $depth,
                            'order'       => $order,
                            'is_active'   => 1,
                            'show_in_nav' => $showInNav ? 1 : 0,
                            'updated_at'  => $now,
                            'deleted_at'  => null,
                        ]);
                    return (int) $existing->id;
                }

                return (int) DB::table('categories')->insertGetId([
                    'portal_id'   => $portalId,
                    'parent_id'   => $parentId,
                    'depth'       => $depth,
                    'name'        => $name,
                    'name_en'     => $nameEn,
                    'slug'        => $slug,
                    'order'       => $order,
                    'is_active'   => 1,
                    'show_in_nav' => $showInNav ? 1 : 0,
                    'type'        => 'section',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            };

            // 1. সারা দেশ (Saradesh) - Root category only (Divisions & Districts are handled in dedicated area tables)
            $saradeshId = $getRootId('saradesh', 'সারা দেশ', 3);

            // 2. জাতীয় (National)
            $nationalId = $getRootId('national', 'জাতীয়', 1);
            if ($nationalId) {
                $nationalSubs = [
                    ['name' => 'রাজধানী', 'name_en' => 'Capital', 'slug' => 'rajdhani', 'order' => 1],
                    ['name' => 'সংসদ ও নির্বাচন', 'name_en' => 'Parliament & Election', 'slug' => 'sangsad-nirbachon', 'order' => 2],
                    ['name' => 'প্রশাসন', 'name_en' => 'Administration', 'slug' => 'proshashon', 'order' => 3],
                    ['name' => 'অপরাধ ও বিচার', 'name_en' => 'Crime & Justice', 'slug' => 'oporadh-bichar', 'order' => 4],
                    ['name' => 'মুক্তিযুদ্ধ ও ইতিহাস', 'name_en' => 'Liberation War & History', 'slug' => 'muktijuddho-itihas', 'order' => 5],
                ];
                foreach ($nationalSubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $nationalId, 1, $s['order'], true);
                }
            }

            // 3. রাজনীতি (Politics)
            $politicsId = $getRootId('politics', 'রাজনীতি', 2);
            if ($politicsId) {
                $politicsSubs = [
                    ['name' => 'আওয়ামী লীগ', 'name_en' => 'Awami League', 'slug' => 'awami-league', 'order' => 1],
                    ['name' => 'বিএনপি', 'name_en' => 'BNP', 'slug' => 'bnp', 'order' => 2],
                    ['name' => 'জামায়াতে ইসলামী', 'name_en' => 'Jamaat-e-Islami', 'slug' => 'jamaat-e-islami', 'order' => 3],
                    ['name' => 'অন্যান্য রাজনৈতিক দল', 'name_en' => 'Other Political Parties', 'slug' => 'other-political-parties', 'order' => 4],
                    ['name' => 'কূটনীতি ও ভূরাজনীতি', 'name_en' => 'Diplomacy & Geopolitics', 'slug' => 'kutniti-bhurajniti', 'order' => 5],
                ];
                foreach ($politicsSubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $politicsId, 1, $s['order'], true);
                }
            }

            // 4. বাণিজ্য (Economy)
            $economyId = $getRootId('economy', 'বাণিজ্য', 4);
            if ($economyId) {
                $economySubs = [
                    ['name' => 'ব্যাংক ও বীমা', 'name_en' => 'Bank & Insurance', 'slug' => 'bank-bima', 'order' => 1],
                    ['name' => 'পুঁজিবাজার', 'name_en' => 'Stock Market', 'slug' => 'punjibazar', 'order' => 2],
                    ['name' => 'শিল্প ও বাণিজ্য', 'name_en' => 'Trade & Commerce', 'slug' => 'shilpo-banijjo', 'order' => 3],
                    ['name' => 'অর্থনীতি ও বাজেট', 'name_en' => 'Economy & Budget', 'slug' => 'orthoniti-budget', 'order' => 4],
                    ['name' => 'রফতানি ও আমদানি', 'name_en' => 'Export & Import', 'slug' => 'roptani-amdani', 'order' => 5],
                ];
                foreach ($economySubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $economyId, 1, $s['order'], true);
                }
            }

            // 5. বিনোদন (Entertainment)
            $entertainmentId = $getRootId('entertainment', 'বিনোদন', 6);
            if ($entertainmentId) {
                $entertainmentSubs = [
                    ['name' => 'ঢালিউড', 'name_en' => 'Dhallywood', 'slug' => 'dhallywood', 'order' => 1],
                    ['name' => 'বলিউড', 'name_en' => 'Bollywood', 'slug' => 'bollywood', 'order' => 2],
                    ['name' => 'হলিউড', 'name_en' => 'Hollywood', 'slug' => 'hollywood', 'order' => 3],
                    ['name' => 'নাটক ও ওটিটি', 'name_en' => 'Drama & OTT', 'slug' => 'natok-ott', 'order' => 4],
                    ['name' => 'সংগীত', 'name_en' => 'Music', 'slug' => 'songit', 'order' => 5],
                ];
                foreach ($entertainmentSubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $entertainmentId, 1, $s['order'], true);
                }
            }

            // 6. প্রযুক্তি (Technology)
            $techId = $getRootId('technology', 'প্রযুক্তি', 7);
            if ($techId) {
                $techSubs = [
                    ['name' => 'স্মার্টফোন ও গ্যাজেট', 'name_en' => 'Smartphones & Gadgets', 'slug' => 'smartphone-gadget', 'order' => 1],
                    ['name' => 'কৃত্রিম বুদ্ধিমত্তা (AI)', 'name_en' => 'Artificial Intelligence', 'slug' => 'artificial-intelligence-ai', 'order' => 2],
                    ['name' => 'ইন্টারনেট ও টেলিকম', 'name_en' => 'Internet & Telecom', 'slug' => 'internet-telecom', 'order' => 3],
                    ['name' => 'তথ্যপ্রযুক্তি ও সাইবার', 'name_en' => 'IT & Cyber Security', 'slug' => 'it-cyber', 'order' => 4],
                    ['name' => 'বিজ্ঞান ও মহাকাশ', 'name_en' => 'Science & Space', 'slug' => 'bigyan-mohakash', 'order' => 5],
                ];
                foreach ($techSubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $techId, 1, $s['order'], true);
                }
            }

            // 7. লাইফস্টাইল (Lifestyle)
            $lifestyleId = $getRootId('lifestyle', 'লাইফস্টাইল', 13);
            if ($lifestyleId) {
                $lifestyleSubs = [
                    ['name' => 'ফ্যাশন ও রূপচর্চা', 'name_en' => 'Fashion & Beauty', 'slug' => 'fashion-rupchorcha', 'order' => 1],
                    ['name' => 'স্বাস্থ্য ও পুষ্টি', 'name_en' => 'Health & Nutrition', 'slug' => 'swasthyo-pushti', 'order' => 2],
                    ['name' => 'ভ্রমণ ও পর্যটন', 'name_en' => 'Travel & Tourism', 'slug' => 'bhromon-porjoton', 'order' => 3],
                    ['name' => 'রেসিপি ও খাবার', 'name_en' => 'Recipe & Food', 'slug' => 'recipe-khabar', 'order' => 4],
                ];
                foreach ($lifestyleSubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $lifestyleId, 1, $s['order'], true);
                }
            }

            // 8. আইন ও আদালত (Law and Court)
            $lawId = $getRootId('law-and-court', 'আইন ও আদালত', 14);
            if ($lawId) {
                $lawSubs = [
                    ['name' => 'সুপ্রিম কোর্ট', 'name_en' => 'Supreme Court', 'slug' => 'supreme-court', 'order' => 1],
                    ['name' => 'বিচার বিভাগ', 'name_en' => 'Judiciary', 'slug' => 'bichar-bibhag', 'order' => 2],
                    ['name' => 'মানবাধিকার', 'name_en' => 'Human Rights', 'slug' => 'manobadhikar', 'order' => 3],
                ];
                foreach ($lawSubs as $s) {
                    $upsertSubcategory($s['name'], $s['name_en'], $s['slug'], $lawId, 1, $s['order'], true);
                }
            }
        }
    }

    public function down(): void
    {
        // Safe down: do nothing to prevent unintended data loss of posts linked to subcategories
    }
};
