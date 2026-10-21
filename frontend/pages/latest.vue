<template>
  <div class="space-y-6">
    <!-- Header & Breadcrumb -->
    <div class="border-b border-gray-200/80 pb-5">
      <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
        <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
        <span>/</span>
        <span class="text-gray-600 font-medium">সর্বশেষ সংবাদ</span>
      </div>

      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Title and Live Pulse -->
        <div class="flex items-center gap-3">
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
            সর্বশেষ সংবাদ
          </h1>
          <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-600 rounded-full text-xs font-bold border border-red-100/80">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-red-600"></span>
            </span>
            <span>লাইভ আপডেট</span>
          </div>
        </div>

        <!-- Actions: Total count & Refresh button -->
        <div class="flex items-center gap-3">
          <span v-if="!pending && filteredPosts.length > 0" class="text-xs text-gray-500 bg-gray-100 px-3 py-1.5 rounded-full font-medium">
            {{ toBanglaNumber(filteredPosts.length) }} টি সংবাদ
          </span>

          <button
            type="button"
            @click="handleManualRefresh"
            :disabled="pending"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 shadow-2xs hover:border-gray-300 transition-all cursor-pointer disabled:opacity-50"
            title="সর্বশেষ সংবাদ রিফ্রেশ করুন"
          >
            <svg
              class="w-3.5 h-3.5 text-gray-500 transition-transform duration-500"
              :class="{ 'animate-spin text-red-600': isRefreshing || pending }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>{{ isRefreshing || pending ? 'আপডেট হচ্ছে...' : 'রিফ্রেশ' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Category Filter Pills Bar -->
    <div v-if="availableCategories.length > 1" class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1">
      <button
        type="button"
        @click="selectedCategory = 'all'"
        :class="[
          'px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all cursor-pointer shrink-0',
          selectedCategory === 'all'
            ? 'bg-red-600 text-white shadow-xs'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200/80 hover:text-gray-900'
        ]"
      >
        সকল সংবাদ ({{ toBanglaNumber(allPosts.length) }})
      </button>

      <button
        v-for="cat in availableCategories"
        :key="cat.slug"
        type="button"
        @click="selectedCategory = cat.slug"
        :class="[
          'px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all cursor-pointer shrink-0',
          selectedCategory === cat.slug
            ? 'bg-red-600 text-white shadow-xs'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200/80 hover:text-gray-900'
        ]"
      >
        {{ cat.name }} ({{ toBanglaNumber(cat.count) }})
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="pending && !isRefreshing" class="space-y-6 animate-pulse">
      <!-- Lead Skeleton -->
      <div class="h-64 sm:h-80 bg-gray-200 rounded-2xl"></div>
      <!-- Grid Skeleton -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3">
          <div class="h-44 bg-gray-200 rounded-xl"></div>
          <div class="h-4 bg-gray-200 rounded-md w-1/3"></div>
          <div class="h-5 bg-gray-200 rounded-md w-3/4"></div>
          <div class="h-3 bg-gray-200 rounded w-full"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error && allPosts.length === 0" class="text-center py-16 bg-red-50 rounded-2xl border border-red-100 p-8 space-y-3">
      <div class="text-4xl mb-1">⚠️</div>
      <p class="text-base font-bold text-red-700">সংবাদ লোড করতে সমস্যা হয়েছে</p>
      <p class="text-xs text-red-500">দয়া করে ইন্টারনেট সংযোগ পরীক্ষা করে পুনরায় চেষ্টা করুন।</p>
      <button
        @click="handleManualRefresh"
        class="mt-2 px-5 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition cursor-pointer"
      >
        পুনরায় চেষ্টা করুন
      </button>
    </div>

    <!-- Empty State -->
    <div v-else-if="filteredPosts.length === 0" class="text-center py-20 bg-white rounded-2xl border border-gray-100 p-8 space-y-3">
      <div class="text-4xl mb-1">📂</div>
      <p class="text-base font-bold text-gray-700">কোনো সংবাদ পাওয়া যায়নি</p>
      <p class="text-xs text-gray-400">খুব শীঘ্রই নতুন সংবাদ প্রকাশিত হবে</p>
      <button
        v-if="selectedCategory !== 'all'"
        @click="selectedCategory = 'all'"
        class="inline-block mt-2 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-colors cursor-pointer"
      >
        সকল সংবাদ দেখুন
      </button>
    </div>

    <!-- Loaded Posts -->
    <div v-else class="space-y-6">
      <!-- 1. Featured Top Latest Post (Only shown when viewing 'all' or if first post exists) -->
      <div
        v-if="topPost"
        class="bg-white rounded-2xl border border-gray-200/90 shadow-2xs hover:shadow-md transition-all duration-300 overflow-hidden group"
      >
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
          <!-- Image -->
          <NuxtLink :to="`/news/${topPost.slug}`" class="lg:col-span-7 block relative overflow-hidden bg-gray-100 h-64 sm:h-80 lg:h-96">
            <img
              :src="getPostImage(topPost)"
              :alt="topPost.title"
              class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-500"
              loading="eager"
            />
            <div class="absolute top-4 left-4 flex gap-2">
              <span v-if="topPost.is_breaking" class="bg-red-600 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow-sm animate-pulse">
                ব্রেকিং
              </span>
              <span class="bg-gray-900/80 text-white text-xs font-semibold px-2.5 py-1 rounded-full backdrop-blur-xs">
                সর্বশেষ খবর
              </span>
            </div>
          </NuxtLink>

          <!-- Content Details -->
          <div class="lg:col-span-5 p-6 sm:p-8 flex flex-col justify-between space-y-4">
            <div class="space-y-3">
              <div class="flex items-center gap-2">
                <NuxtLink
                  v-if="topPost.category?.slug"
                  :to="`/category/${topPost.category.slug}`"
                  class="font-bold text-xs text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg transition-colors inline-block"
                >
                  {{ topPost.category.name }}
                </NuxtLink>
                <span class="text-xs text-gray-400">·</span>
                <span class="text-xs text-gray-500 font-medium flex items-center gap-1">
                  <span>⏱️</span>
                  <span>{{ formatRelativeTime(topPost.published_at) }}</span>
                </span>
              </div>

              <NuxtLink :to="`/news/${topPost.slug}`" class="block group-hover:text-red-600 transition-colors">
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 leading-snug group-hover:text-red-600 transition-colors">
                  {{ topPost.title }}
                </h2>
              </NuxtLink>

              <p class="text-sm text-gray-600 line-clamp-3 sm:line-clamp-4 leading-relaxed">
                {{ topPost.excerpt }}
              </p>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
              <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-600">
                  ✍️
                </span>
                <span class="font-medium text-gray-700">{{ topPost.author?.name || 'অনলাইন ডেস্ক' }}</span>
              </div>
              <div class="flex items-center gap-3 text-gray-400">
                <span>👁️ {{ toBanglaNumber(topPost.view_count || 0) }}</span>
                <NuxtLink
                  :to="`/news/${topPost.slug}`"
                  class="text-red-600 font-bold hover:underline inline-flex items-center gap-1"
                >
                  <span>বিস্তারিত পড়ুন</span>
                  <span>→</span>
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Stream / Grid of Remaining Posts -->
      <div v-if="remainingPosts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <article
          v-for="post in remainingPosts"
          :key="post.id"
          class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-2xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between"
        >
          <div class="space-y-3">
            <!-- Thumbnail Link -->
            <NuxtLink :to="`/news/${post.slug}`" class="block h-44 w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200 relative">
              <img
                :src="getPostImage(post)"
                :alt="post.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              <span
                v-if="post.is_breaking"
                class="absolute top-2 left-2 bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs"
              >
                ব্রেকিং
              </span>
            </NuxtLink>

            <!-- Meta: Category badge & Relative publish time -->
            <div class="flex items-center justify-between text-xs pt-1">
              <NuxtLink
                v-if="post.category?.slug"
                :to="`/category/${post.category.slug}`"
                class="font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2 py-0.5 rounded-md text-[11px] transition-colors"
              >
                {{ post.category.name }}
              </NuxtLink>
              <span v-else class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                সংবাদ
              </span>

              <span class="text-gray-400 text-[11px] font-medium flex items-center gap-1">
                <span>⏱️</span>
                <span>{{ formatRelativeTime(post.published_at) }}</span>
              </span>
            </div>

            <!-- Title -->
            <NuxtLink :to="`/news/${post.slug}`" class="block">
              <h3 class="font-bold text-sm sm:text-base text-gray-800 line-clamp-2 leading-snug group-hover:text-red-600 transition-colors">
                {{ post.title }}
              </h3>
            </NuxtLink>

            <!-- Excerpt -->
            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
              {{ post.excerpt }}
            </p>
          </div>

          <!-- Bottom Footer: Author & Views -->
          <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-400 mt-4 flex items-center justify-between">
            <span class="truncate max-w-[140px]">{{ post.author?.name || 'অনলাইন ডেস্ক' }}</span>
            <span>👁️ {{ toBanglaNumber(post.view_count || 0) }}</span>
          </div>
        </article>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  key: route => route.fullPath,
})

