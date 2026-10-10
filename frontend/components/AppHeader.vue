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
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3.5 sm:py-4 flex items-center justify-between">
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

      <!-- Action Items & Mobile Hamburger -->
      <div class="flex items-center gap-2 sm:gap-3">
        <NuxtLink
          v-if="hfConfig.show_epaper_btn !== false"
          :to="hfConfig.epaper_url || '/epaper'"
          class="hidden sm:flex px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-colors items-center gap-1.5"
        >
          <span>📰</span> ই-পেপার
        </NuxtLink>
        <NuxtLink
          v-if="hfConfig.show_live_btn !== false"
          :to="hfConfig.live_url || '/live'"
          class="hidden sm:flex px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-semibold transition-colors items-center gap-1.5"
        >
          <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
          লাইভ
        </NuxtLink>
        <NuxtLink
          to="/search"
          class="p-2 sm:px-3 sm:py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold transition-colors flex items-center justify-center gap-1.5"
          title="সংবাদ অনুসন্ধান করুন"
        >
          <span>🔍</span>
          <span class="hidden sm:inline">অনুসন্ধান</span>
        </NuxtLink>

        <!-- Mobile Hamburger Toggle Button (md:hidden) -->
        <button
          @click="isDrawerOpen = true"
          type="button"
          class="md:hidden p-2 text-gray-700 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500/20"
          aria-label="মোবাইল মেনু খুলুন"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Category Navigation Bar (Desktop & Horizontal Scrollable for Tablets) -->
    <nav class="border-t border-gray-100 overflow-x-auto scrollbar-none">
      <div class="max-w-7xl mx-auto px-4 sm:px-8 flex items-center gap-1 py-1 text-sm font-semibold text-gray-700">
        <NuxtLink
          to="/"
          class="px-3.5 py-2 rounded-lg hover:text-red-600 hover:bg-red-50/60 transition-colors whitespace-nowrap"
          active-class="text-red-600 bg-red-50"
          exact
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

    <!-- Mobile Slide-out Drawer Menu (Teleport to body for perfect stacking) -->
    <Teleport to="body">
      <!-- Backdrop Overlay -->
      <Transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isDrawerOpen"
          class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 md:hidden"
          @click="closeDrawer"
          aria-hidden="true"
        ></div>
      </Transition>

      <!-- Drawer Panel -->
      <Transition
        enter-active-class="transition-transform duration-300 ease-out"
        enter-from-class="-translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition-transform duration-200 ease-in"
        leave-from-class="translate-x-0"
        leave-to-class="-translate-x-full"
      >
        <aside
          v-if="isDrawerOpen"
          class="fixed inset-y-0 left-0 w-[320px] max-w-[85vw] bg-white z-50 shadow-2xl flex flex-col justify-between overflow-hidden md:hidden"
          role="dialog"
          aria-modal="true"
          aria-label="মোবাইল নেভিগেশন ড্রয়ার"
        >
          <!-- Drawer Header: Brand & Close -->
          <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
            <NuxtLink to="/" @click="closeDrawer" class="flex items-center gap-2.5">
              <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center font-bold text-white text-lg shadow-xs">
                {{ siteName.charAt(0) || 'দ' }}
              </div>
              <div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight leading-tight">
                  {{ siteName }}
                </h2>
                <p class="text-[10px] text-gray-400 font-medium">
                  {{ hfConfig.site_tagline || 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক' }}
                </p>
              </div>
            </NuxtLink>

            <button
              @click="closeDrawer"
              type="button"
              class="w-9 h-9 rounded-xl flex items-center justify-center text-gray-500 hover:text-red-600 hover:bg-red-50 transition-colors focus:outline-none"
              aria-label="ড্রয়ার বন্ধ করুন"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Drawer Body (Scrollable) -->
          <div class="flex-1 px-4 py-4 space-y-5 overflow-y-auto sidebar-scroll">
            <!-- Top Utility Bar in Drawer (Date, Prayer, Weather) -->
            <div class="p-3.5 bg-gray-50/90 rounded-2xl border border-gray-100 text-xs text-gray-600 space-y-2.5">
              <div class="flex items-center gap-2 font-medium text-gray-800">
                <span>📅</span>
                <span>{{ currentDate }}</span>
              </div>
              <div
                v-if="hfConfig.topbar_location || hfConfig.weather_text || hfConfig.prayer_time"
                class="flex flex-wrap items-center gap-x-3 gap-y-1 pt-2 border-t border-gray-200/60 text-[11px]"
              >
                <span v-if="hfConfig.topbar_location" class="flex items-center gap-1 text-gray-700">
                  <span>📍</span> {{ hfConfig.topbar_location }}
                </span>
                <span v-if="hfConfig.prayer_time" class="text-emerald-700 font-semibold flex items-center gap-1">
                  <span>🕌</span> {{ hfConfig.prayer_time }}
                </span>
                <span v-if="hfConfig.weather_text" class="text-blue-700 font-semibold flex items-center gap-1">
                  <span>⛅</span> {{ hfConfig.weather_text }}
                </span>
              </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div>
              <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                দ্রুত সেবা
              </div>
              <div class="grid grid-cols-3 gap-2">
                <NuxtLink
                  v-if="hfConfig.show_epaper_btn !== false"
                  :to="hfConfig.epaper_url || '/epaper'"
                  @click="closeDrawer"
                  class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200/70 text-gray-700 transition-all text-center group"
                >
                  <span class="text-xl mb-1 group-hover:scale-110 transition-transform">📰</span>
                  <span class="text-[11px] font-bold">ই-পেপার</span>
                </NuxtLink>

                <NuxtLink
                  v-if="hfConfig.show_live_btn !== false"
                  :to="hfConfig.live_url || '/live'"
                  @click="closeDrawer"
                  class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-200/70 text-red-600 transition-all text-center group"
                >
                  <span class="text-xl mb-1 relative group-hover:scale-110 transition-transform">
                    📺
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                  </span>
                  <span class="text-[11px] font-bold">লাইভ</span>
                </NuxtLink>

                <NuxtLink
                  to="/search"
                  @click="closeDrawer"
                  class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200/70 text-gray-700 transition-all text-center group"
                >
                  <span class="text-xl mb-1 group-hover:scale-110 transition-transform">🔍</span>
                  <span class="text-[11px] font-bold">অনুসন্ধান</span>
                </NuxtLink>
              </div>
            </div>

            <!-- Full Categories List with Active Badges -->
            <div>
              <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">সকল বিভাগ</span>
                <span class="text-[10px] text-gray-400 font-mono">{{ categories.length + 1 }} টি</span>
              </div>

              <div class="space-y-1">
                <NuxtLink
                  to="/"
                  @click="closeDrawer"
                  class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors border-l-4 border-transparent"
                  active-class="!bg-red-50 !text-red-600 !border-red-600 font-bold"
                  exact
                >
                  <span class="flex items-center gap-2.5">
                    <span>🏠</span> প্রচ্ছদ
                  </span>
                  <span class="text-xs text-gray-400">›</span>
                </NuxtLink>

                <NuxtLink
                  v-for="cat in categories"
                  :key="cat.id"
                  :to="`/category/${cat.slug}`"
                  @click="closeDrawer"
                  class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors border-l-4 border-transparent"
                  active-class="!bg-red-50 !text-red-600 !border-red-600 font-bold"
                >
                  <span class="flex items-center gap-2.5">
                    <span>{{ getCategoryIcon(cat.slug) }}</span> {{ cat.name }}
                  </span>
                  <span class="text-xs text-gray-400">›</span>
                </NuxtLink>
              </div>
            </div>
          </div>

          <!-- Drawer Footer: Social Links & Copyright -->
          <div class="p-4 border-t border-gray-100 bg-gray-50/80 space-y-3 shrink-0">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-gray-500">সোশ্যাল মিডিয়ায় যুক্ত থাকুন:</span>
              <div class="flex items-center gap-1.5">
                <a
                  v-if="hfConfig.facebook_url"
                  :href="hfConfig.facebook_url"
                  target="_blank"
                  class="w-7 h-7 rounded-lg bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center font-bold text-xs transition shadow-2xs"
                  title="Facebook"
                >
                  f
                </a>
                <a
                  v-if="hfConfig.twitter_url"
                  :href="hfConfig.twitter_url"
                  target="_blank"
                  class="w-7 h-7 rounded-lg bg-gray-900 hover:bg-black text-white flex items-center justify-center font-bold text-xs transition shadow-2xs"
                  title="X"
                >
                  𝕏
                </a>
                <a
                  v-if="hfConfig.youtube_url"
                  :href="hfConfig.youtube_url"
                  target="_blank"
                  class="w-7 h-7 rounded-lg bg-red-600 hover:bg-red-700 text-white flex items-center justify-center font-bold text-xs transition shadow-2xs"
                  title="YouTube"
                >
                  ▶
                </a>
              </div>
            </div>
            <p class="text-[10px] text-gray-400 text-center">
              {{ hfConfig.copyright_text || '© ২০২৬ দৈনিক বাংলা। সর্বস্বত্ব সংরক্ষিত।' }}
            </p>
          </div>
        </aside>
      </Transition>
    </Teleport>
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

// Category icons map
const getCategoryIcon = (slug) => {
  const icons = {
    national: '🇧🇩',
    politics: '🏛️',
    international: '🌐',
    economy: '📈',
    sports: '⚽',
    entertainment: '🎬',
    tech: '💻',
    education: '🎓',
    opinion: '✍️',
    lifestyle: '☕',
    special: '⭐',
  }
  return icons[slug] || '📌'
}

// Drawer state and handling
const isDrawerOpen = ref(false)
const closeDrawer = () => {
  isDrawerOpen.value = false
}

// Automatically close drawer on page change
const route = useRoute()
watch(() => route.fullPath, () => {
  isDrawerOpen.value = false
})

// Lock body scrolling when drawer is open
watch(isDrawerOpen, (isOpen) => {
  if (process.client) {
    if (isOpen) {
      document.body.style.overflow = 'hidden'
    } else {
      document.body.style.overflow = ''
    }
  }
})

// Close drawer on Escape key
const onKeydown = (e) => {
  if (e.key === 'Escape' && isDrawerOpen.value) {
    closeDrawer()
  }
}

onMounted(() => {
  if (process.client) {
    window.addEventListener('keydown', onKeydown)
  }
})

onUnmounted(() => {
  if (process.client) {
    window.removeEventListener('keydown', onKeydown)
    document.body.style.overflow = ''
  }
})
</script>
