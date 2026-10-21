<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Re-map any posts currently linked to division or district categories before deletion
        $saradeshCategories = DB::table('categories')->where('slug', 'saradesh')->get();

        foreach ($saradeshCategories as $saradesh) {
            $divisionCats = DB::table('categories')->where('parent_id', $saradesh->id)->get();

            foreach ($divisionCats as $divCat) {
                // Find matching division in divisions table
                $cleanName = trim(str_replace('বিভাগ', '', $divCat->name));
                $divisionRecord = DB::table('divisions')
                    ->where('name', $divCat->name)
                    ->orWhere('name', 'LIKE', '%' . $cleanName . '%')
                    ->orWhere('name_en', $divCat->name_en)
                    ->first();

                // Check districts under this division category
                $districtCats = DB::table('categories')->where('parent_id', $divCat->id)->get();
                foreach ($districtCats as $distCat) {
                    $districtRecord = DB::table('districts')
                        ->where('name', $distCat->name)
                        ->orWhere('name_en', $distCat->name_en)
                        ->first();

                    if ($districtRecord) {
                        DB::table('posts')
                            ->where('category_id', $distCat->id)
                            ->update([
                                'division_id' => $districtRecord->division_id,
                                'district_id' => $districtRecord->id,
                                'category_id' => $saradesh->id,
                            ]);
                    } else if ($divisionRecord) {
                        DB::table('posts')
                            ->where('category_id', $distCat->id)
                            ->update([
                                'division_id' => $divisionRecord->id,
                                'category_id' => $saradesh->id,
                            ]);
                    }
                }

                // Posts directly referencing this division category
                if ($divisionRecord) {
                    DB::table('posts')
                        ->where('category_id', $divCat->id)
                        ->update([
                            'division_id' => $divisionRecord->id,
                            'category_id' => $saradesh->id,
                        ]);
                }
            }
        }

        // 2. Delete division and district category records from categories table
        $saradeshIds = DB::table('categories')->where('slug', 'saradesh')->pluck('id');
        if ($saradeshIds->isNotEmpty()) {
            $divisionCatIds = DB::table('categories')->whereIn('parent_id', $saradeshIds)->pluck('id');
            if ($divisionCatIds->isNotEmpty()) {
                $districtCatIds = DB::table('categories')->whereIn('parent_id', $divisionCatIds)->pluck('id');
                if ($districtCatIds->isNotEmpty()) {
                    if (Schema::hasTable('category_post')) {
                        DB::table('category_post')->whereIn('category_id', $districtCatIds)->delete();
                    }
                    if (Schema::hasTable('category_closures')) {
                        DB::table('category_closures')->whereIn('descendant_id', $districtCatIds)->orWhereIn('ancestor_id', $districtCatIds)->delete();
                    }
                    DB::table('categories')->whereIn('id', $districtCatIds)->delete();
                }

                if (Schema::hasTable('category_post')) {
                    DB::table('category_post')->whereIn('category_id', $divisionCatIds)->delete();
                }
                if (Schema::hasTable('category_closures')) {
                    DB::table('category_closures')->whereIn('descendant_id', $divisionCatIds)->orWhereIn('ancestor_id', $divisionCatIds)->delete();
                }
                DB::table('categories')->whereIn('id', $divisionCatIds)->delete();
            }
        }

        // 3. Set standard order and depth for all root categories
        $rootOrderMap = [
            'national'      => 1,
            'politics'      => 2,
            'saradesh'      => 3,
            'international' => 4,
            'sports'        => 5,
            'entertainment' => 6,
            'economy'       => 7,
            'opinion'       => 8,
            'lifestyle'     => 9,
            'law-and-court' => 10,
            'technology'    => 11,
            'video'         => 12,
            'others'        => 13,
        ];

        foreach ($rootOrderMap as $slug => $order) {
            DB::table('categories')
                ->where('slug', $slug)
                ->update([
                    'order'     => $order,
                    'parent_id' => null,
                    'depth'     => 0,
                ]);
        }

        // 4. Ensure all subcategories of root categories have depth = 1
        $rootCatIds = DB::table('categories')
            ->whereIn('slug', array_keys($rootOrderMap))
            ->pluck('id');

        DB::table('categories')
            ->whereIn('parent_id', $rootCatIds)
            ->update([
                'depth' => 1,
            ]);
    }

    public function down(): void
    {
        // Irreversible safe cleanup
    }
};
