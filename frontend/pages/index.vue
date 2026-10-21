<template>
  <div class="space-y-10">
    <!-- Top Sports Scoreboard Bar (Plugin Dynamic Integration) -->
    <div v-if="sportsScore" class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-xs flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full font-bold">🏏 লাইভ স্কোর</span>
        <span class="text-sm font-bold text-gray-800">{{ sportsScore.match }}</span>
      </div>
      <div class="flex items-center gap-4 text-xs font-semibold">
        <span class="text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg">
          {{ sportsScore.score?.team_a?.name }}: {{ sportsScore.score?.team_a?.runs }}/{{ sportsScore.score?.team_a?.wickets }} ({{ sportsScore.score?.team_a?.overs }})
        </span>
        <span class="text-blue-700 bg-blue-50 px-2 py-1 rounded-lg">
          {{ sportsScore.score?.team_b?.name }}: {{ sportsScore.score?.team_b?.runs }}/{{ sportsScore.score?.team_b?.wickets }} ({{ sportsScore.score?.team_b?.overs }})
        </span>
        <span class="text-gray-500 hidden sm:inline">{{ sportsScore.status }}</span>
      </div>
    </div>

    <!-- Floating subtle refresh indicator when logo is clicked -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 -translate-y-2 scale-95"
    >
      <div
        v-if="isRefreshingNews"
        class="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-red-600 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-xl flex items-center gap-2 pointer-events-none"
      >
        <svg class="animate-spin h-3.5 w-3.5 text-white" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span>সর্বশেষ সংবাদ আপডেট হচ্ছে...</span>
      </div>
    </Transition>

    <!-- Loading Skeleton (Initial load only) -->
    <div v-if="pending && (!posts || posts.length === 0)" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-pulse">
      <div class="lg:col-span-2 h-96 bg-gray-200 rounded-2xl"></div>
      <div class="space-y-4">
        <div class="h-28 bg-gray-200 rounded-xl"></div>
        <div class="h-28 bg-gray-200 rounded-xl"></div>
        <div class="h-28 bg-gray-200 rounded-xl"></div>
      </div>
    </div>

    <!-- Error fallback -->
    <div v-else-if="error" class="bg-red-50 p-6 rounded-2xl border border-red-200 text-center text-red-700 text-sm">
      সংবাদ লোড করতে সমস্যা হয়েছে। CMS API সংযোগ পরীক্ষা করুন।
    </div>

    <!-- Empty fallback -->
    <div v-else-if="posts.length === 0" class="bg-white rounded-2xl p-16 text-center text-gray-400 border border-gray-100 shadow-sm space-y-2">
      <div class="text-4xl mb-2">📰</div>
      <p class="text-base font-bold text-gray-700">বর্তমানে কোনো সংবাদ পাওয়া যায়নি</p>
      <p class="text-xs text-gray-400">ড্যাশবোর্ড থেকে নতুন সংবাদ তৈরি বা প্রকাশ করুন</p>
    </div>

    <!-- Dynamic Sections Rendering Engine -->
    <div v-else class="space-y-12">
      <template v-for="(section, sIdx) in activeSections" :key="section.id">
        <!-- 1. Hero / Lead Section -->
        <section v-if="section.section_type === 'hero'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Lead Story (Left 2 cols) -->
          <div v-if="leadPost" class="lg:col-span-2 group">
            <NuxtLink :to="`/news/${leadPost.slug}`" class="block space-y-3">
              <div class="h-80 sm:h-96 w-full rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 shadow-xs relative">
                <img
                  :src="leadPost.featured_image?.original || (typeof leadPost.featured_image === 'string' ? leadPost.featured_image : '') || 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=1200&auto=format&fit=crop'"
                  :alt="leadPost.title"
                  loading="eager"
                  fetchpriority="high"
                  decoding="async"
                  class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-300"
                />
                <div class="absolute top-4 left-4 flex gap-2">
                  <span v-if="leadPost.is_breaking" class="bg-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm">ব্রেকিং</span>
                  <span v-if="leadPost.category?.name" class="bg-white/90 text-gray-900 text-xs font-semibold px-2.5 py-1 rounded-full shadow-sm">{{ leadPost.category.name }}</span>
                </div>
              </div>

              <h2 class="text-2xl sm:text-3xl font-black text-gray-900 leading-snug group-hover:text-red-600 transition-colors">
                {{ leadPost.title }}
              </h2>

              <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
                {{ leadPost.excerpt }}
              </p>

              <div class="flex items-center gap-3 text-xs text-gray-400">
                <span>{{ leadPost.author?.name || 'অনলাইন ডেস্ক' }}</span>
                <span>·</span>
                <span>{{ leadPost.published_at ? formatDate(leadPost.published_at) : 'আজ' }}</span>
                <span>·</span>
                <span>⏱️ {{ leadPost.reading_time || 2 }} মিনিট পাঠ</span>
              </div>
            </NuxtLink>
          </div>

          <!-- Secondary Stories (Right 1 col) -->
          <div class="space-y-5">
            <h3 class="font-bold text-gray-900 text-sm pb-2 border-b-2 border-red-600 flex items-center justify-between">
              <span>{{ section.title || 'সর্বশেষ প্রধান খবর' }}</span>
              <span class="w-2 h-2 rounded-full bg-red-600"></span>
            </h3>

            <div class="space-y-4">
              <div
                v-for="post in secondaryPosts"
                :key="post.id"
                class="group pb-4 border-b border-gray-100 last:border-none"
              >
                <NuxtLink :to="`/news/${post.slug}`" class="flex gap-3 items-start">
                  <div class="w-24 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                    <img
                      :src="post.featured_image?.thumbnail || post.featured_image?.medium || post.featured_image?.original || (typeof post.featured_image === 'string' ? post.featured_image : '') || 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=400&auto=format&fit=crop'"
                      :alt="post.title"
                      loading="lazy"
                      decoding="async"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                    />
                  </div>
                  <div class="flex-1 min-w-0">
                    <span class="text-[11px] font-semibold text-red-600 block">{{ post.category?.name }}</span>
                    <h4 class="text-xs sm:text-sm font-bold text-gray-800 line-clamp-2 group-hover:text-red-600 transition-colors leading-snug">
                      {{ post.title }}
                    </h4>
                    <span class="text-[10px] text-gray-400 mt-1 block">👁️ {{ post.view_count || 0 }} ভিউ</span>
                  </div>
                </NuxtLink>
              </div>
            </div>
          </div>
        </section>

        <!-- 2. Advertisement Banner Section -->
        <section v-else-if="section.section_type === 'ad'" class="w-full">
          <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-gray-900 via-slate-800 to-gray-900 border border-gray-700/60 shadow-md">
            <span class="absolute top-2 right-3 text-[9px] uppercase tracking-wider text-gray-400/80 font-bold bg-black/40 px-2 py-0.5 rounded z-10">বিজ্ঞাপন (Sponsored)</span>
            <a
              v-if="section.config?.banner_image"
              :href="section.config?.link_url || '#'"
              :target="section.config?.link_url ? '_blank' : undefined"
              class="block w-full h-28 sm:h-36 relative overflow-hidden group"
            >
              <img
                :src="section.config.banner_image"
                :alt="section.config?.alt_text || section.title || 'বিজ্ঞাপন'"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
              />
            </a>
            <div
              v-else
              class="h-28 sm:h-32 p-4 sm:p-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-white"
            >
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-amber-500 flex items-center justify-center text-2xl shadow-md shrink-0">
                  ⚡
                </div>
                <div>
                  <h4 class="font-extrabold text-sm sm:text-base tracking-tight text-white">
                    {{ section.title || 'আপনার ব্র্যান্ড ও সেবার প্রচার বাড়ান' }}
                  </h4>
                  <p class="text-xs text-gray-300 mt-0.5 line-clamp-1">
                    লক্ষাধিক সক্রিয় পাঠকের কাছে আপনার পণ্য পৌঁছে দিন — দৈনিক বাংলা অ্যাড নেটওয়ার্ক
                  </p>
                </div>
              </div>
              <a
                :href="section.config?.link_url || 'mailto:ads@dainikbangla.com'"
                class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all shadow-md shrink-0 flex items-center gap-1.5"
              >
                বিজ্ঞাপন দিন <span>→</span>
              </a>
            </div>
          </div>
        </section>

        <!-- 3. Split Featured Section (2 Columns) -->
        <section v-else-if="section.layout_type === 'split'" class="space-y-5">
          <div class="flex items-center justify-between border-b border-gray-200 pb-2">
            <h3 class="text-xl font-bold text-gray-900 border-l-4 border-amber-600 pl-3">
              {{ section.title }}
            </h3>
            <NuxtLink v-if="section.category" :to="`/category/${section.category.slug}`" class="text-xs text-red-600 font-semibold hover:underline">
              আরও দেখুন →
            </NuxtLink>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="post in getSectionPosts(section).slice(0, section.posts_count || 4)"
              :key="post.id"
              class="bg-white rounded-2xl p-4 border border-gray-200 shadow-xs hover:shadow-md transition-shadow group flex flex-col justify-between"
            >
              <NuxtLink :to="`/news/${post.slug}`" class="space-y-3 block">
                <div class="h-48 w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                  <img
                    :src="post.featured_image?.thumbnail || post.featured_image?.medium || post.featured_image?.original || (typeof post.featured_image === 'string' ? post.featured_image : '') || 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600&auto=format&fit=crop'"
                    :alt="post.title"
                    loading="lazy"
                    decoding="async"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                  />
                </div>
                <span class="text-xs font-semibold text-amber-600">{{ post.category?.name }}</span>
                <h4 class="font-bold text-base text-gray-900 line-clamp-2 leading-snug group-hover:text-red-600 transition-colors">
                  {{ post.title }}
                </h4>
                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                  {{ post.excerpt }}
                </p>
              </NuxtLink>

              <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-400 mt-3 flex items-center justify-between">
                <span>{{ post.author?.name || 'অনলাইন ডেস্ক' }}</span>
                <span>👁️ {{ post.view_count || 0 }}</span>
              </div>
            </div>
          </div>
        </section>

          <!-- 4. Default / Category Grid (3 Columns) -->
          <section v-else class="space-y-6">
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
              <h3 class="text-xl font-bold text-gray-900 border-l-4 border-red-600 pl-3">
                {{ section.title }}
              </h3>
              <NuxtLink v-if="section.category" :to="`/category/${section.category.slug}`" class="text-xs text-red-600 font-semibold hover:underline">
                সকল সংবাদ →
              </NuxtLink>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              <div
                v-for="post in getSectionPosts(section).slice(0, section.posts_count || 6)"
                :key="post.id"
                class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-xs hover:shadow-md transition-shadow group flex flex-col justify-between"
              >
                <NuxtLink :to="`/news/${post.slug}`" class="space-y-3 block">
                  <div class="h-44 w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                    <img
                      :src="post.featured_image?.thumbnail || post.featured_image?.medium || post.featured_image?.original || (typeof post.featured_image === 'string' ? post.featured_image : '') || 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600&auto=format&fit=crop'"
                      :alt="post.title"
                      loading="lazy"
                      decoding="async"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                    />
                  </div>
                  <span class="text-xs font-semibold text-red-600">{{ post.category?.name }}</span>
                  <h4 class="font-bold text-sm text-gray-800 line-clamp-2 leading-snug group-hover:text-red-600 transition-colors">
                    {{ post.title }}
                  </h4>
                  <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                    {{ post.excerpt }}
                  </p>
                </NuxtLink>

                <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-400 mt-3 flex items-center justify-between">
                  <span>{{ post.author?.name || 'অনলাইন ডেস্ক' }}</span>
                  <span>👁️ {{ post.view_count || 0 }}</span>
                </div>
              </div>
            </div>
          </section>

          <!-- YouTube Live & Video Gallery Section (Rendered ONCE at configured position, e.g. top or middle) -->
          <HomeYoutubeSection
            v-if="shouldRenderYoutubeAt(sIdx)"
            :yt-config="ytConfig"
            :yt-live-id="ytLiveId"
            :yt-videos="ytVideos"
          />
        </template>

        <!-- YouTube Live & Video Gallery Section (Rendered ONCE at bottom if configured at bottom) -->
        <HomeYoutubeSection
          v-if="shouldRenderYoutubeAtBottom"
          :yt-config="ytConfig"
          :yt-live-id="ytLiveId"
          :yt-videos="ytVideos"
        />
      </div>
    </div>
  </template>

