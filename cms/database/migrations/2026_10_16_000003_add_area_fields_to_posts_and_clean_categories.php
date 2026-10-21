<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure divisions and districts have bn_name column
        if (Schema::hasTable('divisions') && !Schema::hasColumn('divisions', 'bn_name')) {
            Schema::table('divisions', function (Blueprint $table) {
                $table->string('bn_name')->nullable()->after('name');
            });
            DB::table('divisions')->update(['bn_name' => DB::raw('name')]);
        }

        if (Schema::hasTable('districts') && !Schema::hasColumn('districts', 'bn_name')) {
            Schema::table('districts', function (Blueprint $table) {
                $table->string('bn_name')->nullable()->after('name');
            });
            DB::table('districts')->update(['bn_name' => DB::raw('name')]);
        }

        // 2. Add division_id and district_id to posts table
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'division_id')) {
                $table->foreignId('division_id')->nullable()->after('category_id')->constrained('divisions')->nullOnDelete();
            }
            if (!Schema::hasColumn('posts', 'district_id')) {
                $table->foreignId('district_id')->nullable()->after('division_id')->constrained('districts')->nullOnDelete();
            }
        });

        // 3. Migrate any existing posts linked to division/district categories before deleting them
        $saradeshCategories = DB::table('categories')->where('slug', 'saradesh')->get();
        foreach ($saradeshCategories as $saradesh) {
            $divisionCats = DB::table('categories')->where('parent_id', $saradesh->id)->get();
            foreach ($divisionCats as $divCat) {
                // Find matching division in divisions table
                $divisionRecord = DB::table('divisions')
                    ->where('name', $divCat->name)
                    ->orWhere('name_en', $divCat->name_en)
                    ->orWhere('id', (int) $divCat->order)
                    ->first();

                $divId = $divisionRecord?->id;

                // Check posts directly referencing this division category
                if ($divId) {
                    DB::table('posts')
                        ->where('category_id', $divCat->id)
                        ->update([
                            'division_id' => $divId,
                            'category_id' => $saradesh->id,
                        ]);
                }

                // District categories under this division
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
                    }
                }
            }
        }

        // 4. Clean up categories table: Delete all 72 division & district categories per portal
        $saradeshIds = DB::table('categories')->where('slug', 'saradesh')->pluck('id');
        if ($saradeshIds->isNotEmpty()) {
            $divisionCatIds = DB::table('categories')->whereIn('parent_id', $saradeshIds)->pluck('id');
            if ($divisionCatIds->isNotEmpty()) {
                $districtCatIds = DB::table('categories')->whereIn('parent_id', $divisionCatIds)->pluck('id');
                if ($districtCatIds->isNotEmpty()) {
                    // Delete category_closures and category_post references if any
                    if (Schema::hasTable('category_post')) {
                        DB::table('category_post')->whereIn('category_id', $districtCatIds)->delete();
                    }
                    DB::table('category_closures')->whereIn('descendant_id', $districtCatIds)->orWhereIn('ancestor_id', $districtCatIds)->delete();
                    DB::table('categories')->whereIn('id', $districtCatIds)->delete();
                }

                if (Schema::hasTable('category_post')) {
                    DB::table('category_post')->whereIn('category_id', $divisionCatIds)->delete();
                }
                DB::table('category_closures')->whereIn('descendant_id', $divisionCatIds)->orWhereIn('ancestor_id', $divisionCatIds)->delete();
                DB::table('categories')->whereIn('id', $divisionCatIds)->delete();
            }
        }
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'district_id')) {
                $table->dropForeign(['district_id']);
                $table->dropColumn('district_id');
            }
            if (Schema::hasColumn('posts', 'division_id')) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            }
        });
    }
};