const config = useRuntimeConfig()

// Selected category filter state
const selectedCategory = ref('all')
const isRefreshing = ref(false)

// Fetch all latest posts across all categories (sorted by published_at DESC)
const { data: postsResponse, pending, error, refresh } = useFetch(
  () => `${config.public.apiBase}/posts?per_page=30`,
  {
    headers: {
      'Accept': 'application/json',
      'X-Portal-Key': config.public.portalKey,
    },
    key: 'latest-news-posts',
    lazy: true,
    default: () => ({ data: [] }),
    onResponseError({ response, error: resError }) {
      console.error('[Latest] Failed to load latest posts:', response?.status, resError)
    },
  }
)

const allPosts = computed(() => {
  const res = postsResponse.value
  if (Array.isArray(res?.data)) return res.data
  if (Array.isArray(res)) return res
  return []
})

// Dynamically extract unique categories present in the posts
const availableCategories = computed(() => {
  const catMap = new Map()
  for (const post of allPosts.value) {
    if (post.category && post.category.slug) {
      if (!catMap.has(post.category.slug)) {
        catMap.set(post.category.slug, {
          name: post.category.name,
          slug: post.category.slug,
          count: 1,
        })
      } else {
        catMap.get(post.category.slug).count += 1
      }
    }
  }
  return Array.from(catMap.values())
})