<script setup>
import { onMounted, onUnmounted, watch } from 'vue'

definePageMeta({
  key: route => route.fullPath,
})

const config = useRuntimeConfig()
const refreshTrigger = useState('home-refresh-trigger', () => 0)
const isRefreshingNews = useState('is-refreshing-news', () => false)

// Watch logo click trigger to reload news repeatedly without full screen reload
watch(refreshTrigger, async (newVal, oldVal) => {
  if (newVal > oldVal) {
    await Promise.allSettled([
      refreshPosts(),
      refreshFeatured(),
    ])
  }
})

// 1. Fetch live posts & featured posts (Instant payload caching for fast navigation)
const { data: postsResponse, pending, error, refresh: refreshPosts } = useFetch(`${config.public.apiBase}/posts?per_page=20`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'home-posts-lead',
  lazy: true,
})

const { data: featuredResponse, refresh: refreshFeatured } = useFetch(`${config.public.apiBase}/posts/featured?limit=6`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'home-posts-featured',
  lazy: true,
  default: () => ({ data: [] }),
})

const posts = computed(() => postsResponse.value?.data || [])
const featuredPosts = computed(() => {
  const list = featuredResponse.value?.data || []
  return list.length > 0 ? list : posts.value
})

const leadPost = computed(() => featuredPosts.value[0] || posts.value[0] || null)
const secondaryPosts = computed(() => {
  if (featuredPosts.value.length > 1) {
    return featuredPosts.value.slice(1, 4)
  }
  return posts.value.slice(1, 4)
})

