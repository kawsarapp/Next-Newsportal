<template>
  <div class="space-y-6">
    <div class="border-b pb-4">
      <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
        <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
        <span>/</span>
        <span>ক্যাটাগরি</span>
      </div>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">{{ categoryTitle }}</h2>
        <span v-if="!pending && posts.length > 0" class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium">
          {{ posts.length }} টি সংবাদ
        </span>
      </div>
    </div>

    <!-- Posts Grid Loading Skeleton -->
    <div v-if="pending" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3 animate-pulse">
        <div class="h-44 bg-gray-200 rounded-xl"></div>
        <div class="h-5 bg-gray-200 rounded-md w-3/4"></div>
        <div class="space-y-1.5">
          <div class="h-3 bg-gray-200 rounded w-full"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-16 bg-red-50 rounded-2xl border border-red-100 p-8 space-y-3">
      <div class="text-4xl mb-1">⚠️</div>
      <p class="text-base font-bold text-red-700">সংবাদ লোড করতে সমস্যা হয়েছে</p>
      <p class="text-xs text-red-500">দয়া করে কিছুক্ষণ পর আবার চেষ্টা করুন</p>
      <button @click="refresh()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition">
        পুনরায় চেষ্টা করুন
      </button>
    </div>

    <!-- Empty State -->
    <div v-else-if="posts.length === 0" class="text-center py-20 bg-white rounded-2xl border border-gray-100 p-8 space-y-3">
      <div class="text-4xl mb-1">📂</div>
      <p class="text-base font-bold text-gray-700">এই বিভাগে এখনো কোনো সংবাদ প্রকাশিত হয়নি</p>
      <p class="text-xs text-gray-400">খুব শীঘ্রই এই বিভাগে নতুন সংবাদ যুক্ত করা হবে</p>
      <NuxtLink to="/" class="inline-block mt-2 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-colors">
        ← প্রচ্ছদে ফিরে যান
      </NuxtLink>
    </div>

    <!-- Loaded Posts Grid -->
    <!-- Loaded Posts Grid with In-Feed Ad Banner -->
    <div v-else class="space-y-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <template v-for="(post, index) in posts" :key="post.id">
          <!-- In-feed Category Sponsor Banner after 3 items -->
          <div
            v-if="index === 3"
            class="sm:col-span-2 md:col-span-3 rounded-2xl overflow-hidden bg-gradient-to-r from-gray-900 via-slate-800 to-gray-900 border border-gray-700/60 p-5 sm:p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm"
          >
            <div class="flex items-center gap-3.5">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-red-500 flex items-center justify-center text-xl shadow-xs shrink-0">
                ⭐
              </div>
              <div>
                <span class="text-[9px] uppercase tracking-wider text-amber-400 font-bold block mb-0.5">স্পন্সরড কনটেন্ট · Sponsored</span>
                <h4 class="font-bold text-sm sm:text-base text-white">দৈনিক বাংলা ডিজিটাল বিজ্ঞাপন নেটওয়ার্কে যোগ দিন</h4>
                <p class="text-xs text-gray-300 mt-0.5 line-clamp-1">দেশ ও প্রবাসের কোটি পাঠকের কাছে আপনার ব্র্যান্ডের প্রচারণা পৌঁছে দিন</p>
              </div>
            </div>
            <a
              href="mailto:ads@dainikbangla.com"
              class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0"
            >
              বিজ্ঞাপন বুকিং করুন →
            </a>
          </div>

          <div
            class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-2xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between"
          >
            <NuxtLink :to="`/news/${post.slug}`" class="space-y-3 block">
              <div class="h-44 w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                <img
                  :src="post.featured_image?.original || (typeof post.featured_image === 'string' ? post.featured_image : '') || 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600&auto=format&fit=crop'"
                  :alt="post.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  loading="lazy"
                />
              </div>
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
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const config = useRuntimeConfig()

const banglaCategoryMap = {
  national: 'জাতীয়',
  politics: 'রাজনীতি',
  sports: 'খেলাধুলা',
  international: 'আন্তর্জাতিক',
  economy: 'অর্থনীতি',
  entertainment: 'বিনোদন',
  tech: 'তথ্যপ্রযুক্তি',
  education: 'শিক্ষা',
  lifestyle: 'লাইফস্টাইল',
  opinion: 'মতামত',
}

const slug = computed(() => String(route.params.slug || ''))

// Fetch Category detail from CMS so title is always available even if empty
const { data: catResponse } = await useFetch(() => `${config.public.apiBase}/categories/${slug.value}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: () => `category-info-${slug.value}`,
  default: () => null,
})

const { data: postsResponse, pending, error, refresh } = await useFetch(() => `${config.public.apiBase}/posts?category=${slug.value}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: () => `category-posts-${slug.value}`,
})

const posts = computed(() => postsResponse.value?.data || [])

const categoryTitle = computed(() => {
  return catResponse.value?.data?.name || posts.value[0]?.category?.name || banglaCategoryMap[slug.value] || slug.value
})
</script>