// Filter posts by selected category pill
const filteredPosts = computed(() => {
  if (selectedCategory.value === 'all') {
    return allPosts.value
  }
  return allPosts.value.filter(
    (post) => post.category?.slug === selectedCategory.value
  )
})

// Lead / Top post is first of filtered list
const topPost = computed(() => {
  return filteredPosts.value[0] || null
})

// Remaining posts after top post
const remainingPosts = computed(() => {
  return filteredPosts.value.slice(1)
})

// Bengali number helper
function toBanglaNumber(num) {
  if (num === null || num === undefined) return '০'
  const bnNums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
  return String(num).replace(/\d/g, (d) => bnNums[d] || d)
}

// Relative time in Bengali helper
function formatRelativeTime(dateStr) {
  if (!dateStr) return 'কিছুক্ষণ আগে'
  try {
    const diffMs = Date.now() - new Date(dateStr).getTime()
    const diffSec = Math.floor(diffMs / 1000)
    const diffMin = Math.floor(diffSec / 60)
    const diffHour = Math.floor(diffMin / 60)
    const diffDay = Math.floor(diffHour / 24)

    if (diffMin < 1) return 'এইমাত্র'
    if (diffMin < 60) return `${toBanglaNumber(diffMin)} মিনিট আগে`
    if (diffHour < 24) return `${toBanglaNumber(diffHour)} ঘণ্টা আগে`
    if (diffDay === 1) return 'গতকাল'
    if (diffDay < 7) return `${toBanglaNumber(diffDay)} দিন আগে`
    return new Date(dateStr).toLocaleDateString('bn-BD', { month: 'short', day: 'numeric' })
  } catch {
    return 'কিছুক্ষণ আগে'
  }
}

// Fallback image helper
function getPostImage(post) {
  if (!post) return 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=800&auto=format&fit=crop'
  return (
    post.featured_image?.original ||
    (typeof post.featured_image === 'string' ? post.featured_image : '') ||
    'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=800&auto=format&fit=crop'
  )
}

// Manual refresh handler with animation
async function handleManualRefresh() {
  if (isRefreshing.value) return
  isRefreshing.value = true
  try {
    await refresh()
  } finally {
    setTimeout(() => {
      isRefreshing.value = false
    }, 400)
  }
}

// Auto-refresh periodically every 45s while user stays on this page
let autoRefreshTimer = null
onMounted(() => {
  autoRefreshTimer = setInterval(() => {
    // Only refresh if tab is active
    if (typeof document !== 'undefined' && !document.hidden) {
      refresh()
    }
  }, 45000)
})

onUnmounted(() => {
  if (autoRefreshTimer) {
    clearInterval(autoRefreshTimer)
  }
})

// Head metadata
useHead({
  title: computed(() => `সর্বশেষ সংবাদ — ${config.public.siteName || 'দৈনিক বাংলা'}`),
  meta: [
    { name: 'description', content: 'দেশ-বিদেশের সর্বশেষ ব্রেকিং নিউজ, রাজনীতি, বাণিজ্য, খেলাধুলা ও বিনোদনের তাজা খবর' },
  ],
})
</script>
