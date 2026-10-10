<template>
  <div class="min-h-screen flex flex-col bg-gray-50/50">
    <!-- Header with Utility, Brand, Nav & Mobile Drawer -->
    <AppHeader />

    <!-- Live Breaking News Bar -->
    <BreakingTicker :breakingNews="breakingNews" />

    <!-- Main Dynamic View (SPA Page Content) with bottom padding for mobile bar -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-8 py-6 pb-20 md:pb-6">
      <NuxtPage :page-key="route => route.fullPath" />
    </main>

    <!-- Footer -->
    <AppFooter />

    <!-- Floating Mobile Bottom Navigation Bar (md:hidden) -->
    <nav
      class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.08)] py-1 px-2"
      aria-label="মোবাইল বটম নেভিগেশন"
    >
      <div class="grid grid-cols-5 items-center justify-around max-w-md mx-auto">
        <!-- 1. Home -->
        <NuxtLink
          to="/"
          exact
          class="flex flex-col items-center justify-center py-1 text-gray-500 hover:text-red-600 transition-colors group"
          active-class="!text-red-600 font-bold"
        >
          <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <span class="text-[10px] mt-0.5 tracking-tight">হোম</span>
        </NuxtLink>

        <!-- 2. Live -->
        <NuxtLink
          to="/live"
          class="flex flex-col items-center justify-center py-1 text-gray-500 hover:text-red-600 transition-colors group relative"
          active-class="!text-red-600 font-bold"
        >
          <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
            </svg>
            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
            <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-red-600"></span>
          </div>
          <span class="text-[10px] mt-0.5 tracking-tight">লাইভ</span>
        </NuxtLink>

        <!-- 3. Video -->
        <NuxtLink
          to="/video"
          class="flex flex-col items-center justify-center py-1 text-gray-500 hover:text-red-600 transition-colors group"
          active-class="!text-red-600 font-bold"
        >
          <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
          </div>
          <span class="text-[10px] mt-0.5 tracking-tight">ভিডিও</span>
        </NuxtLink>

        <!-- 4. E-Paper -->
        <NuxtLink
          to="/epaper"
          class="flex flex-col items-center justify-center py-1 text-gray-500 hover:text-red-600 transition-colors group"
          active-class="!text-red-600 font-bold"
        >
          <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
          </div>
          <span class="text-[10px] mt-0.5 tracking-tight">ই-পেপার</span>
        </NuxtLink>

        <!-- 5. Search -->
        <NuxtLink
          to="/search"
          class="flex flex-col items-center justify-center py-1 text-gray-500 hover:text-red-600 transition-colors group"
          active-class="!text-red-600 font-bold"
        >
          <div class="relative">
            <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <span class="text-[10px] mt-0.5 tracking-tight">অনুসন্ধান</span>
        </NuxtLink>
      </div>
    </nav>

    <!-- Smooth Floating Scroll-to-Top Button -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-75 translate-y-3"
      enter-to-class="opacity-100 scale-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100 translate-y-0"
      leave-to-class="opacity-0 scale-75 translate-y-3"
    >
      <button
        v-if="showScrollTop"
        @click="scrollToTop"
        type="button"
        class="fixed bottom-20 right-4 md:bottom-6 md:right-6 z-40 w-11 h-11 bg-white hover:bg-red-600 text-gray-700 hover:text-white border border-gray-200/90 shadow-lg hover:shadow-xl rounded-full flex items-center justify-center transition-all duration-300 group focus:outline-none focus:ring-2 focus:ring-red-500/20"
        aria-label="উপরে যান"
        title="উপরে যান"
      >
        <svg
          class="w-5 h-5 transition-transform group-hover:-translate-y-0.5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
      </button>
    </Transition>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

// 1. Fetch breaking news
const { data: breakingResponse } = await useFetch(`${config.public.apiBase}/posts/breaking`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'app-breaking-news',
  default: () => ({ success: true, data: [] }),
})

const breakingNews = computed(() => breakingResponse.value?.data || [])

// 2. Scroll-to-Top State and Controller
const showScrollTop = ref(false)

const onScroll = () => {
  if (process.client) {
    showScrollTop.value = window.scrollY > 300
  }
}

const scrollToTop = () => {
  if (process.client) {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  }
}

onMounted(() => {
  if (process.client) {
    window.addEventListener('scroll', onScroll, { passive: true })
  }
})

onUnmounted(() => {
  if (process.client) {
    window.removeEventListener('scroll', onScroll)
  }
})
</script>
