<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class AdditionalCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $portalId = 1;

        $newCategories = [
            ['name' => 'সারাদেশ', 'name_en' => 'Saradesh', 'slug' => 'saradesh', 'order' => 10],
            ['name' => 'মতামত', 'name_en' => 'Opinion', 'slug' => 'opinion', 'order' => 11],
            ['name' => 'লাইফস্টাইল', 'name_en' => 'Lifestyle', 'slug' => 'lifestyle', 'order' => 12],
            ['name' => 'আইন ও আদালত', 'name_en' => 'Law & Court', 'slug' => 'law-and-court', 'order' => 13],
            ['name' => 'ধর্ম ও জীবন', 'name_en' => 'Religion', 'slug' => 'religion', 'order' => 14],
            ['name' => 'চাকরি ও ক্যারিয়ার', 'name_en' => 'Career & Jobs', 'slug' => 'career-and-jobs', 'order' => 15],
            ['name' => 'সাহিত্য ও সংস্কৃতি', 'name_en' => 'Literature & Culture', 'slug' => 'literature-culture', 'order' => 16],
            ['name' => 'পরিবেশ ও জলবায়ু', 'name_en' => 'Environment & Climate', 'slug' => 'environment-climate', 'order' => 17],
            ['name' => 'কৃষি ও প্রকৃতি', 'name_en' => 'Agriculture', 'slug' => 'agriculture', 'order' => 18],
            ['name' => 'প্রবাসী ও অভিবাসন', 'name_en' => 'Expatriate & Migration', 'slug' => 'expatriate', 'order' => 19],
            ['name' => 'বিজ্ঞান ও উদ্ভাবন', 'name_en' => 'Science & Innovation', 'slug' => 'science-innovation', 'order' => 20],
            ['name' => 'নারী ও শিশু', 'name_en' => 'Women & Children', 'slug' => 'women-children', 'order' => 21],
            ['name' => 'বিশেষ প্রতিবেদন', 'name_en' => 'Special Reports', 'slug' => 'special-reports', 'order' => 22],
        ];

        foreach ($newCategories as $cat) {
            $existing = Category::withTrashed()
                ->where('portal_id', $portalId)
                ->where('slug', $cat['slug'])
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->update([
                    'name' => $cat['name'],
                    'name_en' => $cat['name_en'],
                    'order' => $cat['order'],
                    'is_active' => true,
                    'show_in_nav' => true,
                ]);
            } else {
                Category::create([
                    'portal_id' => $portalId,
                    'name' => $cat['name'],
                    'name_en' => $cat['name_en'],
                    'slug' => $cat['slug'],
                    'order' => $cat['order'],
                    'is_active' => true,
                    'show_in_nav' => true,
                ]);
            }
        }
    }
}
