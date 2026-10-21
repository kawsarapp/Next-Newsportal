<template>
  <div class="news-detail-wrapper space-y-6">
    <!-- Reading Progress Bar (Fixed at the very top of viewport) -->
    <div class="fixed top-0 left-0 right-0 h-1.5 z-50 bg-gray-200/50 pointer-events-none">
      <div
        class="h-full bg-gradient-to-r from-red-500 via-red-600 to-rose-600 transition-[width] duration-150 ease-out"
        :style="{ width: `${readingProgress}%` }"
      ></div>
    </div>

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-500">
      <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
      <span>/</span>
      <NuxtLink
        v-if="post?.category"
        :to="`/category/${post.category.slug}`"
        class="text-red-600 font-medium hover:underline"
      >
        {{ post.category.name }}
      </NuxtLink>
      <span v-else class="text-red-600 font-medium">জাতীয়</span>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="pending && !post" class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-pulse">
      <div class="lg:col-span-8 space-y-4">
        <div class="h-10 bg-gray-200 rounded-xl w-3/4"></div>
        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
        <div class="h-96 bg-gray-200 rounded-2xl"></div>
        <div class="space-y-2">
          <div class="h-4 bg-gray-200 rounded"></div>
          <div class="h-4 bg-gray-200 rounded"></div>
          <div class="h-4 bg-gray-200 rounded w-5/6"></div>
        </div>
      </div>
      <div class="lg:col-span-4 space-y-4">
        <div class="h-64 bg-gray-200 rounded-2xl"></div>
        <div class="h-48 bg-gray-200 rounded-2xl"></div>
      </div>
    </div>

    <!-- Not found / Error -->
    <div v-else-if="!post" class="text-center py-20 bg-white rounded-2xl border p-8 space-y-3">
      <h2 class="text-xl font-bold text-gray-800">সংবাদটি খুঁজে পাওয়া যায়নি</h2>
      <p class="text-xs text-gray-500">হয়তো সংবাদটি সরানো হয়েছে বা লিঙ্কটি ভুল।</p>
      <NuxtLink to="/" class="inline-block mt-3 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold">
        ← প্রচ্ছদে ফিরে যান
      </NuxtLink>
    </div>

    <!-- Two-Column Article & Sidebar Grid -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <!-- ── Left Column: Main Article Content (Desktop 8 Cols) ── -->
      <article class="lg:col-span-8 bg-white rounded-2xl p-6 sm:p-8 border border-gray-200/80 shadow-xs space-y-6 self-start">
        <div class="space-y-3">
          <div class="flex items-center gap-2 flex-wrap">
            <NuxtLink
              v-if="post.category"
              :to="`/category/${post.category.slug}`"
              class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-bold px-2.5 py-1 rounded-full transition-colors"
            >
              {{ post.category.name }}
            </NuxtLink>
            <span v-else class="text-xs bg-red-50 text-red-600 font-bold px-2.5 py-1 rounded-full">
              বিশেষ সংবাদ
            </span>

            <!-- Secondary / Additional Categories -->
            <NuxtLink
              v-for="secCat in post.secondary_categories"
              :key="secCat.id"
              :to="`/category/${secCat.slug}`"
              class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-2.5 py-1 rounded-full transition-colors"
            >
              {{ secCat.name }}
            </NuxtLink>

            <!-- Area / Location Pill (Separated from Category) -->
            <NuxtLink
              v-if="post.district || post.division"
              :to="`/category/saradesh?division_id=${post.division_id}${post.district_id ? `&district_id=${post.district_id}` : ''}`"
              class="text-xs bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-full border border-emerald-200/60 transition-colors flex items-center gap-1"
            >
              <span>📍</span>
              <span>{{ post.district?.name ? `${post.district.name}, ${post.division?.name || ''}` : `${post.division?.name} বিভাগ` }}</span>
            </NuxtLink>

            <span v-if="post.is_breaking" class="text-xs bg-red-600 text-white font-bold px-2.5 py-1 rounded-full">
              ব্রেকিং
            </span>
            <span v-if="post.is_exclusive" class="text-xs bg-amber-500 text-white font-bold px-2.5 py-1 rounded-full">
              এক্সক্লুসিভ
            </span>
          </div>

          <h1 class="text-2xl sm:text-3.5xl font-black text-gray-900 leading-snug">
            {{ post.title }}
          </h1>

          <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-gray-400 py-3 border-y border-gray-100">
            <div class="flex items-center gap-3">
              <span class="font-semibold text-gray-700">প্রতিবেদক: {{ post.author?.name || 'অনলাইন ডেস্ক' }}</span>
              <span>·</span>
              <span>প্রকাশ: {{ formatDate(post.published_at) }}</span>
            </div>

            <div class="flex items-center gap-3">
              <span>⏱️ {{ post.reading_time || 2 }} মিনিট পাঠ</span>
              <span>·</span>
              <span>👁️ {{ post.view_count || 1 }} ভিউ</span>
            </div>
          </div>

          <!-- Reader Experience Toolbar (Font size adjuster & reading tracker) -->
          <div class="flex items-center justify-between gap-3 py-2 px-3 bg-gray-50/90 rounded-xl border border-gray-100 text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <span class="text-gray-500 font-medium">হরফের আকার:</span>
              <div class="flex items-center bg-white rounded-lg p-0.5 border border-gray-200 shadow-2xs">
                <button
                  @click="setFontSize('small')"
                  type="button"
                  class="px-2.5 py-1 rounded-md text-xs font-bold transition-all"
                  :class="currentFontSize === 'small' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                  title="হরফ ছোট করুন (A-)"
                  aria-label="হরফ ছোট করুন"
                >
                  A-
                </button>
                <button
                  @click="setFontSize('normal')"
                  type="button"
                  class="px-2.5 py-1 rounded-md text-xs font-bold transition-all"
                  :class="currentFontSize === 'normal' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                  title="স্বাভাবিক হরফ (A)"
                  aria-label="স্বাভাবিক হরফ"
                >
                  A
                </button>
                <button
                  @click="setFontSize('large')"
                  type="button"
                  class="px-2.5 py-1 rounded-md text-xs font-bold transition-all"
                  :class="currentFontSize === 'large' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
                  title="হরফ বড় করুন (A+)"
                  aria-label="হরফ বড় করুন"
                >
                  A+
                </button>
              </div>
            </div>

            <div class="flex items-center gap-1.5 text-[11px] font-medium text-gray-500">
              <span class="hidden sm:inline">পঠিত:</span>
              <span class="font-bold text-red-600 font-mono">{{ readingProgress }}%</span>
            </div>
          </div>
        </div>

        <!-- Excerpt Highlight -->
        <div v-if="post.excerpt" class="p-4 bg-gray-50 border-l-4 border-red-600 rounded-r-xl text-sm font-semibold text-gray-700 leading-relaxed italic">
          {{ post.excerpt }}
        </div>

        <!-- Featured Image -->
        <div v-if="post.featured_image?.url || post.featured_image?.original || (typeof post.featured_image === 'string' && post.featured_image)" class="rounded-2xl overflow-hidden bg-gray-100 border border-gray-200">
          <div class="relative w-full aspect-video sm:aspect-[16/9] max-h-[500px] overflow-hidden bg-gray-100">
            <img
              :src="post.featured_image?.url || post.featured_image?.original || post.featured_image"
              :alt="post.featured_image?.alt || post.title"
              loading="eager"
              fetchpriority="high"
              decoding="async"
              class="w-full h-full object-cover"
            />
          </div>
          <div v-if="post.featured_image?.caption" class="px-4 py-2 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 italic">
            📷 {{ post.featured_image.caption }}
          </div>
        </div>

        <!-- Main Body HTML with Dynamic Font Size -->
        <div
          v-if="post.body"
          class="article-content pt-2 transition-all duration-200"
          :style="fontSizeStyle"
          @click="handleContentClick"
          v-html="post.body"
        ></div>
        <div
          v-else-if="post.excerpt"
          class="article-content pt-2 text-gray-700 transition-all duration-200"
          :style="fontSizeStyle"
          @click="handleContentClick"
        >
          <p>{{ post.excerpt }}</p>
        </div>

        <!-- In-Article Sponsored Banner -->
        <div class="my-6 rounded-2xl overflow-hidden bg-gradient-to-r from-red-50 via-amber-50 to-orange-50 border border-amber-200/70 p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-3 shadow-2xs">
          <div class="flex items-center gap-3">
            <span class="text-2xl">📢</span>
            <div>
              <span class="text-[9px] uppercase tracking-wider font-bold text-amber-700 block">বিজ্ঞাপন · Sponsored</span>
              <p class="text-xs sm:text-sm font-bold text-gray-800">আপনার প্রতিষ্ঠানের প্রচারণায় দৈনিক বাংলা ডিজিটাল প্ল্যাটফর্ম</p>
            </div>
          </div>
          <a
            href="mailto:ads@dainikbangla.com"
            class="px-4 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-2xs shrink-0"
          >
            যোগাযোগ করুন →
          </a>
        </div>

        <!-- Tags / Hashtags List -->
        <div v-if="post.tags && post.tags.length > 0" class="pt-4 border-t border-gray-100 flex items-center gap-2 flex-wrap">
          <span class="text-xs text-gray-400 font-semibold flex items-center gap-1">
            <span>🏷️</span> হ্যাশট্যাগ:
          </span>
          <NuxtLink
            v-for="tag in post.tags"
            :key="tag.id || tag.slug || tag"
            :to="`/search?q=${encodeURIComponent(tag.name || tag)}`"
            class="px-2.5 py-1 bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1 group shadow-2xs"
            :title="`#${tag.name || tag} সম্পর্কিত সংবাদ অনুসন্ধান করুন`"
          >
            <span class="text-red-500 font-bold">#</span>
            <span>{{ tag.name || tag }}</span>
          </NuxtLink>
        </div>

        <!-- Photo Gallery Section (if available) -->
        <div v-if="post.featured_image?.gallery && post.featured_image.gallery.length > 0" class="pt-6 border-t border-gray-100 space-y-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
              <span>📸</span> ফটো গ্যালারি
            </h3>
            <span class="text-xs font-semibold px-2.5 py-0.5 bg-red-50 text-red-600 rounded-full">
              {{ post.featured_image.gallery.length }} টি ছবি
            </span>
          </div>

          <div :class="galleryGridClass">
            <div
              v-for="(item, i) in post.featured_image.gallery"
              :key="i"
              class="group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-xs hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
              @click="openLightbox(i)"
            >
              <div class="block overflow-hidden relative" :class="galleryImageHeightClass">
                <img
                  :src="item.url || item"
                  :alt="item.caption || post.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  loading="lazy"
                />
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/25 transition-colors flex items-center justify-center">
                  <span class="opacity-0 group-hover:opacity-100 transition-opacity bg-black/70 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-xs">
                    🔍 বড় করে দেখুন
                  </span>
                </div>
              </div>
              <div v-if="item.caption" class="p-2.5 text-xs text-gray-700 bg-white border-t border-gray-100 font-medium leading-relaxed">
                {{ item.caption }}
              </div>
            </div>
          </div>
        </div>

        <!-- Social Share Section -->
        <div class="pt-6 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
          <span class="text-xs font-bold text-gray-700">সংবাদটি শেয়ার করুন:</span>
          <div class="flex items-center gap-2">
            <button @click="share('fb')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
              <span>f</span> Facebook
            </button>
            <button @click="share('twitter')" class="px-3 py-1.5 bg-gray-900 hover:bg-black text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
              <span>𝕏</span> Twitter
            </button>
            <button @click="copyLink" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5">
              <span>🔗</span> লিংক কপি
            </button>
          </div>
        </div>

        <!-- ── সংবাদ সমাপ্তি সেকশন: জনপ্রিয় ও সর্বশেষ সংবাদ (Dual-Column Bottom Feed) ── -->
        <div class="mt-8 pt-8 border-t border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 relative">
            <!-- বাম কলাম: জনপ্রিয় -->
            <div class="space-y-4">
              <div class="border-b border-gray-200 pb-2.5">
                <h3 class="text-xl font-bold text-[#1b3b4f] tracking-tight">
                  জনপ্রিয়
                </h3>
              </div>

              <div class="divide-y divide-gray-100">
                <div
                  v-for="(item, idx) in bottomPopularNews"
                  :key="`pop-${item.id}`"
                  class="py-3 first:pt-0 last:pb-0 group"
                >
                  <NuxtLink :to="`/news/${item.slug}`" @click="scrollToTop" class="flex items-center gap-3 justify-between">
                    <!-- Bengali Number Badge -->
                    <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-md bg-[#1a4054] text-white text-xs sm:text-sm font-bold flex items-center justify-center shrink-0 shadow-2xs group-hover:bg-red-600 transition-colors">
                      {{ toBnNumber(idx + 1) }}
                    </span>

                    <!-- Title -->
                    <h4 class="text-xs sm:text-sm font-bold text-[#0f3460] group-hover:text-red-600 transition-colors line-clamp-3 leading-snug flex-1">
                      {{ item.title }}
                    </h4>

                    <!-- Right Thumbnail -->
                    <div class="w-20 sm:w-24 h-14 sm:h-16 rounded-md overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                      <img
                        :src="getPostThumb(item)"
                        :alt="item.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy"
                      />
                    </div>
                  </NuxtLink>
                </div>
              </div>
            </div>

            <!-- মধ্যবর্তী ডিভাইডার (ট্যাবলেট ও ডেস্কটপ) -->
            <div class="hidden md:block absolute top-0 bottom-0 left-1/2 -translate-x-1/2 w-[1px] bg-gray-200"></div>

            <!-- ডান কলাম: সর্বশেষ -->
            <div class="space-y-4 md:pl-4">
              <div class="border-b border-gray-200 pb-2.5">
                <h3 class="text-xl font-bold text-[#1b3b4f] tracking-tight">
                  সর্বশেষ
                </h3>
              </div>

              <div class="divide-y divide-gray-100">
                <div
                  v-for="item in bottomLatestNews"
                  :key="`latest-${item.id}`"
                  class="py-3 first:pt-0 last:pb-0 group"
                >
                  <NuxtLink :to="`/news/${item.slug}`" @click="scrollToTop" class="flex items-center gap-3 justify-between">
                    <!-- Title -->
                    <h4 class="text-xs sm:text-sm font-bold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-3 leading-snug flex-1">
                      {{ item.title }}
                    </h4>

                    <!-- Right Thumbnail -->
                    <div class="w-20 sm:w-24 h-14 sm:h-16 rounded-md overflow-hidden bg-gray-100 shrink-0 border border-gray-100">
                      <img
                        :src="getPostThumb(item)"
                        :alt="item.title"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        loading="lazy"
                      />
                    </div>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>
        </div>
      </article>

      <!-- ── Right Column: Sidebar (Desktop 4 Cols) ── -->
      <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24 self-start lg:max-h-[calc(100vh-7rem)] lg:overflow-y-auto sidebar-scroll pr-1">
        <!-- 1. Latest vs Popular Tabbed Widget -->
        <div v-if="sidebarConfig.show_tabs !== false" class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs">
          <!-- Tab Switcher -->
          <div class="flex border-b border-gray-100 pb-3 gap-2">
            <button
              @click="sidebarTab = 'latest'"
              class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all text-center"
              :class="sidebarTab === 'latest' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-500 hover:text-gray-800 bg-gray-50'"
            >
              🔥 {{ sidebarConfig.tabs_title_1 || 'সর্বশেষ সংবাদ' }}
            </button>
            <button
              @click="sidebarTab = 'popular'"
              class="flex-1 py-1.5 text-xs font-bold rounded-lg transition-all text-center"
              :class="sidebarTab === 'popular' ? 'bg-red-600 text-white shadow-xs' : 'text-gray-500 hover:text-gray-800 bg-gray-50'"
            >
              👁️ {{ sidebarConfig.tabs_title_2 || 'সর্বাধিক পঠিত' }}
            </button>
          </div>

          <!-- List Items -->
          <div class="divide-y divide-gray-100 pt-3">
            <div
              v-for="(item, index) in tabbedNews"
              :key="item.id"
              class="py-3 first:pt-1 last:pb-1 group"
            >
              <NuxtLink :to="`/news/${item.slug}`" class="flex gap-3 items-start">
                <span class="w-6 h-6 rounded-full bg-gray-100 group-hover:bg-red-50 text-gray-500 group-hover:text-red-600 font-bold text-xs flex items-center justify-center shrink-0 transition-colors">
                  {{ index + 1 }}
                </span>
                <div class="flex-1 min-w-0 space-y-1">
                  <h4 class="text-xs font-bold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-2 leading-relaxed">
                    {{ item.title }}
                  </h4>
                  <div class="flex items-center gap-2 text-[10px] text-gray-400">
                    <span v-if="item.category?.name" class="text-red-600 font-medium">{{ item.category.name }}</span>
                    <span>·</span>
                    <span>{{ formatTimeAgo(item.published_at) }}</span>
                  </div>
                </div>
              </NuxtLink>
            </div>

            <div v-if="tabbedNews.length === 0" class="py-6 text-center text-xs text-gray-400">
              কোনো সংবাদ পাওয়া যায়নি
            </div>
          </div>
        </div>

        <!-- 2. Related News (Same Category) -->
        <div v-if="sidebarConfig.show_related !== false && relatedPosts.length > 0" class="bg-white rounded-2xl p-5 border border-gray-200/80 shadow-xs space-y-3">
          <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-red-600"></span>
              {{ post.category ? `${post.category.name} বিভাগের ` : '' }}{{ sidebarConfig.related_title || 'আরও খবর' }}
            </h3>
            <NuxtLink
              v-if="post.category"
              :to="`/category/${post.category.slug}`"
              class="text-[11px] font-semibold text-red-600 hover:underline"
            >
              সবগুলো →
            </NuxtLink>
          </div>

          <div class="space-y-3">
            <div
              v-for="item in relatedPosts"
              :key="item.id"
              class="group pt-2 border-b border-gray-50 last:border-0 last:pb-0"
            >
              <NuxtLink :to="`/news/${item.slug}`" class="flex gap-3 items-center">
                <img
                  v-if="item.featured_image?.original || (typeof item.featured_image === 'string' && item.featured_image)"
                  :src="item.featured_image?.original || item.featured_image"
                  :alt="item.title"
                  class="w-16 h-12 rounded-lg object-cover bg-gray-100 shrink-0 group-hover:opacity-90"
                  loading="lazy"
                />
                <div class="flex-1 min-w-0">
                  <h4 class="text-xs font-bold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-2 leading-snug">
                    {{ item.title }}
                  </h4>
                  <span class="text-[10px] text-gray-400 mt-0.5 block">{{ formatTimeAgo(item.published_at) }}</span>
                </div>
              </NuxtLink>
            </div>
          </div>
        </div>

        <!-- 3. Sidebar Advertisement Banner Slot -->
        <div v-if="sidebarConfig.show_ad !== false" class="rounded-2xl overflow-hidden border border-gray-200/90 bg-white shadow-xs">
          <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 flex items-center justify-between text-[10px] uppercase font-bold text-gray-400">
            <span>{{ sidebarConfig.ad_title || 'বিজ্ঞাপন (Sponsored)' }}</span>
            <span class="text-gray-300">বিজ্ঞাপন স্থান</span>
          </div>
          <div class="p-3">
            <a
              v-if="sidebarConfig.ad_image"
              :href="sidebarConfig.ad_link || '#'"
              :target="sidebarConfig.ad_link ? '_blank' : undefined"
              class="block rounded-xl overflow-hidden group border border-gray-100 relative"
            >
              <img
                :src="sidebarConfig.ad_image"
                :alt="sidebarConfig.ad_title || 'Sidebar Ad'"
                class="w-full h-auto object-cover group-hover:scale-103 transition-transform duration-300"
              />
            </a>
            <div
              v-else
              class="py-6 px-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl text-center space-y-2 border border-dashed border-gray-200"
            >
              <component
                :is="sidebarConfig.ad_link ? 'a' : 'div'"
                :href="sidebarConfig.ad_link || undefined"
                :target="sidebarConfig.ad_link ? '_blank' : undefined"
                class="flex flex-col items-center justify-center text-gray-400 block"
              >
                <span class="text-3xl mb-1">📢</span>
                <span class="text-xs font-semibold text-gray-700">
                  {{ sidebarConfig.ad_text || 'আপনার ব্র্যান্ড বা পণ্যের বিজ্ঞাপন দিন' }}
                </span>
                <span class="text-[10px] text-gray-400 mt-1">
                  {{ sidebarConfig.ad_contact || 'যোগাযোগ: ads@newsportal.com' }}
                </span>
              </component>
            </div>
          </div>
        </div>

        <!-- 4. Quick Newsletter / Social Box -->
        <div v-if="sidebarConfig.show_newsletter !== false" class="bg-gradient-to-br from-red-600 to-red-700 text-white rounded-2xl p-5 shadow-xs space-y-3">
          <div class="space-y-1">
            <h4 class="text-sm font-bold">
              {{ sidebarConfig.newsletter_title || 'দৈনিক বাংলা ই-নিউজলেটার' }}
            </h4>
            <p class="text-[11px] text-red-100 leading-relaxed">
              {{ sidebarConfig.newsletter_desc || 'প্রতিদিনের সেরা ও বিশ্লেষণমূলক সংবাদের আপডেট পেতে সাবস্ক্রাইব করুন।' }}
            </p>
          </div>
          <div class="space-y-2">
            <input
              type="email"
              placeholder="আপনার ইমেইল লিখুন..."
              class="w-full px-3 py-2 text-xs rounded-xl bg-white/10 placeholder-red-200 border border-white/20 text-white focus:outline-none focus:bg-white focus:text-gray-900 transition-colors"
            />
            <button class="w-full py-2 bg-white hover:bg-gray-100 text-red-600 font-bold rounded-xl text-xs transition-colors shadow-xs">
              সাবস্ক্রাইব করুন
            </button>
          </div>
        </div>
      </aside>
    </div>

    <!-- Lightbox Modal for Photo Gallery -->
  <Teleport to="body">
    <div
      v-if="activeLightboxImage"
      class="fixed inset-0 z-50 bg-black/90 backdrop-blur-xs flex items-center justify-center p-4"
      @click.self="closeLightbox"
    >
      <button
        type="button"
        @click="closeLightbox"
        class="absolute top-4 right-4 text-white hover:text-red-400 bg-white/10 hover:bg-white/20 p-2.5 rounded-full transition-colors z-10"
        title="বন্ধ করুন (Esc)"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>

      <!-- Prev button -->
      <button
        v-if="post?.featured_image?.gallery?.length > 1"
        type="button"
        @click.stop="prevLightbox"
        class="absolute left-4 top-1/2 -translate-y-1/2 text-white bg-white/10 hover:bg-white/20 p-3 rounded-full transition-colors z-10"
        title="পূর্ববর্তী ছবি"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
      </button>

      <!-- Next button -->
      <button
        v-if="post?.featured_image?.gallery?.length > 1"
        type="button"
        @click.stop="nextLightbox"
        class="absolute right-4 top-1/2 -translate-y-1/2 text-white bg-white/10 hover:bg-white/20 p-3 rounded-full transition-colors z-10"
        title="পরবর্তী ছবি"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>

      <!-- Image & Caption Container -->
      <div class="max-w-4xl max-h-[90vh] flex flex-col items-center select-none">
        <img
          :src="activeLightboxImage.url"
          :alt="activeLightboxImage.caption || post.title"
          class="max-w-full max-h-[75vh] object-contain rounded-lg shadow-2xl"
        />
        <div class="mt-3 text-center text-white px-4">
          <span class="text-xs text-gray-400 font-mono block mb-1">
            ছবি {{ activeLightboxImage.index }} / {{ activeLightboxImage.total }}
          </span>
          <p v-if="activeLightboxImage.caption" class="text-sm font-medium">
            {{ activeLightboxImage.caption }}
          </p>
        </div>
      </div>
    </div>
  </Teleport>
  </div>
</template>

<script setup>
definePageMeta({
  key: route => route.fullPath,
})

const route = useRoute()
const config = useRuntimeConfig()

const slug = computed(() => String(route.params.slug || ''))
const sidebarTab = ref('latest')

// Global state caches for sidebar across article navigations
const globalSidebarConfig = useState('global-sidebar-config', () => null)
const globalSidebarPosts = useState('global-sidebar-posts', () => null)

// 1. Fetch current single post (Non-blocking instant routing with Nuxt payload/client caching)
const { data: postResponse, pending, refresh } = useFetch(() => `${config.public.apiBase}/posts/${slug.value}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: computed(() => `news-post-${slug.value}`),
  watch: [() => route.params.slug],
  lazy: true,
})

watch(() => route.params.slug, (newSlug, oldSlug) => {
  if (newSlug && newSlug !== oldSlug && process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    refresh()
  }
})

const post = computed(() => postResponse.value?.data || null)

const currentArticleCategorySlug = useState('current-article-category-slug', () => '')

watch(() => post.value?.category, (cat) => {
  if (cat?.slug) {
    currentArticleCategorySlug.value = cat.slug
  }
}, { immediate: true })

onUnmounted(() => {
  currentArticleCategorySlug.value = ''
})

// Intercept clicks on links inside article content for smooth in-app navigation and normalization
const handleContentClick = (event) => {
  const target = event.target
  if (!target) return
  const anchor = target.closest('a')
  if (!anchor) return

  const rawHref = anchor.getAttribute('href')
  if (!rawHref || rawHref.startsWith('#') || rawHref.startsWith('javascript:')) return

  let href = rawHref.trim()

  // Normalize relative or protocol-less links
  if (!href.startsWith('http://') && !href.startsWith('https://') && !href.startsWith('/') && !href.startsWith('mailto:') && !href.startsWith('tel:')) {
    if (href.startsWith('news/')) {
      href = '/' + href
    } else {
      href = '/news/' + href
    }
  }

  const currentOrigin = typeof window !== 'undefined' ? window.location.origin : ''
  const isInternal = href.startsWith('/') || (currentOrigin && href.startsWith(currentOrigin))

  if (isInternal) {
    let internalPath = href
    if (currentOrigin && internalPath.startsWith(currentOrigin)) {
      internalPath = internalPath.slice(currentOrigin.length)
    }

    if (internalPath.startsWith('/news/')) {
      event.preventDefault()
      const targetSlug = internalPath.replace(/^\/news\//, '').split('?')[0].split('#')[0]
      if (targetSlug) {
        navigateTo(`/news/${targetSlug}`)
      }
      return
    }

    if (internalPath.startsWith('/')) {
      event.preventDefault()
      navigateTo(internalPath)
      return
    }
  } else if (href.startsWith('http://') || href.startsWith('https://')) {
    event.preventDefault()
    window.open(href, '_blank', 'noopener,noreferrer')
  }
}

// 2. Fetch dynamic sidebar config from CMS (Non-blocking with cross-article cache)
const { data: sidebarConfigResponse } = useFetch(`${config.public.apiBase}/sidebar-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'single-news-sidebar-config',
  lazy: true,
  default: () => ({ success: true, data: globalSidebarConfig.value || {} }),
  getCachedData: (key, nuxtApp) => nuxtApp.payload.data[key] || nuxtApp.static.data[key] || (globalSidebarConfig.value ? { success: true, data: globalSidebarConfig.value } : undefined),
})

