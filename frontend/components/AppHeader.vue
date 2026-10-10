<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
    <!-- Top Utility Bar (Date, Prayer/Weather, E-paper) -->
    <div
      v-if="hfConfig.show_topbar !== false"
      class="border-b border-gray-100 bg-gray-50/70 text-xs text-gray-500 py-1.5 px-4 sm:px-8 flex flex-wrap items-center justify-between gap-2"
    >
      <div class="flex items-center gap-4">
        <span>📅 {{ currentDate }}</span>
        <span v-if="hfConfig.topbar_location" class="hidden md:inline">|</span>
        <span v-if="hfConfig.topbar_location" class="hidden md:inline">{{ hfConfig.topbar_location }}</span>
      </div>

      <div class="flex items-center gap-4">
        <span v-if="hfConfig.prayer_time" class="text-emerald-700 font-medium">{{ hfConfig.prayer_time }}</span>
        <span v-if="hfConfig.prayer_time && hfConfig.weather_text">|</span>
        <span v-if="hfConfig.weather_text" class="text-blue-700 font-medium">{{ hfConfig.weather_text }}</span>
      </div>
    </div>

    <!-- Main Branding Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-4 flex items-center justify-between">
      <NuxtLink to="/" class="flex items-center gap-3 group">
        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center font-bold text-white text-xl shadow-sm group-hover:scale-105 transition-transform">
          {{ siteName.charAt(0) || 'দ' }}
        </div>
        <div>
          <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none group-hover:text-red-600 transition-colors">
            {{ siteName }}
          </h1>
          <p class="text-[11px] text-gray-400 font-medium tracking-wider mt-0.5">
            {{ hfConfig.site_tagline || 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক' }}
          </p>
        </div>
      </NuxtLink>

      <!-- Header Leaderboard Ad (Desktop) -->
      <div class="hidden lg:flex items-center justify-center">
        <a
          href="mailto:ads@dainikbangla.com"
          class="px-5 py-2 rounded-xl bg-gradient-to-r from-gray-100 via-amber-50 to-gray-100 border border-amber-200/60 hover:border-amber-300 transition-colors flex items-center gap-3 text-xs text-gray-700 shadow-2xs group"
        >
          <span class="w-6 h-6 rounded-md bg-amber-500 text-white flex items-center justify-center font-bold text-[10px]">AD</span>
          <div class="text-left">
            <span class="font-bold text-gray-900 block group-hover:text-red-600 transition-colors">বিজ্ঞাপনের জন্য যোগাযোগ করুন</span>
            <span class="text-[10px] text-gray-400">শীর্ষ ব্যানার ও কাস্টম স্পন্সরশিপ</span>
          </div>
          <span class="text-red-600 font-bold ml-1 text-xs">→</span>
        </a>
      </div>

      <div class="flex items-center gap-3">
        <NuxtLink
          v-if="hfConfig.show_epaper_btn !== false"
          :to="hfConfig.epaper_url || '/epaper'"
          class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5"
        >
          <span>📰</span> ই-পেপার
        </NuxtLink>
        <NuxtLink
          v-if="hfConfig.show_live_btn !== false"
          :to="hfConfig.live_url || '/live'"
          class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5"
        >
          <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
          লাইভ
        </NuxtLink>
      </div>
    </div>

    <!-- Category Navigation Bar -->
    <nav class="border-t border-gray-100 overflow-x-auto scrollbar-none">
      <div class="max-w-7xl mx-auto px-4 sm:px-8 flex items-center gap-1 py-1 text-sm font-semibold text-gray-700">
        <NuxtLink
          to="/"
          class="px-3.5 py-2 rounded-lg hover:text-red-600 hover:bg-red-50/60 transition-colors whitespace-nowrap"
          active-class="text-red-600 bg-red-50"
        >
          প্রচ্ছদ
        </NuxtLink>

        <NuxtLink
          v-for="cat in categories"
          :key="cat.id"
          :to="`/category/${cat.slug}`"
          class="px-3.5 py-2 rounded-lg hover:text-red-600 hover:bg-red-50/60 transition-colors whitespace-nowrap"
          active-class="text-red-600 bg-red-50"
        >
          {{ cat.name }}
        </NuxtLink>
      </div>
    </nav>
  </header>
</template>

<script setup>
const config = useRuntimeConfig()
const siteName = config.public.siteName || 'দৈনিক বাংলা'

const currentDate = new Date().toLocaleDateString('bn-BD', {
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric'
})

// Dynamic Header/Footer Config from CMS
const { data: hfResponse } = await useFetch(`${config.public.apiBase}/header-footer-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'app-header-footer-config',
  default: () => ({ success: true, data: {} }),
})

const hfConfig = computed(() => hfResponse.value?.data || {})

// Dynamic categories from Headless CMS with useFetch
const { data: categoriesResponse } = await useFetch(`${config.public.apiBase}/categories`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'header-categories',
  default: () => ({ data: [] }),
})

const defaultCategories = [
  { id: 1, name: 'জাতীয়', slug: 'national' },
  { id: 2, name: 'রাজনীতি', slug: 'politics' },
  { id: 3, name: 'আন্তর্জাতিক', slug: 'international' },
  { id: 4, name: 'অর্থনীতি', slug: 'economy' },
  { id: 5, name: 'খেলাধুলা', slug: 'sports' },
  { id: 6, name: 'বিনোদন', slug: 'entertainment' },
  { id: 7, name: 'তথ্যপ্রযুক্তি', slug: 'tech' },
  { id: 8, name: 'শিক্ষা', slug: 'education' },
  { id: 9, name: 'মতামত', slug: 'opinion' },
  { id: 10, name: 'লাইফস্টাইল', slug: 'lifestyle' },
]

const categories = computed(() => {
  const remote = categoriesResponse.value?.data
  return (remote && remote.length > 0) ? remote : defaultCategories
})
</script>