// 2. Fetch Dynamic Homepage Sections from CMS Builder
const { data: sectionsResponse } = useFetch(`${config.public.apiBase}/homepage-sections`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'home-dynamic-sections',
  lazy: true,
  default: () => ({ success: true, data: [] }),
})

// Fallback default sections if none configured yet
const defaultSections = [
  { id: 1, title: 'প্রধান লিড ও ব্রেকিং নিউজ গ্রিড', section_type: 'hero', layout_type: 'grid', posts_count: 4, is_active: true },
  { id: 2, title: 'বিজ্ঞাপন স্লট', section_type: 'ad', layout_type: 'banner', posts_count: 1, is_active: true },
  { id: 3, title: 'অন্যান্য গুরুত্বপূর্ণ সংবাদ', section_type: 'grid', layout_type: 'grid', posts_count: 6, is_active: true },
]

const activeSections = computed(() => {
  const dynamic = sectionsResponse.value?.data
  if (dynamic && dynamic.length > 0) {
    return dynamic.filter(s => s.is_active)
  }
  return defaultSections.filter(s => s.is_active)
})

// Helper to filter posts by section category
function getSectionPosts(section) {
  if (section.category_id || section.category?.id) {
    const targetCatId = Number(section.category_id || section.category?.id)
    const filtered = posts.value.filter(p => {
      if (Number(p.category?.id) === targetCatId) return true
      if (p.secondary_categories?.some(sc => Number(sc.id) === targetCatId)) return true
      if (Array.isArray(p.additional_category_ids) && p.additional_category_ids.map(Number).includes(targetCatId)) return true
      return false
    })
    return filtered
  }
  return posts.value
}