watch(() => sidebarConfigResponse.value?.data, (newData) => {
  if (newData && Object.keys(newData).length > 0) {
    globalSidebarConfig.value = newData
  }
}, { immediate: true })

const sidebarConfig = computed(() => sidebarConfigResponse.value?.data || globalSidebarConfig.value || {})

// 3. Fetch latest & popular posts for sidebar and bottom feed (Non-blocking with cross-article cache)
const { data: sidebarPostsResponse } = useFetch(`${config.public.apiBase}/posts?per_page=20`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'sidebar-recent-posts',
  lazy: true,
  default: () => ({ success: true, data: globalSidebarPosts.value || [] }),
  getCachedData: (key, nuxtApp) => nuxtApp.payload.data[key] || nuxtApp.static.data[key] || (globalSidebarPosts.value ? { success: true, data: globalSidebarPosts.value } : undefined),
})

watch(() => sidebarPostsResponse.value?.data, (newData) => {
  if (newData && Array.isArray(newData) && newData.length > 0) {
    globalSidebarPosts.value = newData
  }
}, { immediate: true })

const allPosts = computed(() => sidebarPostsResponse.value?.data || globalSidebarPosts.value || [])

// Bottom Feed computed lists (Popular & Latest)
const bottomPopularNews = computed(() => {
  const currentId = post.value?.id
  return [...allPosts.value]
    .filter(p => p.id !== currentId)
    .sort((a, b) => (b.view_count || 0) - (a.view_count || 0))
    .slice(0, 5)
})

