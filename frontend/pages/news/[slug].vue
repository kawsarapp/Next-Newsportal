<template>
  <div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-500">
      <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
      <span>/</span>
      <NuxtLink
        v-if="post?.category"
        :to="`/${post.category.slug}`"
        class="text-red-600 font-medium hover:underline"
      >
        {{ post.category.name }}
      </NuxtLink>
      <span v-else class="text-red-600 font-medium">জাতীয়</span>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="pending" class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-pulse">
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
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-50 text-red-600 font-bold px-2.5 py-1 rounded-full">
              {{ post.category?.name || 'বিশেষ সংবাদ' }}
            </span>
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
        </div>

        <!-- Excerpt Highlight -->
        <div v-if="post.excerpt" class="p-4 bg-gray-50 border-l-4 border-red-600 rounded-r-xl text-sm font-semibold text-gray-700 leading-relaxed italic">
          {{ post.excerpt }}
        </div>

        <!-- Featured Image -->
        <div v-if="post.featured_image?.original || (typeof post.featured_image === 'string' && post.featured_image)" class="rounded-2xl overflow-hidden bg-gray-100 border border-gray-200">
          <img
            :src="post.featured_image?.original || post.featured_image"
            :alt="post.featured_image?.alt || post.title"
            class="w-full h-auto max-h-[500px] object-cover"
          />
          <div v-if="post.featured_image?.caption" class="px-4 py-2 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 italic">
            📷 {{ post.featured_image.caption }}
          </div>
        </div>

        <!-- Main Body HTML -->
        <div v-if="post.body" class="article-content pt-2" v-html="post.body"></div>
        <div v-else-if="post.excerpt" class="article-content pt-2 text-gray-700">
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

        <!-- Tags List -->
        <div v-if="post.tags && post.tags.length > 0" class="pt-4 border-t border-gray-100 flex items-center gap-2 flex-wrap">
          <span class="text-xs text-gray-400 font-semibold">ট্যাগ:</span>
          <span
            v-for="tag in post.tags"
            :key="tag.id || tag"
            class="px-2.5 py-1 bg-gray-100 hover:bg-red-50 hover:text-red-600 text-gray-600 rounded-lg text-xs font-medium transition-colors"
          >
            #{{ tag.name || tag }}
          </span>
        </div>

        <!-- Photo Gallery Section (if available) -->
        <div v-if="post.featured_image?.gallery && post.featured_image.gallery.length > 0" class="pt-6 border-t border-gray-100 space-y-4">
          <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span>📸</span> ফটো গ্যালারি
          </h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <div
              v-for="(item, i) in post.featured_image.gallery"
              :key="i"
              class="group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 shadow-xs hover:shadow-md transition-shadow"
            >
              <a :href="item.url || item" target="_blank" class="block overflow-hidden relative">
                <img
                  :src="item.url || item"
                  :alt="item.caption || post.title"
                  class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300"
                  loading="lazy"
                />
              </a>
              <div v-if="item.caption" class="p-2.5 text-xs text-gray-600 bg-white border-t border-gray-100">
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
              :to="`/${post.category.slug}`"
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
  </div>
</template>

<script setup>
const route = useRoute()
const config = useRuntimeConfig()

const slug = computed(() => String(route.params.slug || ''))
const sidebarTab = ref('latest')

// 1. Fetch current single post
const { data: postResponse, pending } = await useFetch(() => `${config.public.apiBase}/posts/${slug.value}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: () => `news-post-${slug.value}`,
})

const post = computed(() => postResponse.value?.data || null)

// 2. Fetch dynamic sidebar config from CMS
const { data: sidebarConfigResponse } = await useFetch(`${config.public.apiBase}/sidebar-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'single-news-sidebar-config',
  default: () => ({ success: true, data: {} }),
})

const sidebarConfig = computed(() => sidebarConfigResponse.value?.data || {})

// 3. Fetch latest & popular posts for sidebar
const { data: sidebarPostsResponse } = await useFetch(`${config.public.apiBase}/posts?per_page=15`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'sidebar-recent-posts',
  default: () => ({ success: true, data: [] }),
})

const allPosts = computed(() => sidebarPostsResponse.value?.data || [])

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

// Record view count
watch(() => post.value?.id, (id) => {
  if (id) {
    fetch(`${config.public.apiBase}/posts/${id}/view`, {
      method: 'POST',
      headers: { 'X-Portal-Key': config.public.portalKey }
    }).catch(() => {})
  }
}, { immediate: true })

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

onMounted(() => {
  loadSocialWidgets()
})

watch(() => post.value?.body, () => {
  loadSocialWidgets()
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

const copyLink = () => {
  navigator.clipboard.writeText(window.location.href)
  alert('সংবাদের লিংক কপি করা হয়েছে!')
}

const share = (platform) => {
  const url = encodeURIComponent(window.location.href)
  if (platform === 'fb') {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank')
  } else {
    window.open(`https://twitter.com/intent/tweet?url=${url}`, '_blank')
  }
}
</script>
