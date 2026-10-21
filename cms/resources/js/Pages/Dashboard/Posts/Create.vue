<template>
  <AppLayout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
      <!-- Breadcrumb & Header -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <Link :href="route('dashboard.posts.index')" class="hover:text-red-600 transition-colors">নিউজ</Link>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ isEdit ? 'সম্পাদনা' : 'নতুন সংবাদ লিখুন' }}</span>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'সংবাদ সম্পাদনা করুন' : 'নতুন সংবাদ তৈরি' }}</h2>
        </div>

        <div class="flex items-center gap-3">
          <Link
            :href="route('dashboard.posts.index')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
          >
            বাতিল
          </Link>
          <button
            type="button"
            @click="submit('draft')"
            :disabled="form.processing"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
          >
            <span v-if="form.processing && activeSubmitAction === 'draft'" class="w-3.5 h-3.5 border-2 border-gray-400 border-t-gray-800 rounded-full animate-spin"></span>
            খসড়া সংরক্ষণ
          </button>
          <button
            type="button"
            @click="submit('published')"
            :disabled="form.processing"
            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-2"
          >
            <span v-if="form.processing && activeSubmitAction === 'published'" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ isEdit ? 'আপডেট করুন' : 'সংবাদ প্রকাশ' }}
          </button>
        </div>
      </div>

      <!-- Form Grid -->
      <form @submit.prevent="submit('draft')" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Title & Slug -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">শিরোনাম <span class="text-red-500">*</span></label>
              <input
                v-model="form.title"
                type="text"
                placeholder="সংবাদের আকর্ষণীয় শিরোনাম লিখুন..."
                class="w-full text-lg px-4 py-2.5 border border-gray-200 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
              />
              <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">স্লাগ (URL)</label>
              <div class="flex items-center text-sm text-gray-500 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                <span class="text-gray-400 select-none">news/</span>
                <input
                  v-model="form.slug"
                  type="text"
                  placeholder="auto-generated-slug"
                  class="bg-transparent border-none p-0 focus:ring-0 text-sm text-gray-700 w-full ml-1"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">সারসংক্ষেপ (Excerpt)</label>
              <textarea
                v-model="form.excerpt"
                rows="2"
                placeholder="সংবাদের মূল সারসংক্ষেপ এক বা দুই বাক্যে..."
                class="w-full text-sm px-4 py-2 border border-gray-200 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
              ></textarea>
            </div>
          </div>

          <!-- Body / TipTap Rich Editor -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-2">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-semibold text-gray-700">সংবাদের মূল বিবরণ <span class="text-red-500">*</span></label>
              <span class="text-xs text-gray-400">Bangla & English Supported</span>
            </div>
            <RichTipTapEditor v-model="form.body" placeholder="এখানে বিস্তারিত সংবাদ তৈরি করুন..." />
            <p v-if="form.errors.body" class="text-red-500 text-xs mt-1">{{ form.errors.body }}</p>
          </div>

          <!-- SEO Meta Details -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
              <span>🔍</span> সার্চ ইঞ্জিন অপ্টিমাইজেশন (SEO)
            </h3>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
              <input
                v-model="form.seo_title"
                type="text"
                placeholder="গুগল সার্চে প্রদর্শিত শিরোনাম (ডিফল্ট মূল শিরোনাম)"
                class="w-full text-sm px-4 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
              <textarea
                v-model="form.seo_description"
                rows="2"
                placeholder="সার্চ রেজাল্টে প্রদর্শিত মেটা ডেসক্রিপশন..."
                class="w-full text-sm px-4 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Sidebar Options (Right 1 col) -->
        <div class="space-y-6">
          <!-- Publishing & Flags -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">সংবাদ বৈশিষ্ট্য ও ধরন</h3>

            <!-- প্রধান ক্যাটাগরি (Primary Category - Original 2 Dropdowns) -->
            <div id="category-selector-container" class="space-y-3 bg-gray-50/70 p-3.5 rounded-xl border border-gray-200/80">
              <div class="flex items-center justify-between">
                <div>
                  <label class="block text-sm font-semibold text-gray-800">
                    প্রধান ক্যাটাগরি <span class="text-red-500">*</span>
                  </label>
                  <span class="text-[11px] text-gray-400 font-medium">সংবাদের মূল বিভাগ নির্ধারণ করুন</span>
                </div>
                <button
                  type="button"
                  @click="openAddSubcategoryModal(selectedRootCategoryId)"
                  class="text-[11px] font-semibold text-red-600 hover:text-red-700 hover:underline flex items-center gap-0.5"
                >
                  <span>+</span> নতুন সাবক্যাটাগরি
                </button>
              </div>

              <!-- ১. প্রধান ক্যাটাগরি ড্রপডাউন -->
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                  প্রধান ক্যাটাগরি নির্বাচন করুন <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="selectedRootCategoryId"
                  @change="onRootCategoryChange"
                  class="w-full text-xs px-3 py-2.5 border border-gray-300 rounded-lg focus:border-red-500 outline-none bg-white font-medium text-gray-800 transition-colors shadow-2xs"
                >
                  <option value="">-- প্রধান ক্যাটাগরি নির্বাচন করুন --</option>
                  <option v-for="cat in rootCategories" :key="cat.id" :value="cat.id">
                    📁 {{ cat.name }}
                  </option>
                </select>
              </div>

              <!-- ২. সাব-ক্যাটাগরি ড্রপডাউন -->
              <div v-if="selectedRootCategoryId">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                  সাব-ক্যাটাগরি নির্বাচন করুন (ঐচ্ছিক)
                </label>
                <select
                  v-model="selectedSubcategoryId"
                  @change="onSubcategoryChange"
                  class="w-full text-xs px-3 py-2.5 border border-gray-300 rounded-lg focus:border-red-500 outline-none bg-white font-medium text-gray-800 transition-colors shadow-2xs"
                >
                  <option value="">-- মূল ক্যাটাগরিতে রাখুন --</option>
                  <option v-for="sub in currentSubcategories" :key="sub.id" :value="sub.id">
                    ↳ {{ sub.name }}
                  </option>
                </select>
              </div>

              <!-- ৩. গ্রিন চেকমার্ক ইন্ডিকেটর -->
              <div
                v-if="primaryCategoryInfo"
                class="flex items-center gap-2 p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 font-medium"
              >
                <span class="text-emerald-600 font-bold">✓</span>
                <span>
                  নির্বাচিত: <strong class="text-emerald-900">{{ primaryCategoryInfo.path }}</strong>
                  <span class="text-emerald-700 ml-1">({{ primaryCategoryInfo.label }})</span>
                </span>
              </div>

              <!-- Error Display -->
              <p v-if="form.errors.category_id" class="text-red-500 text-xs mt-1 font-semibold flex items-center gap-1">
                <span>⚠️</span>
                <span>{{ form.errors.category_id }}</span>
              </p>
            </div>

            <!-- মাল্টি-ক্যাটাগরি (ঐচ্ছিক) / অতিরিক্ত ক্যাটাগরি -->
            <div class="space-y-3 bg-gray-50/70 p-3.5 rounded-xl border border-gray-200/80">
              <div class="flex items-center justify-between">
                <label class="block text-sm font-semibold text-gray-800">
                  মাল্টি-ক্যাটাগরি (ঐচ্ছিক)
                </label>
                <span class="text-[11px] text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded-full">
                  অতিরিক্ত ক্যাটাগরি
                </span>
              </div>

              <p class="text-xs text-gray-500 leading-relaxed">
                একই সংবাদ একাধিক ক্যাটাগরিতে (যেমন: জাতীয় ও লাইফস্টাইল) একসাথে প্রকাশ করতে চাইলে এখান থেকে যুক্ত করুন:
              </p>

              <!-- Multi-category selector dropdown -->
              <select
                v-model="selectedSecondaryCatId"
                @change="addSecondaryCategory"
                class="w-full text-xs px-3 py-2 border border-gray-300 rounded-lg focus:border-red-500 outline-none bg-white font-medium text-gray-800 transition-colors shadow-2xs"
              >
                <option value="">+ অতিরিক্ত ক্যাটাগরি যুক্ত করুন</option>
                <optgroup
                  v-for="group in secondaryCategoryGroups"
                  :key="group.id"
                  :label="group.name"
                >
                  <option
                    v-if="group.isRootAvailable !== false"
                    :value="group.id"
                    class="font-bold text-gray-900"
                  >
                    {{ group.name }} (মূল ক্যাটাগরি)
                  </option>
                  <option
                    v-for="child in group.children"
                    :key="child.id"
                    :value="child.id"
                    class="text-gray-700 pl-3"
                  >
                    ↳ {{ child.name }}
                  </option>
                </optgroup>
              </select>

              <!-- Selected multi-category chips -->
              <div v-if="selectedSecondaryCategories.length > 0" class="flex flex-wrap gap-1.5 pt-1">
                <span
                  v-for="cat in selectedSecondaryCategories"
                  :key="cat.id"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-medium"
                >
                  <span>📂 {{ cat.parent_name ? `${cat.parent_name} › ${cat.name}` : cat.name }}</span>
                  <button
                    type="button"
                    @click="removeSecondaryCategory(cat.id)"
                    class="text-red-400 hover:text-red-700 font-bold focus:outline-none ml-0.5"
                    title="সরান"
                  >
                    ✕
                  </button>
                </span>
              </div>
            </div>

            <!-- হ্যাশট্যাগ / কি-ওয়ার্ড (Hashtags) -->
            <div class="space-y-2 bg-gray-50/70 p-3.5 rounded-xl border border-gray-200/80">
              <div class="flex items-center justify-between">
                <label class="block text-sm font-semibold text-gray-800 flex items-center gap-1">
                  <span>#️⃣ হ্যাশট্যাগ / ট্যাগ</span>
                </label>
                <span class="text-[11px] text-gray-400">Enter চেপে যুক্ত করুন</span>
              </div>

              <div class="relative">
                <div class="flex items-center text-sm bg-white border border-gray-300 rounded-lg focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500 overflow-hidden px-2.5 py-1.5 shadow-2xs">
                  <span class="text-red-500 font-bold select-none text-sm mr-1">#</span>
                  <input
                    v-model="tagInput"
                    @keydown.enter.prevent="addTagFromInput"
                    @keydown.,.prevent="addTagFromInput"
                    type="text"
                    placeholder="হ্যাশট্যাগ লিখুন (যেমন: বাজেট, নির্বাচন)..."
                    class="w-full text-xs border-none p-0 focus:ring-0 text-gray-800 outline-none"
                  />
                  <button
                    type="button"
                    @click="addTagFromInput"
                    class="ml-1 px-2.5 py-1 bg-gray-900 hover:bg-black text-white text-[11px] font-semibold rounded shrink-0 transition-colors"
                  >
                    যোগ
                  </button>
                </div>

                <!-- ট্যাগ চিপস -->
                <div v-if="form.tags && form.tags.length > 0" class="flex flex-wrap gap-1.5 mt-2">
                  <span
                    v-for="(t, idx) in form.tags"
                    :key="idx"
                    class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-gray-200/80 hover:bg-red-50 text-gray-800 hover:text-red-700 rounded-md text-xs font-medium transition-colors"
                  >
                    <span class="text-red-500 font-bold">#</span>{{ t }}
                    <button
                      type="button"
                      @click="removeTag(idx)"
                      class="text-gray-400 hover:text-red-600 font-bold ml-0.5"
                    >
                      ✕
                    </button>
                  </span>
                </div>

                <!-- সাজেস্টেড পপুলার ট্যাগস -->
                <div v-if="suggestedTags.length > 0" class="pt-2">
                  <span class="text-[10px] text-gray-400 font-semibold block mb-1">জনপ্রিয় ট্যাগ:</span>
                  <div class="flex flex-wrap gap-1">
                    <button
                      v-for="st in suggestedTags"
                      :key="st.id"
                      type="button"
                      @click="addSuggestedTag(st.name)"
                      class="text-[11px] px-2 py-0.5 bg-white border border-gray-200 hover:border-red-400 hover:text-red-600 rounded text-gray-600 transition-colors"
                    >
                      + #{{ st.name }}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">সংবাদের ধরন</label>
              <select
                v-model="form.type"
                class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none bg-white"
              >
                <option value="article">সাধারণ সংবাদ (Article)</option>
                <option value="video">ভিডিও প্রতিবেদন (Video)</option>
                <option value="gallery">ছবি গ্যালারি (Gallery)</option>
                <option value="live">লাইভ আপডেট (Live Blog)</option>
                <option value="opinion">মতামত ও কলাম (Opinion)</option>
                <option value="analysis">বিশ্লেষণধর্মী (Analysis)</option>
              </select>
            </div>

            <div class="pt-3 border-t border-gray-100 space-y-3">
              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_breaking" type="checkbox" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                <div>
                  <span class="text-sm font-medium text-gray-800">ব্রেকিং নিউজ</span>
                  <p class="text-xs text-gray-400">হেডলাইন টিকারে তৎক্ষণাৎ প্রকাশ পাবে</p>
                </div>
              </label>

              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                <div>
                  <span class="text-sm font-medium text-gray-800">প্রধান / ফিচার্ড সংবাদ</span>
                  <p class="text-xs text-gray-400">হোমপেজের মূল লিড নিউজ হিসেবে প্রাধান্য পাবে</p>
                </div>
              </label>

              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_exclusive" type="checkbox" class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                <div>
                  <span class="text-sm font-medium text-gray-800">এক্সক্লুসিভ নিউজ</span>
                  <p class="text-xs text-gray-400">বিশেষ এক্সক্লুসিভ ট্যাগ সহ চিহ্নিত হবে</p>
                </div>
              </label>
            </div>
          </div>

          <!-- ── এলাকা / অবস্থান (ঐচ্ছিক) Dedicated Card ── -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <div class="flex items-center gap-2">
                <span class="text-base">📍</span>
                <h3 class="text-base font-bold text-gray-900">ভৌগোলিক অবস্থান / এলাকা</h3>
              </div>
              <span class="text-[11px] text-gray-500 font-medium bg-gray-100 px-2.5 py-0.5 rounded-full">ঐচ্ছিক</span>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed">
              সংবাদের নির্দিষ্ট ভৌগোলিক অবস্থান ট্যাগ করার জন্য সংশ্লিষ্ট বিভাগ ও জেলা নির্বাচন করতে পারেন (ক্যাটাগরি নির্বাচনের পাশাপাশি এটি ঐচ্ছিক)।
            </p>

            <div class="space-y-3 bg-emerald-50/50 p-3.5 rounded-xl border border-emerald-200/80">
              <!-- বিভাগ নির্বাচন -->
              <div>
                <label class="block text-xs font-semibold text-emerald-900 mb-1">
                  বিভাগ নির্বাচন করুন
                </label>
                <select
                  v-model="form.division_id"
                  @change="handleAreaDivisionChange"
                  class="w-full text-sm px-3 py-2.5 border border-emerald-300 rounded-lg focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-none bg-white font-medium text-gray-800 transition-colors shadow-2xs"
                >
                  <option value="">সকল বিভাগ / কোনো নির্দিষ্ট বিভাগ নেই</option>
                  <option v-for="div in props.divisions" :key="div.id" :value="div.id">
                    {{ div.name.endsWith('বিভাগ') ? div.name : div.name + ' বিভাগ' }}
                  </option>
                </select>
              </div>

              <!-- জেলা নির্বাচন (বিভাগ সিলেক্ট করার পর ফিল্টার হবে) -->
              <div v-if="form.division_id && areaDistricts.length > 0">
                <label class="block text-xs font-semibold text-emerald-900 mb-1">
                  জেলা নির্বাচন করুন
                </label>
                <select
                  v-model="form.district_id"
                  class="w-full text-sm px-3 py-2.5 border border-emerald-300 rounded-lg focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 outline-none bg-white font-medium text-gray-800 transition-colors shadow-2xs"
                >
                  <option value="">সকল জেলা / কোনো নির্দিষ্ট জেলা নেই</option>
                  <option v-for="dist in areaDistricts" :key="dist.id" :value="dist.id">
                    {{ dist.name }}
                  </option>
                </select>
              </div>

              <!-- নির্বাচিত অবস্থান নির্দেশক -->
              <div v-if="selectedAreaText" class="pt-2 border-t border-emerald-200/60 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-emerald-800 font-medium">
                  <span>📌</span>
                  <span>নির্বাচিত অবস্থান: <strong class="text-emerald-900">{{ selectedAreaText }}</strong></span>
                </div>
                <button
                  type="button"
                  @click="clearAreaSelection"
                  class="text-red-500 hover:text-red-700 text-[11px] underline font-semibold"
                >
                  মুছুন
                </button>
              </div>
            </div>
          </div>

          <!-- Featured Image -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-3">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">ফিচার্ড প্রধান ছবি</h3>

            <div v-if="imageUrl" class="relative rounded-xl overflow-hidden border border-gray-200 mb-3 group">
              <img :src="imageUrl" alt="Preview" class="w-full h-44 object-cover" />
              <button
                type="button"
                @click="removeImage"
                class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                title="ছবি সরান"
              >
                ✕
              </button>
            </div>

            <div class="space-y-3">
              <!-- Featured image action buttons -->
              <div class="grid grid-cols-2 gap-2">
                <label
                  :class="[
                    'flex items-center justify-center gap-1.5 px-3 py-2.5 border-2 border-dashed border-gray-300 hover:border-red-500 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-gray-50 cursor-pointer transition-colors',
                    { 'opacity-50 pointer-events-none': isUploadingFeatured }
                  ]"
                >
                  <span v-if="isUploadingFeatured" class="w-3.5 h-3.5 border-2 border-red-500/30 border-t-red-600 rounded-full animate-spin"></span>
                  <span v-else>📁</span>
                  <span>{{ isUploadingFeatured ? 'আপলোড...' : 'নতুন ছবি' }}</span>
                  <input
                    type="file"
                    @change="uploadFeaturedFile"
                    accept="image/*"
                    :disabled="isUploadingFeatured"
                    class="hidden"
                  />
                </label>

                <button
                  type="button"
                  @click="openFeaturedMediaPicker"
                  class="flex items-center justify-center gap-1.5 px-3 py-2.5 border border-gray-300 hover:border-red-500 hover:bg-red-50/40 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-white transition-colors"
                >
                  <span>🗂️</span> মিডিয়া লাইব্রেরি
                </button>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">অথবা ওয়েব লিংক (URL)</label>
                <input
                  v-model="imageUrl"
                  type="url"
                  placeholder="https://..."
                  class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">ছবির ক্যাপশন / বিবরণ</label>
                <input
                  v-model="imageCaption"
                  type="text"
                  placeholder="ছবি: রয়টার্স / সংগৃহীত..."
                  class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
                />
              </div>
            </div>
          </div>

          <!-- Multi-Image Gallery Card -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <div>
                <h3 class="text-base font-bold text-gray-900">ছবি গ্যালারি (মাল্টি-ইমেজ)</h3>
                <p class="text-xs text-gray-400">সংবাদের অতিরিক্ত ছবি বা ফটো অ্যালবাম যোগ করুন (২ বা ৩টি ছবি সহজে যোগ করুন)</p>
              </div>
              <span class="text-xs px-2.5 py-1 bg-red-50 text-red-600 rounded-full font-bold">
                {{ galleryImages.length }} টি ছবি
              </span>
            </div>

            <!-- Gallery action buttons -->
            <div class="grid grid-cols-2 gap-2">
              <label
                :class="[
                  'flex items-center justify-center gap-1.5 px-3 py-2.5 border-2 border-dashed border-gray-300 hover:border-red-500 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-gray-50 cursor-pointer transition-colors text-center',
                  { 'opacity-50 pointer-events-none': isUploadingGallery }
                ]"
              >
                <span v-if="isUploadingGallery" class="w-3.5 h-3.5 border-2 border-red-500/30 border-t-red-600 rounded-full animate-spin"></span>
                <span v-else>📸</span>
                <span>{{ isUploadingGallery ? 'আপলোড হচ্ছে...' : 'ছবি আপলোড (একাধিক)' }}</span>
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  @change="uploadGalleryFiles"
                  :disabled="isUploadingGallery"
                  class="hidden"
                />
              </label>

              <button
                type="button"
                @click="openGalleryMediaPicker"
                class="flex items-center justify-center gap-1.5 px-3 py-2.5 border border-gray-300 hover:border-red-500 hover:bg-red-50/40 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-white transition-colors"
              >
                <span>🗂️</span> মিডিয়া লাইব্রেরি
              </button>
            </div>

            <!-- Gallery Previews Grid -->
            <div v-if="galleryImages.length > 0" class="space-y-2 pt-1">
              <div class="flex items-center justify-between text-xs text-gray-500 px-1">
                <span class="font-medium">যুক্ত ছবি ({{ galleryImages.length }} টি):</span>
                <button
                  type="button"
                  @click="galleryImages = []"
                  class="text-[11px] text-red-500 hover:underline"
                >
                  সব মুছুন
                </button>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                <div
                  v-for="(img, idx) in galleryImages"
                  :key="idx"
                  class="relative rounded-xl overflow-hidden border border-gray-200 group bg-gray-50 flex flex-col justify-between shadow-2xs"
                >
                  <div class="relative h-24 bg-gray-200">
                    <img :src="img.url" alt="Gallery item" class="w-full h-full object-cover" />
                    <span class="absolute top-1 left-1 bg-black/60 text-white text-[10px] px-1.5 py-0.5 rounded font-mono">
                      #{{ idx + 1 }}
                    </span>
                    <button
                      type="button"
                      @click="removeGalleryImage(idx)"
                      class="absolute top-1 right-1 bg-red-600 hover:bg-red-700 text-white w-5 h-5 flex items-center justify-center rounded-full text-xs shadow transition-colors"
                      title="মুছে ফেলুন"
                    >
                      ✕
                    </button>
                  </div>
                  <input
                    v-model="img.caption"
                    type="text"
                    placeholder="ছবির ক্যাপশন..."
                    class="w-full text-[11px] px-2 py-1 bg-white border-t border-gray-100 outline-none focus:bg-red-50/20"
                  />
                </div>
              </div>
            </div>

            <!-- Add by URL input -->
            <div class="flex items-center gap-2 pt-1">
              <input
                v-model="newGalleryUrl"
                type="url"
                placeholder="ওয়েব ছবির লিঙ্ক (URL) দিন..."
                class="w-full text-xs px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
                @keydown.enter.prevent="addGalleryUrl"
              />
              <button
                type="button"
                @click="addGalleryUrl"
                class="px-3 py-2 bg-gray-800 hover:bg-black text-white text-xs font-semibold rounded-lg shrink-0 transition-colors"
              >
                যুক্ত
              </button>
            </div>
          </div>
        </div>
      </form>

      <!-- Media Picker Modal -->
      <MediaPickerModal
        :show="showMediaPicker"
        :multiple="mediaPickerMultiple"
        :title="mediaPickerTitle"
        @close="showMediaPicker = false"
        @select="handleMediaPickerSelect"
      />

      <!-- Client-side Floating Notification Toast -->
      <transition name="fade">
        <div
          v-if="submitAlertMessage"
          class="fixed bottom-6 right-6 z-50 max-w-md px-5 py-3.5 rounded-xl shadow-2xl flex items-center justify-between gap-3 text-sm font-semibold bg-red-600 text-white border border-red-500"
        >
          <div class="flex items-center gap-2.5">
            <span class="text-lg">⚠️</span>
            <span>{{ submitAlertMessage }}</span>
          </div>
          <button type="button" @click="submitAlertMessage = ''" class="text-white/80 hover:text-white ml-2 text-base">✕</button>
        </div>
      </transition>

      <!-- Inline Subcategory Creation Modal -->
      <transition name="fade">
        <div
          v-if="showAddSubcategoryModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs"
        >
          <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <div>
                <h3 class="text-base font-bold text-gray-900">নতুন সাব-ক্যাটাগরি যুক্ত করুন</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                  একটি মূল ক্যাটাগরির অধীনে নতুন সাব-ক্যাটাগরি তৈরি করুন
                </p>
              </div>
              <button
                type="button"
                @click="closeAddSubcategoryModal"
                class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 text-sm font-bold"
              >
                ✕
              </button>
            </div>

            <div class="space-y-3">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                  মূল ক্যাটাগরি <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="newSubcategoryParentId"
                  class="w-full text-sm px-3.5 py-2 border border-gray-300 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none bg-white font-medium text-gray-800"
                >
                  <option value="" disabled>-- মূল ক্যাটাগরি বেছে নিন --</option>
                  <option v-for="cat in rootCategories" :key="cat.id" :value="cat.id">
                    📁 {{ cat.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                  সাব-ক্যাটাগরির নাম (বাংলা) <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="newSubcategoryName"
                  type="text"
                  placeholder="যেমন: মধ্যপ্রাচ্য, ক্রিকেট, শিক্ষা..."
                  @keydown.enter.prevent="submitNewSubcategory"
                  class="w-full text-sm px-3.5 py-2 border border-gray-300 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
                  autofocus
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                  ইংরেজি নাম (ঐচ্ছিক - URL স্লাগের জন্য)
                </label>
                <input
                  v-model="newSubcategoryNameEn"
                  type="text"
                  placeholder="e.g. Middle East, Cricket..."
                  @keydown.enter.prevent="submitNewSubcategory"
                  class="w-full text-sm px-3.5 py-2 border border-gray-300 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
                />
              </div>

              <p v-if="subcategoryErrorMessage" class="text-xs text-red-600 font-medium">
                ⚠️ {{ subcategoryErrorMessage }}
              </p>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-gray-100">
              <button
                type="button"
                @click="closeAddSubcategoryModal"
                class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-xl text-xs font-semibold transition"
              >
                বাতিল
              </button>
              <button
                type="button"
                @click="submitNewSubcategory"
                :disabled="isCreatingSubcategory"
                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm"
              >
                <span v-if="isCreatingSubcategory" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span>{{ isCreatingSubcategory ? 'সংরক্ষণ হচ্ছে...' : 'সাব-ক্যাটাগরি যুক্ত করুন' }}</span>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import RichTipTapEditor from '@/Components/UI/RichTipTapEditor.vue'
import MediaPickerModal from '@/Components/UI/MediaPickerModal.vue'

const props = defineProps({
  post: {
    type: Object,
    default: null,
  },
  categories: {
    type: Array,
    default: () => [],
  },
  divisions: {
    type: Array,
    default: () => [],
  },
  existingTags: {
    type: Array,
    default: () => [],
  },
})

const isEdit = computed(() => !!props.post?.id)

// Featured image states
const imageUrl = ref(props.post?.featured_image?.original || (typeof props.post?.featured_image === 'string' ? props.post?.featured_image : ''))
const imageCaption = ref(props.post?.featured_image?.caption || props.post?.featured_image?.alt || '')
const isUploadingFeatured = ref(false)

// Gallery images states
const initialGallery = computed(() => {
  const g = props.post?.featured_image?.gallery
  if (Array.isArray(g)) {
    return g.map(item => (typeof item === 'string' ? { url: item, caption: '' } : item))
  }
  return []
})
const galleryImages = ref([...initialGallery.value])
const isUploadingGallery = ref(false)
const newGalleryUrl = ref('')

// Media picker states
const showMediaPicker = ref(false)
const mediaPickerMultiple = ref(false)
const mediaPickerTarget = ref('featured') // 'featured' | 'gallery'
const mediaPickerTitle = computed(() => {
  return mediaPickerTarget.value === 'featured'
    ? 'মিডিয়া লাইব্রেরি থেকে প্রধান ছবি নির্বাচন করুন'
    : 'মিডিয়া লাইব্রেরি থেকে গ্যালারিতে ছবি যোগ করুন'
})

const openFeaturedMediaPicker = () => {
  mediaPickerTarget.value = 'featured'
  mediaPickerMultiple.value = false
  showMediaPicker.value = true
}

const openGalleryMediaPicker = () => {
  mediaPickerTarget.value = 'gallery'
  mediaPickerMultiple.value = true
  showMediaPicker.value = true
}

const handleMediaPickerSelect = (selected) => {
  if (mediaPickerTarget.value === 'featured') {
    const item = Array.isArray(selected) ? selected[0] : selected
    if (item?.url) {
      imageUrl.value = item.url
    }
  } else {
    const items = Array.isArray(selected) ? selected : [selected]
    items.forEach(item => {
      if (item?.url) {
        galleryImages.value.push({
          url: item.url,
          caption: item.name ? item.name.replace(/\.[^/.]+$/, '') : '',
        })
      }
    })
  }
}

const getInitialSecondaryCategoryIds = () => {
  if (props.post?.secondary_categories && Array.isArray(props.post.secondary_categories)) {
    return props.post.secondary_categories.map(c => Number(c.id))
  }
  if (props.post?.secondaryCategories && Array.isArray(props.post.secondaryCategories)) {
    return props.post.secondaryCategories.map(c => Number(c.id))
  }
  if (Array.isArray(props.post?.additional_category_ids)) {
    return props.post.additional_category_ids.map(Number)
  }
  return []
}

const getInitialTags = () => {
  if (!props.post?.tags || !Array.isArray(props.post.tags)) return []
  return props.post.tags.map(t => (typeof t === 'string' ? t.replace(/^#+/, '') : (t.name || '').replace(/^#+/, ''))).filter(Boolean)
}

const form = useForm({
  title: props.post?.title || '',
  slug: props.post?.slug || '',
  excerpt: props.post?.excerpt || '',
  body: props.post?.body || '',
  category_id: props.post?.category_id || '',
  division_id: props.post?.division_id || '',
  district_id: props.post?.district_id || '',
  secondary_category_ids: getInitialSecondaryCategoryIds(),
  tags: getInitialTags(),
  type: props.post?.type?.value || props.post?.type || 'article',
  status: props.post?.status?.value || props.post?.status || 'draft',
  is_breaking: Boolean(props.post?.is_breaking),
  is_featured: Boolean(props.post?.is_featured),
  is_exclusive: Boolean(props.post?.is_exclusive),
  seo_title: props.post?.seo_title || '',
  seo_description: props.post?.seo_description || '',
  featured_image: props.post?.featured_image || null,
})

// ─── Categories & Subcategories Management ──────────────────────────────────
const localCategories = ref([...props.categories])

watch(() => props.categories, (newVal) => {
  if (Array.isArray(newVal)) {
    localCategories.value = [...newVal]
  }
}, { deep: true })

const rootCategories = computed(() => {
  return localCategories.value
    .filter(c => !c.parent_id || Number(c.parent_id) === 0 || Number(c.depth) === 0)
    .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
})

// ─── Primary Category (Original 2 Dropdowns) ────────────────────────────────
const selectedRootCategoryId = ref('')
const selectedSubcategoryId = ref('')

const currentSubcategories = computed(() => {
  if (!selectedRootCategoryId.value) return []
  const rootId = Number(selectedRootCategoryId.value)
  return localCategories.value
    .filter(c => c.parent_id && Number(c.parent_id) === rootId)
    .sort((a, b) => (a.order ?? 0) - (b.order ?? 0) || a.name.localeCompare(b.name, 'bn'))
})

const primaryCategoryInfo = computed(() => {
  if (!form.category_id) return null
  const cat = localCategories.value.find(c => Number(c.id) === Number(form.category_id))
  if (!cat) return null

  if (cat.parent_id) {
    const parent = localCategories.value.find(c => Number(c.id) === Number(cat.parent_id))
    return {
      id: cat.id,
      name: cat.name,
      path: parent ? `${parent.name} › ${cat.name}` : cat.name,
      isSubcategory: true,
      label: 'সাবক্যাটাগরি',
    }
  }

  return {
    id: cat.id,
    name: cat.name,
    path: cat.name,
    isSubcategory: false,
    label: 'মূল ক্যাটাগরি',
  }
})

const syncPrimaryAndSecondary = () => {
  if (form.category_id) {
    const pId = Number(form.category_id)
    form.secondary_category_ids = form.secondary_category_ids.filter(x => Number(x) !== pId)
  }
}

const onRootCategoryChange = () => {
  selectedSubcategoryId.value = ''
  if (selectedRootCategoryId.value) {
    form.category_id = Number(selectedRootCategoryId.value)
  } else {
    form.category_id = ''
  }
  syncPrimaryAndSecondary()
  if (form.errors.category_id) {
    delete form.errors.category_id
  }
}

const onSubcategoryChange = () => {
  if (selectedSubcategoryId.value) {
    form.category_id = Number(selectedSubcategoryId.value)
  } else if (selectedRootCategoryId.value) {
    form.category_id = Number(selectedRootCategoryId.value)
  } else {
    form.category_id = ''
  }
  syncPrimaryAndSecondary()
  if (form.errors.category_id) {
    delete form.errors.category_id
  }
}

const initCategoriesFromPost = () => {
  if (!form.category_id) return
  const cat = localCategories.value.find(c => Number(c.id) === Number(form.category_id))
  if (cat) {
    if (cat.parent_id) {
      selectedRootCategoryId.value = Number(cat.parent_id)
      selectedSubcategoryId.value = Number(cat.id)
    } else {
      selectedRootCategoryId.value = Number(cat.id)
      selectedSubcategoryId.value = ''
    }
  }
}

initCategoriesFromPost()

watch(localCategories, () => {
  if (form.category_id && !selectedRootCategoryId.value) {
    initCategoriesFromPost()
  }
}, { deep: true })

watch(() => props.post, (newPost) => {
  if (newPost) {
    form.category_id = newPost.category_id || ''
    form.secondary_category_ids = getInitialSecondaryCategoryIds()
    initCategoriesFromPost()
  }
})

// ─── Multi-Category (অতিরিক্ত ক্যাটাগরি) Management ────────────────────────
const selectedSecondaryCatId = ref('')

const secondaryCategoryGroups = computed(() => {
  const primaryId = form.category_id ? Number(form.category_id) : null
  const selectedSecIds = new Set(form.secondary_category_ids.map(Number))
  const groups = []

  rootCategories.value.forEach(root => {
    const rootId = Number(root.id)
    const isRootPrimary = primaryId === rootId
    const isRootSelected = selectedSecIds.has(rootId)

    const children = localCategories.value
      .filter(c => c.parent_id && Number(c.parent_id) === rootId)
      .filter(c => Number(c.id) !== primaryId && !selectedSecIds.has(Number(c.id)))
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0) || a.name.localeCompare(b.name, 'bn'))

    if ((!isRootPrimary && !isRootSelected) || children.length > 0) {
      groups.push({
        id: root.id,
        name: root.name,
        isRootAvailable: !isRootPrimary && !isRootSelected,
        children: children,
      })
    }
  })

  return groups
})

const selectedSecondaryCategories = computed(() => {
  const catMap = new Map(localCategories.value.map(c => [Number(c.id), c]))
  return form.secondary_category_ids
    .map(id => {
      const cat = catMap.get(Number(id))
      if (!cat) return null
      let parent_name = null
      if (cat.parent_id && catMap.has(Number(cat.parent_id))) {
        parent_name = catMap.get(Number(cat.parent_id)).name
      }
      return {
        id: cat.id,
        name: cat.name,
        parent_name: parent_name,
      }
    })
    .filter(Boolean)
})

const addSecondaryCategory = () => {
  if (!selectedSecondaryCatId.value) return
  const numId = Number(selectedSecondaryCatId.value)
  if (numId && !form.secondary_category_ids.some(x => Number(x) === numId)) {
    if (form.category_id && Number(form.category_id) === numId) {
      selectedSecondaryCatId.value = ''
      return
    }
    form.secondary_category_ids.push(numId)
  }
  selectedSecondaryCatId.value = ''
}

const removeSecondaryCategory = (id) => {
  const numId = Number(id)
  form.secondary_category_ids = form.secondary_category_ids.filter(x => Number(x) !== numId)
}

// ─── Inline Subcategory Creation Modal ──────────────────────────────────────
const showAddSubcategoryModal = ref(false)
const isCreatingSubcategory = ref(false)
const newSubcategoryParentId = ref('')
const newSubcategoryName = ref('')
const newSubcategoryNameEn = ref('')
const subcategoryErrorMessage = ref('')

const openAddSubcategoryModal = (parentId = null) => {
  if (parentId) {
    newSubcategoryParentId.value = Number(parentId)
  } else if (selectedRootCategoryId.value) {
    newSubcategoryParentId.value = Number(selectedRootCategoryId.value)
  } else if (rootCategories.value.length > 0) {
    newSubcategoryParentId.value = Number(rootCategories.value[0].id)
  } else {
    newSubcategoryParentId.value = ''
  }
  newSubcategoryName.value = ''
  newSubcategoryNameEn.value = ''
  subcategoryErrorMessage.value = ''
  showAddSubcategoryModal.value = true
}

const closeAddSubcategoryModal = () => {
  showAddSubcategoryModal.value = false
  newSubcategoryParentId.value = ''
  newSubcategoryName.value = ''
  newSubcategoryNameEn.value = ''
  subcategoryErrorMessage.value = ''
}

const submitNewSubcategory = async () => {
  const name = newSubcategoryName.value.trim()
  if (!name) {
    subcategoryErrorMessage.value = 'সাব-ক্যাটাগরির নাম লিখুন।'
    return
  }
  if (!newSubcategoryParentId.value) {
    subcategoryErrorMessage.value = 'মূল ক্যাটাগরি বেছে নিন।'
    return
  }

  isCreatingSubcategory.value = true
  subcategoryErrorMessage.value = ''

  try {
    const parentId = Number(newSubcategoryParentId.value)
    const payload = {
      name: name,
      name_en: newSubcategoryNameEn.value.trim() || null,
      parent_id: parentId,
      show_in_nav: true,
      is_active: true,
    }

    const res = await window.axios.post(route('dashboard.categories.store'), payload, {
      headers: { 'Accept': 'application/json' },
    })

    const newCat = res.data?.category
    if (newCat) {
      localCategories.value.push(newCat)
      selectedRootCategoryId.value = Number(newCat.parent_id)
      selectedSubcategoryId.value = Number(newCat.id)
      form.category_id = Number(newCat.id)
      syncPrimaryAndSecondary()
      closeAddSubcategoryModal()
    } else {
      closeAddSubcategoryModal()
      window.location.reload()
    }
  } catch (err) {
    console.error('Failed to create subcategory:', err)
    subcategoryErrorMessage.value = err.response?.data?.message || err.response?.data?.errors?.name?.[0] || 'সাব-ক্যাটাগরি তৈরি করতে ব্যর্থ হয়েছে।'
  } finally {
    isCreatingSubcategory.value = false
  }
}

// ─── Hashtags / Tags Management ──────────────────────────────────────────────
const tagInput = ref('')

const addTagFromInput = () => {
  const raw = tagInput.value.trim()
  if (!raw) return
  const parts = raw.split(/[,，]/).map(p => p.trim().replace(/^#+/, '')).filter(Boolean)
  parts.forEach(p => {
    if (!form.tags.includes(p)) {
      form.tags.push(p)
    }
  })
  tagInput.value = ''
}

const addSuggestedTag = (name) => {
  const clean = name.trim().replace(/^#+/, '')
  if (clean && !form.tags.includes(clean)) {
    form.tags.push(clean)
  }
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}

const suggestedTags = computed(() => {
  if (!props.existingTags || props.existingTags.length === 0) return []
  const currentSet = new Set(form.tags.map(t => t.toLowerCase()))
  return props.existingTags
    .filter(t => !currentSet.has(t.name.toLowerCase()))
    .slice(0, 8)
})

// ─── Area / Location Management (Fully Decoupled from Category) ─────────────
const areaDistricts = computed(() => {
  if (!form.division_id) return []
  const div = props.divisions.find(d => Number(d.id) === Number(form.division_id))
  return div?.districts || []
})

const handleAreaDivisionChange = () => {
  form.district_id = ''
}

const clearAreaSelection = () => {
  form.division_id = ''
  form.district_id = ''
}

const selectedAreaText = computed(() => {
  if (!form.division_id) return ''
  const div = props.divisions.find(d => Number(d.id) === Number(form.division_id))
  if (!div) return ''
  const divName = div.name.endsWith('বিভাগ') ? div.name : `${div.name} বিভাগ`
  if (form.district_id) {
    const dist = div.districts?.find(d => Number(d.id) === Number(form.district_id))
    if (dist) {
      return `${divName} › ${dist.name}`
    }
  }
  return divName
})


const removeImage = () => {
  imageUrl.value = ''
  imageCaption.value = ''
  form.featured_image = null
}

const uploadFeaturedFile = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  isUploadingFeatured.value = true
  const formData = new FormData()
  formData.append('file', file)

  try {
    const res = await window.axios.post(route('dashboard.media.upload'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Accept: 'application/json',
      },
    })
    if (res.data?.url) {
      imageUrl.value = res.data.url
    }
  } catch (err) {
    console.error('Featured image upload failed:', err)
    alert('ছবি আপলোড করতে ব্যর্থ হয়েছে। সর্বোচ্চ ফাইল সাইজ ১০MB-এর মধ্যে হতে হবে।')
  } finally {
    isUploadingFeatured.value = false
    e.target.value = ''
  }
}

const uploadGalleryFiles = async (e) => {
  const fileList = Array.from(e.target.files || [])
  if (fileList.length === 0) return

  isUploadingGallery.value = true
  const formData = new FormData()

  if (fileList.length === 1) {
    formData.append('file', fileList[0])
  } else {
    fileList.forEach(file => {
      formData.append('files[]', file)
    })
  }

  try {
    const res = await window.axios.post(route('dashboard.media.upload'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Accept: 'application/json',
      },
    })

    if (res.data?.files && Array.isArray(res.data.files)) {
      res.data.files.forEach(item => {
        if (item.url) {
          galleryImages.value.push({
            url: item.url,
            caption: item.name ? item.name.replace(/\.[^/.]+$/, '') : '',
          })
        }
      })
    } else if (res.data?.url) {
      galleryImages.value.push({ url: res.data.url, caption: '' })
    }
  } catch (err) {
    console.error('Gallery images upload failed:', err)
    alert('গ্যালারির ছবি আপলোড করতে ব্যর্থ হয়েছে। সর্বোচ্চ ফাইল সাইজ ১০MB-এর মধ্যে হতে হবে।')
  } finally {
    isUploadingGallery.value = false
    e.target.value = ''
  }
}

const addGalleryUrl = () => {
  if (!newGalleryUrl.value.trim()) return
  galleryImages.value.push({
    url: newGalleryUrl.value.trim(),
    caption: '',
  })
  newGalleryUrl.value = ''
}

const removeGalleryImage = (index) => {
  galleryImages.value.splice(index, 1)
}

const activeSubmitAction = ref(null)
const submitAlertMessage = ref('')

const submit = (targetStatus) => {
  // 1. Client-side title validation
  if (!form.title || !form.title.trim()) {
    submitAlertMessage.value = 'অনুগ্রহ করে সংবাদের শিরোনাম লিখুন।'
    setTimeout(() => { submitAlertMessage.value = '' }, 4000)
    const titleEl = document.querySelector('input[placeholder*="শিরোনাম"]')
    if (titleEl) {
      titleEl.scrollIntoView({ behavior: 'smooth', block: 'center' })
      titleEl.focus()
    }
    return
  }

  // 2. Client-side category validation
  if (!form.category_id) {
    submitAlertMessage.value = 'অনুগ্রহ করে প্রধান ক্যাটাগরি নির্বাচন করুন।'
    form.errors.category_id = 'অনুগ্রহ করে প্রধান ক্যাটাগরি নির্বাচন করুন।'
    setTimeout(() => { submitAlertMessage.value = '' }, 4000)
    const catEl = document.getElementById('category-selector-container')
    if (catEl) {
      catEl.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
    return
  }

  // 3. Client-side body validation
  const cleanBody = form.body ? form.body.replace(/<[^>]*>/g, '').trim() : ''
  if (!cleanBody && !form.body.includes('<img') && !form.body.includes('<iframe')) {
    submitAlertMessage.value = 'অনুগ্রহ করে সংবাদের মূল বিবরণ বা কনটেন্ট লিখুন।'
    setTimeout(() => { submitAlertMessage.value = '' }, 4000)
    const editorEl = document.querySelector('.prose')
    if (editorEl) {
      editorEl.scrollIntoView({ behavior: 'smooth', block: 'center' })
    }
    return
  }

  // 4. Update featured image structure or reset to null
  if (imageUrl.value || galleryImages.value.length > 0) {
    form.featured_image = {
      original: imageUrl.value || (galleryImages.value[0]?.url ?? ''),
      sm: imageUrl.value || (galleryImages.value[0]?.url ?? ''),
      md: imageUrl.value || (galleryImages.value[0]?.url ?? ''),
      caption: imageCaption.value,
      alt: imageCaption.value,
      gallery: galleryImages.value,
    }
  } else {
    form.featured_image = null
  }

  form.status = targetStatus
  activeSubmitAction.value = targetStatus

  const requestOptions = {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Post submit errors:', errors)
      const firstKey = Object.keys(errors)[0]
      const firstMsg = errors[firstKey]
      submitAlertMessage.value = `সংরক্ষণ করা যায়নি: ${firstMsg}`
      setTimeout(() => { submitAlertMessage.value = '' }, 5000)

      setTimeout(() => {
        if (errors.title) {
          document.querySelector('input[placeholder*="শিরোনাম"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
        } else if (errors.category_id) {
          document.getElementById('category-selector-container')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
        } else if (errors.body) {
          document.querySelector('.prose')?.scrollIntoView({ behavior: 'smooth', block: 'center' })
        } else {
          const errorEl = document.querySelector('.text-red-500:not(:empty)')
          if (errorEl) errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
      }, 100)
    },
    onFinish: () => {
      activeSubmitAction.value = null
    },
  }

  if (isEdit.value) {
    form.put(route('dashboard.posts.update', props.post.id), requestOptions)
  } else {
    form.post(route('dashboard.posts.store'), requestOptions)
  }
}
</script>