// 3. Sports Score Bar
const { data: sportsResponse } = useFetch(`${config.public.apiBase}/m/CricketScoreboard`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'home-sports-score',
  lazy: true,
  default: () => null,
})

const sportsScore = computed(() => sportsResponse.value?.success ? sportsResponse.value : null)

const formatDate = (dateStr) => {
  try {
    return new Date(dateStr).toLocaleDateString('bn-BD', { month: 'short', day: 'numeric' })
  } catch {
    return 'আজ'
  }
}

// 4. YouTube Live & Videos config from CMS
const { data: ytResponse } = useFetch(`${config.public.apiBase}/youtube-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'home-youtube-config',
  lazy: true,
  default: () => ({ success: true, data: {} }),
})

const ytConfig = computed(() => ytResponse.value?.data || {})
const ytVideos = computed(() => ytConfig.value?.videos || [])

function extractYoutubeId(url) {
  if (!url) return null
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

const ytLiveId = computed(() => {
  return extractYoutubeId(ytConfig.value?.live_video_url)
})

function getYoutubeThumb(url) {
  const id = extractYoutubeId(url)
  return id ? `https://img.youtube.com/vi/${id}/mqdefault.jpg` : null
}

const shouldRenderYoutubeAt = (sIdx) => {
  if (ytConfig.value?.show_on_home === false) return false
  if (!ytLiveId.value && (!ytVideos.value || ytVideos.value.length === 0)) return false

  const pos = ytConfig.value?.home_position || 'middle'
  const total = activeSections.value.length

  if (pos === 'top') {
    return sIdx === 0
  }
  if (pos === 'middle') {
    const targetIdx = total > 1 ? 1 : 0
    return sIdx === targetIdx
  }
  return false
}

const shouldRenderYoutubeAtBottom = computed(() => {
  if (ytConfig.value?.show_on_home === false) return false
  if (!ytLiveId.value && (!ytVideos.value || ytVideos.value.length === 0)) return false

  const pos = ytConfig.value?.home_position || 'middle'
  if (pos === 'bottom') return true
  if (activeSections.value.length === 0) return true
  return false
})

onMounted(() => {
  // Fresh refresh when navigating to home
  refreshPosts()
  refreshFeatured()

  if (process.client) {
    const handleVisibility = () => {
      if (document.visibilityState === 'visible') {
        refreshPosts()
        refreshFeatured()
      }
    }
    document.addEventListener('visibilitychange', handleVisibility)
    onUnmounted(() => {
      document.removeEventListener('visibilitychange', handleVisibility)
    })
  }
})
</script>