const bottomLatestNews = computed(() => {
  const currentId = post.value?.id
  return allPosts.value
    .filter(p => p.id !== currentId)
    .slice(0, 5)
})

const toBnNumber = (n) => String(n).replace(/\d/g, d => '০১২৩৪৫৬৭৮৯'[d])

const getPostThumb = (p) => {
  return (
    p?.featured_image?.thumbnail ||
    p?.featured_image?.url ||
    p?.featured_image?.original ||
    (typeof p?.featured_image === 'string' ? p?.featured_image : '') ||
    'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=300&auto=format&fit=crop'
  )
}

const scrollToTop = () => {
  if (process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

// Tabbed news list: latest vs most read (sorted by view_count)
const tabbedNews = computed(() => {
  const currentId = post.value?.id
  const filtered = allPosts.value.filter(p => p.id !== currentId)
  const maxCount = sidebarConfig.value.tabs_count || 5
  if (sidebarTab.value === 'popular') {
    return [...filtered].sort((a, b) => (b.view_count || 0) - (a.view_count || 0)).slice(0, maxCount)
  }
  return filtered.slice(0, maxCount)
})

// Related posts from same category
const relatedPosts = computed(() => {
  const currentCatId = post.value?.category?.id
  const currentId = post.value?.id
  if (!currentCatId) return []
  const maxCount = sidebarConfig.value.related_count || 4
  return allPosts.value
    .filter(p => p.category?.id === currentCatId && p.id !== currentId)
    .slice(0, maxCount)
})

// Non-blocking fire-and-forget view count recording
const recordPostView = (id) => {
  if (!process.client || !id) return
  const url = `${config.public.apiBase}/posts/${id}/view`
  try {
    fetch(url, {
      method: 'POST',
      headers: { 'X-Portal-Key': config.public.portalKey },
      keepalive: true,
    }).catch(() => {})
  } catch {}
}

watch(() => post.value?.id, (newId) => {
  if (newId && process.client) {
    recordPostView(newId)
  }
})

const loadSocialWidgets = () => {
  if (process.client) {
    nextTick(() => {
      if (document.querySelector('.twitter-tweet') || post.value?.body?.includes('twitter-tweet')) {
        if (!window.twttr) {
          const s = document.createElement('script')
          s.src = 'https://platform.twitter.com/widgets.js'
          s.async = true
          s.charset = 'utf-8'
          document.head.appendChild(s)
        } else {
          window.twttr?.widgets?.load?.()
        }
      }
    })
  }
}

const adjustFacebookEmbeds = () => {
  if (!process.client) return
  const iframes = document.querySelectorAll('.article-content iframe')
  if (!iframes || iframes.length === 0) return

  iframes.forEach((iframe) => {
    const src = iframe.getAttribute('src') || ''
    const isFacebook = src.includes('facebook.com') || src.includes('fb.watch')
    if (!isFacebook) return

    const orientationAttr = iframe.getAttribute('data-orientation')
    const embedType = iframe.getAttribute('data-embed-type')

    let determinedOrientation = orientationAttr

    if (!determinedOrientation) {
      if (iframe.classList.contains('facebook-video-vertical') || embedType === 'facebook-reel') {
        determinedOrientation = 'vertical'
      } else if (iframe.classList.contains('facebook-video-square')) {
        determinedOrientation = 'square'
      } else if (iframe.classList.contains('facebook-video-landscape') || embedType === 'facebook-video') {
        determinedOrientation = 'landscape'
      } else {
        // Dimension inspect
        let w = parseFloat(iframe.getAttribute('width'))
        let h = parseFloat(iframe.getAttribute('height'))

        if (isNaN(w) || isNaN(h)) {
          try {
            const urlObj = new URL(src, window.location.origin)
            const qw = parseFloat(urlObj.searchParams.get('width'))
            const qh = parseFloat(urlObj.searchParams.get('height'))
            if (!isNaN(qw) && !isNaN(qh)) {
              w = qw
              h = qh
            }
          } catch {}
        }

        if (!isNaN(w) && !isNaN(h) && w > 0 && h > 0) {
          if (h > w * 1.3) {
            determinedOrientation = 'vertical'
          } else if (Math.abs(h - w) <= (w * 0.15)) {
            determinedOrientation = 'square'
          } else {
            determinedOrientation = 'landscape'
          }
        } else {
          determinedOrientation = 'landscape'
        }
      }
    }

    if (determinedOrientation === 'vertical') {
      iframe.classList.remove('facebook-video-landscape', 'facebook-video-square')
      iframe.classList.add('facebook-video', 'facebook-video-vertical')
      iframe.setAttribute('data-orientation', 'vertical')
      iframe.setAttribute('data-embed-type', 'facebook-reel')
      iframe.style.setProperty('aspect-ratio', '9 / 16', 'important')
      iframe.style.setProperty('width', '100%', 'important')
      iframe.style.setProperty('max-width', '380px', 'important')
      iframe.style.setProperty('height', 'auto', 'important')
      iframe.style.setProperty('min-height', 'auto', 'important')
      iframe.style.setProperty('border', 'none', 'important')
      iframe.style.setProperty('border-radius', '0.75rem', 'important')
      iframe.style.setProperty('margin', '1.5rem auto', 'important')
      iframe.style.setProperty('display', 'block', 'important')
      iframe.style.setProperty('background-color', '#000', 'important')
      iframe.style.setProperty('overflow', 'hidden', 'important')
    } else if (determinedOrientation === 'square') {
      iframe.classList.remove('facebook-video-landscape', 'facebook-video-vertical')
      iframe.classList.add('facebook-video', 'facebook-video-square')
      iframe.setAttribute('data-orientation', 'square')
      iframe.setAttribute('data-embed-type', 'facebook-video')
      iframe.style.setProperty('aspect-ratio', '1 / 1', 'important')
      iframe.style.setProperty('width', '100%', 'important')
      iframe.style.setProperty('max-width', '500px', 'important')
      iframe.style.setProperty('height', 'auto', 'important')
      iframe.style.setProperty('min-height', 'auto', 'important')
      iframe.style.setProperty('border', 'none', 'important')
      iframe.style.setProperty('border-radius', '0.75rem', 'important')
      iframe.style.setProperty('margin', '1.5rem auto', 'important')
      iframe.style.setProperty('display', 'block', 'important')
      iframe.style.setProperty('overflow', 'hidden', 'important')
    } else {
      // Landscape (16:9)
      iframe.classList.remove('facebook-video-vertical', 'facebook-video-square')
      iframe.classList.add('facebook-video', 'facebook-video-landscape')
      iframe.setAttribute('data-orientation', 'landscape')
      iframe.setAttribute('data-embed-type', 'facebook-video')
      iframe.style.setProperty('aspect-ratio', '16 / 9', 'important')
      iframe.style.setProperty('width', '100%', 'important')
      iframe.style.setProperty('max-width', '680px', 'important')
      iframe.style.setProperty('height', 'auto', 'important')
      iframe.style.setProperty('min-height', 'auto', 'important')
      iframe.style.setProperty('border', 'none', 'important')
      iframe.style.setProperty('border-radius', '0.75rem', 'important')
      iframe.style.setProperty('margin', '1.5rem auto', 'important')
      iframe.style.setProperty('display', 'block', 'important')
      iframe.style.setProperty('overflow', 'hidden', 'important')
    }
  })
}

// 4. Reader Experience (Font Size Controller & Reading Progress)
const currentFontSize = ref('normal')

const setFontSize = (size) => {
  currentFontSize.value = size
  if (process.client) {
    localStorage.setItem('newsportal_font_size', size)
  }
}

const fontSizeStyle = computed(() => {
  if (currentFontSize.value === 'small') {
    return {
      fontSize: '1rem',
      lineHeight: '1.75'
    }
  }
  if (currentFontSize.value === 'large') {
    return {
      fontSize: '1.375rem',
      lineHeight: '2.05'
    }
  }
  return {
    fontSize: '1.125rem',
    lineHeight: '1.85'
  }
})

const readingProgress = ref(0)

const updateReadingProgress = () => {
  if (!process.client) return
  const winScroll = document.documentElement.scrollTop || document.body.scrollTop
  const height = document.documentElement.scrollHeight - document.documentElement.clientHeight
  if (height > 0) {
    const scrolled = (winScroll / height) * 100
    readingProgress.value = Math.min(100, Math.max(0, Math.round(scrolled)))
  } else {
    readingProgress.value = 0
  }
}

onMounted(() => {
  loadSocialWidgets()
  nextTick(() => {
    adjustFacebookEmbeds()
  })
  if (post.value?.id) {
    recordPostView(post.value.id)
  }
  if (process.client) {
    const saved = localStorage.getItem('newsportal_font_size')
    if (saved && ['small', 'normal', 'large'].includes(saved)) {
      currentFontSize.value = saved
    }
    window.addEventListener('scroll', updateReadingProgress, { passive: true })
    updateReadingProgress()
  }
})

onUnmounted(() => {
  if (process.client) {
    window.removeEventListener('scroll', updateReadingProgress)
  }
})

watch(() => post.value?.body, () => {
  loadSocialWidgets()
  nextTick(() => {
    adjustFacebookEmbeds()
  })
})

const formatDate = (dateStr) => {
  try {
    return new Date(dateStr).toLocaleDateString('bn-BD', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
  } catch {
    return 'আজ'
  }
}

const formatTimeAgo = (dateStr) => {
  try {
    const diff = Date.now() - new Date(dateStr).getTime()
    const mins = Math.floor(diff / 60000)
    if (mins < 60) return `${mins > 0 ? mins : 1} মিনিট আগে`
    const hours = Math.floor(mins / 60)
    if (hours < 24) return `${hours} ঘণ্টা আগে`
    return new Date(dateStr).toLocaleDateString('bn-BD', { month: 'short', day: 'numeric' })
  } catch {
    return 'কিছুক্ষণ আগে'
  }
}

const copyLink = async () => {
  if (process.client) {
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(window.location.href)
      } else {
        const textarea = document.createElement('textarea')
        textarea.value = window.location.href
        textarea.style.position = 'fixed'
        textarea.style.opacity = '0'
        document.body.appendChild(textarea)
        textarea.select()
        document.execCommand('copy')
        document.body.removeChild(textarea)
      }
      alert('সংবাদের লিংক কপি করা হয়েছে!')
    } catch {
      alert('লিংক কপি করা সম্ভব হয়নি।')
    }
  }
}

const share = (platform) => {
  const url = encodeURIComponent(window.location.href)
  if (platform === 'fb') {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
  } else {
    window.open(`https://twitter.com/intent/tweet?url=${url}`, '_blank')
  }
}

// ─── Multi-Image Gallery & Lightbox Logic ────────────────────────────────────
const lightboxIndex = ref(null)

const galleryGridClass = computed(() => {
  const count = post.value?.featured_image?.gallery?.length || 0
  if (count === 1) return 'grid grid-cols-1 max-w-lg mx-auto gap-4'
  if (count === 2) return 'grid grid-cols-1 sm:grid-cols-2 gap-4'
  if (count === 3) return 'grid grid-cols-1 sm:grid-cols-3 gap-4'
  return 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4'
})

const galleryImageHeightClass = computed(() => {
  const count = post.value?.featured_image?.gallery?.length || 0
  if (count === 1) return 'h-72 sm:h-80'
  if (count === 2) return 'h-60 sm:h-72'
  if (count === 3) return 'h-48 sm:h-56'
  return 'h-44 sm:h-48'
})

const activeLightboxImage = computed(() => {
  if (lightboxIndex.value === null) return null
  const gallery = post.value?.featured_image?.gallery || []
  const item = gallery[lightboxIndex.value]
  if (!item) return null
  return {
    url: item.url || item,
    caption: item.caption || '',
    index: lightboxIndex.value + 1,
    total: gallery.length,
  }
})

const openLightbox = (index) => {
  lightboxIndex.value = index
}

const closeLightbox = () => {
  lightboxIndex.value = null
}

const prevLightbox = () => {
  const gallery = post.value?.featured_image?.gallery || []
  if (gallery.length === 0) return
  if (lightboxIndex.value === 0) {
    lightboxIndex.value = gallery.length - 1
  } else {
    lightboxIndex.value--
  }
}

const nextLightbox = () => {
  const gallery = post.value?.featured_image?.gallery || []
  if (gallery.length === 0) return
  if (lightboxIndex.value === gallery.length - 1) {
    lightboxIndex.value = 0
  } else {
    lightboxIndex.value++
  }
}

const handleLightboxKeydown = (e) => {
  if (lightboxIndex.value === null) return
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowLeft') prevLightbox()
  if (e.key === 'ArrowRight') nextLightbox()
}

onMounted(() => {
  if (process.client) {
    window.addEventListener('keydown', handleLightboxKeydown)
  }
})

onBeforeUnmount(() => {
  if (process.client) {
    window.removeEventListener('keydown', handleLightboxKeydown)
  }
})
</script>

<style>
/* 1. Landscape Videos (16:9) */
.article-content iframe.facebook-video-landscape,
.article-content iframe[data-embed-type="facebook-video"],
.article-content iframe[data-orientation="landscape"],
.article-content iframe[data-embed-type="youtube"],
.article-content iframe[src*="facebook.com/plugins/video"]:not([data-orientation="vertical"]):not([data-orientation="square"]):not(.facebook-video-vertical):not(.facebook-video-square),
.article-content iframe[src*="plugins/video.php"]:not([data-orientation="vertical"]):not([data-orientation="square"]):not(.facebook-video-vertical):not(.facebook-video-square),
.article-content iframe.facebook-video:not(.facebook-video-vertical):not(.facebook-video-square):not([data-orientation="vertical"]):not([data-orientation="square"]) {
  aspect-ratio: 16 / 9 !important;
  width: 100% !important;
  max-width: 680px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  border: none !important;
  overflow: hidden !important;
}

/* 2. Facebook Vertical Videos / Reels (9:16) */
.article-content iframe.facebook-video-vertical,
.article-content iframe[data-embed-type="facebook-reel"],
.article-content iframe[data-orientation="vertical"] {
  aspect-ratio: 9 / 16 !important;
  width: 100% !important;
  max-width: 380px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  border: none !important;
  overflow: hidden !important;
  background-color: #000 !important;
}

/* 3. Facebook Square Videos (1:1) */
.article-content iframe.facebook-video-square,
.article-content iframe[data-embed-type="facebook-square"],
.article-content iframe[data-orientation="square"] {
  aspect-ratio: 1 / 1 !important;
  width: 100% !important;
  max-width: 520px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  border: none !important;
  overflow: hidden !important;
}

/* 4. YouTube Shorts (Kept untouched and separate) */
.article-content iframe[src*="youtube.com/shorts/"],
.article-content iframe[data-embed-type="youtube-shorts"] {
  aspect-ratio: 9 / 16 !important;
  width: 100% !important;
  max-width: 380px !important;
  min-height: 560px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  overflow: hidden !important;
}

/* Mobile Responsive Adjustments */
@media (max-width: 640px) {
  .article-content iframe.facebook-video-vertical,
  .article-content iframe[data-embed-type="facebook-reel"],
  .article-content iframe[data-orientation="vertical"] {
    max-width: min(100%, 340px) !important;
    width: 100% !important;
    aspect-ratio: 9 / 16 !important;
    height: auto !important;
  }

  .article-content iframe.facebook-video-square,
  .article-content iframe[data-embed-type="facebook-square"],
  .article-content iframe[data-orientation="square"] {
    max-width: 100% !important;
    width: 100% !important;
    aspect-ratio: 1 / 1 !important;
    height: auto !important;
  }

  .article-content iframe.facebook-video-landscape,
  .article-content iframe[data-orientation="landscape"] {
    max-width: 100% !important;
    width: 100% !important;
    aspect-ratio: 16 / 9 !important;
    height: auto !important;
  }

  .article-content iframe[src*="youtube.com/shorts/"],
  .article-content iframe[data-embed-type="youtube-shorts"] {
    max-width: 340px !important;
    min-height: 500px !important;
  }
}
</style>
