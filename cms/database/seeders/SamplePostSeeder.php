<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Portal;
use App\Models\User;
use App\Models\Category;
use App\Models\Post;

class SamplePostSeeder extends Seeder
{
    public function run(): void
    {
        $portal = Portal::first();
        $user = User::first();
        $categories = Category::all();

        if (!$portal || !$user || $categories->isEmpty()) {
            return;
        }

        $sampleNews = [
            [
                'title' => 'দেশজুড়ে ডিজিটাল রূপান্তরের নতুন দিগন্ত উন্মোচিত',
                'slug' => 'digital-transformation-bangladesh',
                'excerpt' => 'তথ্যপ্রযুক্তি খাতের বৈপ্লবিক অগ্রগতিতে নাগরিক সেবা এখন হাতের মুঠোয় পৌঁছে যাচ্ছে।',
                'body' => '<p>বাংলাদেশে ডিজিটাল সেবা খাতে এসেছে যুগান্তকারী পরিবর্তন। প্রযুক্তিগত সুবিধার ফলে সাধারণ মানুষের দৈনন্দিন কার্যক্রম হয়েছে সহজ ও গতিশীল। সরকার স্মার্ট বাংলাদেশ বিনির্মাণে একের পর এক উদ্ভাবনী উদ্যোগ গ্রহণ করছে।</p>',
                'category_id' => $categories->where('slug', 'technology')->first()?->id ?? $categories->first()->id,
                'status' => 'published',
                'type' => 'article',
                'is_breaking' => true,
                'is_featured' => true,
                'view_count' => 1420,
            ],
            [
                'title' => 'জাতীয় ক্রিকেট দলের অসাধারণ জয়ে উৎসবের আমেজ',
                'slug' => 'bangladesh-cricket-historic-win',
                'excerpt' => 'শেষ ওভারের টানটান উত্তেজনায় অবিস্মরণীয় জয় ছিনিয়ে নিল টাইগাররা।',
                'body' => '<p>রোমাঞ্চকর এক লড়াইয়ে সফরকারী দলের বিরুদ্ধে ঐতিহাসিক জয় পেল বাংলাদেশ। ব্যাট এবং বল হাতে দুর্দান্ত পারফর্ম করে ম্যাচ সেরা নির্বাচিত হয়েছেন তরুণ অলরাউন্ডার।</p>',
                'category_id' => $categories->where('slug', 'sports')->first()?->id ?? $categories->first()->id,
                'status' => 'published',
                'type' => 'article',
                'is_breaking' => false,
                'is_featured' => true,
                'view_count' => 3890,
            ],
            [
                'title' => 'রপ্তানি আয়ে ইতিবাচক প্রবৃদ্ধি: বৈদেশিক মুদ্রার রিজার্ভে স্বস্তি',
                'slug' => 'export-revenue-growth-reserve-relief',
                'excerpt' => 'চলতি অর্থবছরে তৈরি পোশাক ও চামড়াজাত পণ্যের রপ্তানি আয় উল্লেখযোগ্য হারে বৃদ্ধি পেয়েছে।',
                'body' => '<p>আন্তর্জাতিক বাজারে বাংলাদেশি পণ্যের চাহিদা বৃদ্ধি পাওয়ায় দেশের অর্থনীতিতে তৈরি হয়েছে নতুন আশা। বাংলাদেশ ব্যাংকের সর্বশেষ প্রতিবেদন অনুযায়ী রিজার্ভ পরিস্থিতির উন্নতি পরিলক্ষিত হয়েছে।</p>',
                'category_id' => $categories->where('slug', 'economy')->first()?->id ?? $categories->first()->id,
                'status' => 'published',
                'type' => 'article',
                'is_breaking' => false,
                'is_featured' => false,
                'view_count' => 840,
            ],
            [
                'title' => 'উচ্চশিক্ষা ও উদ্ভাবনী গবেষণায় নতুন বৃত্তির ঘোষণা',
                'slug' => 'higher-education-research-fellowship-announced',
                'excerpt' => 'মেধাবী শিক্ষার্থীদের জন্য সরকারি বিশেষ ফান্ড থেকে বিশেষ শিক্ষা বৃত্তির সুযোগ তৈরি হয়েছে।',
                'body' => '<p>বিজ্ঞান ও তথ্যপ্রযুক্তি গবেষণায় তরুণদের আগ্রহী করতে এ বছর বিপুলসংখ্যক শিক্ষার্থীকে পূর্ণাঙ্গ স্কলারশিপ প্রদান করা হবে।</p>',
                'category_id' => $categories->where('slug', 'education')->first()?->id ?? $categories->first()->id,
                'status' => 'pending_review',
                'type' => 'article',
                'is_breaking' => false,
                'is_featured' => false,
                'view_count' => 120,
            ],
        ];

        foreach ($sampleNews as $data) {
            Post::create(array_merge($data, [
                'portal_id' => $portal->id,
                'author_id' => $user->id,
                'published_at' => now(),
            ]));
        }

        $this->command->info('✅ Sample posts seeded successfully.');
    }
}
