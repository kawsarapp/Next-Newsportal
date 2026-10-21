<template>
  <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
    <!-- Top Utility Bar (Date, Prayer/Weather, E-paper) -->
    <div
      v-if="hfConfig.show_topbar !== false"
      class="border-b border-gray-100 bg-gray-50/70 text-xs text-gray-500 py-1.5 px-4 sm:px-8 flex flex-wrap items-center justify-between gap-2"
    >
      <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
        <span>📅 {{ currentDate }}</span>
        <template v-if="hfConfig.topbar_location">
          <span class="hidden md:inline text-gray-300">|</span>
          <span class="hidden md:inline">📍 {{ hfConfig.topbar_location }}</span>
        </template>
      </div>

      <div class="flex items-center gap-3 sm:gap-4">
        <!-- Live Dhaka Time & Active Prayer Waqt -->
        <span class="text-emerald-700 font-semibold flex items-center gap-1.5" title="ঢাকা সময় ও বর্তমান ওয়াক্ত">
          <span>⏰</span>
          <span class="font-bold tracking-tight">{{ currentTime || '...' }}</span>
          <span v-if="activePrayerName" class="text-[11px] bg-emerald-100/80 text-emerald-800 px-1.5 py-0.5 rounded font-medium border border-emerald-200/60">
            🕌 {{ activePrayerName }}
          </span>
        </span>
        <span v-if="hfConfig.weather_text" class="text-gray-300">|</span>
        <span v-if="hfConfig.weather_text" class="text-blue-700 font-semibold flex items-center gap-1">
          {{ hfConfig.weather_text }}
        </span>
      </div>
    </div>

    <!-- Main Branding Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3.5 sm:py-4 flex items-center justify-between">
      <a
        href="/"
        @click="handleLogoClick"
        class="flex items-center gap-3 group cursor-pointer"
        title="হোমপেজ রিলোড করুন"
      >
        <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center font-bold text-white text-xl shadow-sm group-hover:scale-105 transition-transform">
          {{ siteName.charAt(0) || 'দ' }}
        </div>
        <div>
          <h1 class="text-2xl font-black text-gray-900 tracking-tight leading-none group-hover:text-red-600 transition-colors">
            {{ siteName }}
          </h1>
          <p class="text-xs text-gray-400 font-medium tracking-wide mt-0.5">
            {{ hfConfig.site_tagline || 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক' }}
          </p>
        </div>
      </a>

      <!-- Header Leaderboard Ad (Desktop) -->
      <div class="hidden lg:flex items-center justify-center">
        <a
          href="mailto:ads@dainikbangla.com"
          class="px-5 py-2 rounded-xl bg-gradient-to-r from-gray-100 via-amber-50 to-gray-100 border border-amber-200/60 hover:border-amber-300 transition-colors flex items-center gap-3 text-xs text-gray-700 shadow-2xs group"
        >
          <span class="w-6 h-6 rounded-md bg-amber-500 text-white flex items-center justify-center font-bold text-xs">AD</span>
          <div class="text-left">
            <span class="font-bold text-gray-900 block group-hover:text-red-600 transition-colors">বিজ্ঞাপনের জন্য যোগাযোগ করুন</span>
            <span class="text-xs text-gray-400">শীর্ষ ব্যানার ও কাস্টম স্পন্সরশিপ</span>
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

        <!-- Mobile Hamburger Toggle Button (lg:hidden) -->
        <button
          @click="isDrawerOpen = true"
          type="button"
          class="lg:hidden p-2 text-gray-700 hover:text-red-600 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500/20"
          aria-label="মোবাইল মেনু খুলুন"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>

    <!-- Category Navigation Bar (Desktop Nav with Slick Dropdowns & Mobile Touch Scrolling) -->
    <nav class="border-t border-gray-100 relative bg-white">
      <div class="max-w-7xl mx-auto relative flex items-center">
        <!-- Left Scroll Button & Subtle Fade (Mobile / Tablet) -->
        <div
          v-show="canScrollLeft"
          class="lg:hidden absolute left-0 inset-y-0 flex items-center z-10 pl-1 pr-3 bg-gradient-to-r from-white via-white/95 to-transparent pointer-events-none"
        >
          <button
            @click="scrollNavLeft"
            type="button"
            class="w-7 h-7 rounded-full bg-white shadow-md border border-gray-200/90 flex items-center justify-center text-gray-700 hover:text-red-600 hover:bg-gray-50 active:scale-95 transition-all pointer-events-auto"
            aria-label="পূর্ববর্তী ক্যাটাগরি"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
        </div>

        <!-- Scrollable Navigation Container -->
        <div
          ref="navScrollContainer"
          class="w-full flex items-center justify-start lg:justify-between gap-1.5 sm:gap-2 overflow-x-auto lg:overflow-visible scrollbar-none touch-scroll-x py-2 px-3 sm:px-6"
        >
          <!-- Static Home Link -->
          <NuxtLink
            to="/"
            class="nav-category-tab px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap shrink-0 transition-all flex items-center gap-1 min-h-[34px]"
            :class="route.path === '/' ? 'bg-red-600 text-white font-bold shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-red-600'"
            exact
          >
            প্রচ্ছদ
          </NuxtLink>

          <!-- Latest News Link -->
          <NuxtLink
            to="/latest"
            class="nav-category-tab px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap shrink-0 transition-all flex items-center gap-1 min-h-[34px]"
            :class="route.path === '/latest' ? 'bg-red-600 text-white font-bold shadow-xs' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-red-600'"
          >
            সর্বশেষ
          </NuxtLink>

          <!-- Dynamic Category Items -->
          <div
            v-for="cat in categories"
            :key="cat.id"
            class="relative group shrink-0"
          >
            <!-- Root Category Link with Dropdown Indicator if category has children -->
            <NuxtLink
              :to="getCategoryUrl(cat)"
              class="nav-category-tab px-3 py-1.5 rounded-full text-xs sm:text-sm font-semibold whitespace-nowrap shrink-0 transition-all flex items-center gap-1 min-h-[34px] group-hover:text-red-600"
              :class="isCategoryActive(cat) ? 'bg-red-600 text-white font-bold shadow-xs group-hover:text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700 hover:text-red-600'"
            >
              <span>{{ cat.name }}</span>
              <svg
                v-if="(cat.children && cat.children.length > 0) || cat.slug === 'saradesh'"
                class="w-3.5 h-3.5 hidden lg:inline-block text-gray-400 group-hover:text-red-600 group-hover:rotate-180 transition-transform duration-200"
                :class="{'text-white group-hover:text-white': isCategoryActive(cat)}"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </NuxtLink>

          <!-- ── Mega Cascading Dropdown for 'সারা দেশ' (Divisions -> Districts from Area API) ── -->
          <div
            v-if="cat.slug === 'saradesh' && areas && areas.length > 0"
            class="hidden lg:block absolute left-0 top-full pt-1.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none group-hover:pointer-events-auto"
          >
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 flex w-[640px] overflow-hidden">
              <!-- Left Column: 8 Divisions List -->
              <div class="w-48 bg-gray-50/90 border-r border-gray-100 p-2.5 space-y-1 shrink-0">
                <div class="text-xs font-bold text-gray-400 px-3 py-1.5 uppercase tracking-wider flex items-center justify-between">
                  <span>বিভাগসমূহ</span>
                  <span class="text-xs text-gray-400 font-mono">{{ areas.length }}টি</span>
                </div>
                <button
                  v-for="division in areas"
                  :key="division.id"
                  type="button"
                  @mouseenter="activeDivisionId = division.id"
                  :class="[
                    'w-full text-left px-3 py-2 rounded-xl text-xs font-semibold flex items-center justify-between transition-colors',
                    (activeDivision?.id === division.id)
                      ? 'bg-red-600 text-white shadow-xs'
                      : 'text-gray-700 hover:bg-gray-200/60'
                  ]"
                >
                  <span>{{ division.name }}</span>
                  <span class="text-xs opacity-70">›</span>
                </button>
              </div>

              <!-- Right Panel: Districts of Active Division -->
              <div class="flex-1 p-4 bg-white flex flex-col justify-between">
                <div>
                  <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-100">
                    <NuxtLink
                      :to="`/category/saradesh?division_id=${activeDivision?.id}`"
                      class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1 group/div"
                    >
                      <span>{{ activeDivision?.name }} বিভাগের সব খবর</span>
                      <span class="group-hover/div:translate-x-0.5 transition-transform">→</span>
                    </NuxtLink>
                    <span class="text-xs text-gray-400 font-medium">
                      {{ activeDivision?.districts?.length || 0 }} টি জেলা
                    </span>
                  </div>

                  <!-- District Pills Grid -->
                  <div class="grid grid-cols-3 gap-1.5 max-h-[290px] overflow-y-auto sidebar-scroll pr-1">
                    <NuxtLink
                      v-for="district in (activeDivision?.districts || [])"
                      :key="district.id"
                      :to="`/category/saradesh?division_id=${activeDivision?.id}&district_id=${district.id}`"
                      class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-gray-700 hover:text-red-600 hover:bg-red-50/80 transition-colors flex items-center justify-between border border-gray-100 hover:border-red-100"
                    >
                      <span class="truncate">{{ district.name }}</span>
                    </NuxtLink>
                  </div>
                </div>

                <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                  <span>সরাসরি জেলাভিত্তিক খবর পড়ুন</span>
                  <NuxtLink to="/category/saradesh" class="text-gray-700 hover:text-red-600 font-semibold flex items-center gap-1">
                    সমগ্র সারা দেশ <span>→</span>
                  </NuxtLink>
                </div>
              </div>
            </div>
          </div>

          <!-- ── Dropdown Menu for Categories with Children (sports, international, others, etc.) ── -->
          <div
            v-else-if="cat.children && cat.children.length > 0"
            class="hidden lg:block absolute left-0 top-full pt-1.5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pointer-events-none group-hover:pointer-events-auto"
          >
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 py-2 w-52 overflow-hidden max-h-[420px] overflow-y-auto sidebar-scroll">
              <NuxtLink
                v-for="sub in cat.children"
                :key="sub.id"
                :to="`/category/${sub.slug}`"
                class="flex items-center justify-between px-4 py-2 text-xs font-semibold text-gray-700 hover:text-red-600 hover:bg-red-50/70 transition-colors"
              >
                <span>{{ sub.name }}</span>
                <span class="text-xs text-gray-300">›</span>
              </NuxtLink>
            </div>
          </div>
        </div>
        </div>

        <!-- Right Scroll Button & Subtle Fade (Mobile / Tablet) -->
        <div
          v-show="canScrollRight"
          class="lg:hidden absolute right-0 inset-y-0 flex items-center z-10 pr-1 pl-3 bg-gradient-to-l from-white via-white/95 to-transparent pointer-events-none"
        >
          <button
            @click="scrollNavRight"
            type="button"
            class="w-7 h-7 rounded-full bg-white shadow-md border border-gray-200/90 flex items-center justify-center text-gray-700 hover:text-red-600 hover:bg-gray-50 active:scale-95 transition-all pointer-events-auto"
            aria-label="পরবর্তী ক্যাটাগরি"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </nav>

    <!-- Mobile Subcategory Secondary Quick Bar -->
    <div
      v-if="activeSubcategories.length > 0"
      class="lg:hidden border-t border-gray-100 bg-gray-50/95 py-2 px-3 overflow-x-auto scrollbar-none touch-scroll-x flex items-center gap-2 text-xs shadow-2xs"
    >
      <span class="text-xs font-bold text-gray-500 shrink-0 flex items-center gap-1">
        <span>📂</span>
        <span>উপবিভাগ:</span>
      </span>
      <NuxtLink
        :to="`/category/${activeParentCategorySlug}`"
        class="px-2.5 py-1 rounded-lg whitespace-nowrap text-xs font-semibold transition-colors shrink-0"
        :class="route.path === `/category/${activeParentCategorySlug}` ? 'bg-red-600 text-white font-bold shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-100'"
      >
        সকল
      </NuxtLink>
      <NuxtLink
        v-for="sub in activeSubcategories"
        :key="sub.id"
        :to="`/category/${sub.slug}`"
        class="px-2.5 py-1 rounded-lg whitespace-nowrap text-xs font-medium transition-colors shrink-0"
        :class="route.path === `/category/${sub.slug}` ? 'bg-red-600 text-white font-bold shadow-xs' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-100'"
      >
        {{ sub.name }}
      </NuxtLink>
    </div>

    <!-- Trending Topics Bar -->
    <div class="border-t border-gray-100 bg-gray-50/70">
      <div class="max-w-7xl mx-auto px-4 sm:px-8 py-2">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none touch-scroll-x">
          <!-- Trending Label Pill -->
          <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#1a4054] text-white rounded-full text-xs font-bold shrink-0 shadow-xs">
            <span>ট্রেন্ডিং</span>
            <svg class="w-3 h-3 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M17 7H9M17 7V15" />
            </svg>
          </div>

          <!-- Topic Pills -->
          <NuxtLink
            v-for="topic in trendingTopics"
            :key="topic"
            :to="`/search?q=${encodeURIComponent(topic)}`"
            class="px-3 py-1 bg-gray-200/80 hover:bg-red-50 hover:text-red-600 rounded-full text-xs font-medium text-gray-700 transition-colors shrink-0 whitespace-nowrap"
          >
            {{ topic }}
          </NuxtLink>
        </div>
      </div>
    </div>

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
          class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 lg:hidden"
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
          class="fixed inset-y-0 left-0 w-[330px] max-w-[85vw] h-full h-[100dvh] max-h-screen bg-white z-50 shadow-2xl flex flex-col justify-between overflow-hidden lg:hidden"
          role="dialog"
          aria-modal="true"
          aria-label="মোবাইল নেভিগেশন ড্রয়ার"
        >
          <!-- Drawer Header: Brand & Close -->
          <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
            <a
              href="/"
              @click="(e) => { closeDrawer(); handleLogoClick(e); }"
              class="flex items-center gap-2.5 cursor-pointer"
              title="হোমপেজ রিলোড করুন"
            >
              <div class="w-9 h-9 bg-red-600 rounded-xl flex items-center justify-center font-bold text-white text-lg shadow-xs">
                {{ siteName.charAt(0) || 'দ' }}
              </div>
              <div>
                <h2 class="text-lg font-black text-gray-900 tracking-tight leading-tight">
                  {{ siteName }}
                </h2>
                <p class="text-xs text-gray-400 font-medium">
                  {{ hfConfig.site_tagline || 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক' }}
                </p>
              </div>
            </a>

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

          <!-- Drawer Body (Scrollable with Accordion Subcategories) -->
          <div class="flex-1 px-4 py-4 space-y-4 overflow-y-auto overscroll-contain touch-pan-y sidebar-scroll">
            <!-- Top Utility Bar in Drawer (Date, Clock, Prayer, Weather) -->
            <div class="p-3.5 bg-gray-50/90 rounded-2xl border border-gray-100 text-xs text-gray-600 space-y-2.5">
              <div class="flex items-center justify-between font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <span>📅</span>
                  <span>{{ currentDate }}</span>
                </div>
                <div v-if="currentTime" class="flex items-center gap-1.5 text-emerald-800 font-semibold">
                  <span>⏰</span>
                  <span>{{ currentTime }}</span>
                  <span v-if="activePrayerName" class="text-[11px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded font-medium border border-emerald-200/60">
                    🕌 {{ activePrayerName }}
                  </span>
                </div>
              </div>
              <div
                v-if="hfConfig.topbar_location || hfConfig.weather_text"
                class="flex flex-wrap items-center gap-x-3 gap-y-1 pt-2 border-t border-gray-200/60 text-xs"
              >
                <span v-if="hfConfig.topbar_location" class="flex items-center gap-1 text-gray-700">
                  <span>📍</span> {{ hfConfig.topbar_location }}
                </span>
                <span v-if="hfConfig.weather_text" class="text-blue-700 font-semibold flex items-center gap-1">
                  <span>⛅</span> {{ hfConfig.weather_text }}
                </span>
              </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div>
              <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
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
                  <span class="text-xs font-bold">ই-পেপার</span>
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
                  <span class="text-xs font-bold">লাইভ</span>
                </NuxtLink>

                <NuxtLink
                  to="/search"
                  @click="closeDrawer"
                  class="flex flex-col items-center justify-center p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200/70 text-gray-700 transition-all text-center group"
                >
                  <span class="text-xl mb-1 group-hover:scale-110 transition-transform">🔍</span>
                  <span class="text-xs font-bold">অনুসন্ধান</span>
                </NuxtLink>
              </div>
            </div>

            <!-- Full Categories List with Accordion Support -->
            <div>
              <div class="flex items-center justify-between pb-2 mb-2 border-b border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">মেনু ও বিভাগসমূহ</span>
                <span class="text-xs text-gray-400 font-mono">{{ categories.length + 1 }} টি</span>
              </div>

              <div class="space-y-1">
                <!-- Home -->
                <NuxtLink
                  to="/"
                  @click="closeDrawer"
                  class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"
                  active-class="!bg-red-50 !text-red-600 font-bold"
                  exact
                >
                  <span class="flex items-center gap-2.5">
                    <span>🏠</span> প্রচ্ছদ
                  </span>
                  <span class="text-xs text-gray-400">›</span>
                </NuxtLink>

                <!-- Latest -->
                <NuxtLink
                  to="/latest"
                  @click="closeDrawer"
                  class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors"
                  active-class="!bg-red-50 !text-red-600 font-bold"
                >
                  <span class="flex items-center gap-2.5">
                    <span>⚡</span> সর্বশেষ সংবাদ
                  </span>
                  <span class="text-xs text-gray-400">›</span>
                </NuxtLink>

                <!-- All Categories -->
                <div v-for="cat in categories" :key="cat.id" class="rounded-xl overflow-hidden">
                  <!-- Category Row -->
                  <div
                    class="flex items-center justify-between px-3.5 py-2 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
                    :class="{'bg-red-50/60 text-red-600': isCategoryActive(cat)}"
                  >
                    <NuxtLink
                      :to="getCategoryUrl(cat)"
                      @click="closeDrawer"
                      class="flex-1 flex items-center gap-2 hover:text-red-600 py-0.5"
                    >
                      <span>{{ getCategoryIcon(cat.slug) }}</span>
                      <span>{{ cat.name }}</span>
                      <span v-if="cat.children && cat.children.length > 0" class="text-xs bg-red-50 text-red-600 font-bold px-1.5 py-0.5 rounded-md">
                        {{ cat.children.length }}টি
                      </span>
                      <span v-else-if="cat.slug === 'saradesh'" class="text-xs bg-red-50 text-red-600 font-bold px-1.5 py-0.5 rounded-md">
                        ৮ বিভাগ
                      </span>
                    </NuxtLink>

                    <!-- Accordion Toggle Button (for any category with children or saradesh) -->
                    <button
                      v-if="(cat.children && cat.children.length > 0) || cat.slug === 'saradesh'"
                      @click.stop.prevent="toggleAccordion(cat.slug)"
                      type="button"
                      class="px-2 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 transition-all"
                      :class="mobileAccordions[cat.slug]
                        ? 'bg-red-100 text-red-700'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                      :aria-label="`${cat.name} উপবিভাগ দেখুন`"
                    >
                      <span class="text-xs">{{ mobileAccordions[cat.slug] ? 'বন্ধ' : 'উপবিভাগ' }}</span>
                      <svg
                        class="w-3.5 h-3.5 transition-transform duration-200"
                        :class="{'rotate-180': mobileAccordions[cat.slug]}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                  </div>

                  <!-- Accordion Content (Subcategories or Areas) -->
                  <div
                    v-if="((cat.children && cat.children.length > 0) || (cat.slug === 'saradesh' && areas.length > 0)) && mobileAccordions[cat.slug]"
                    class="p-2 space-y-2 bg-gray-50/90 rounded-xl my-1 border-l-2 border-red-500 ml-2.5"
                  >
                    <!-- Special 2-Level Accordion for 'সারা দেশ' (Divisions -> Districts from areas API) -->
                    <template v-if="cat.slug === 'saradesh'">
                      <div class="flex items-center justify-between px-1 text-xs text-gray-500 font-medium">
                        <span>বিভাগসমূহ নির্বাচন করুন:</span>
                        <NuxtLink to="/category/saradesh" @click="closeDrawer" class="text-red-600 font-bold hover:underline">
                          সমগ্র সারা দেশ →
                        </NuxtLink>
                      </div>
                      <div v-for="division in areas" :key="division.id" class="space-y-1">
                        <div class="flex items-center justify-between py-1.5 px-2 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                          <NuxtLink
                            :to="`/category/saradesh?division_id=${division.id}`"
                            @click="closeDrawer"
                            class="flex-1 hover:text-red-600 flex items-center gap-1.5"
                          >
                            <span>📍</span> {{ division.name }} বিভাগ
                          </NuxtLink>
                          <button
                            v-if="division.districts && division.districts.length > 0"
                            @click="toggleDivisionAccordion(division.name_en || division.id)"
                            type="button"
                            class="p-1 text-gray-400 hover:text-red-600 flex items-center gap-0.5 text-xs"
                          >
                            <span>{{ division.districts.length }} জেলা</span>
                            <svg
                              class="w-3.5 h-3.5 transition-transform"
                              :class="{'rotate-180 text-red-600': mobileDivisionAccordions[division.name_en || division.id]}"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                            </svg>
                          </button>
                        </div>

                        <!-- Districts Grid inside Division -->
                        <div
                          v-if="division.districts && division.districts.length > 0 && mobileDivisionAccordions[division.name_en || division.id]"
                          class="grid grid-cols-2 gap-1 pl-3 pr-1 py-1.5 bg-white rounded-xl border border-gray-100"
                        >
                          <NuxtLink
                            v-for="district in division.districts"
                            :key="district.id"
                            :to="`/category/saradesh?division_id=${division.id}&district_id=${district.id}`"
                            @click="closeDrawer"
                            class="px-2 py-1 text-xs text-gray-600 hover:text-red-600 truncate rounded-md hover:bg-red-50 transition-colors"
                          >
                            • {{ district.name }}
                          </NuxtLink>
                        </div>
                      </div>
                    </template>

                    <!-- Standard 1-Level Accordion (sports, international, others, etc.) -->
                    <template v-else>
                      <div class="flex items-center justify-between px-1 text-xs text-gray-500 font-medium">
                        <span>উপবিভাগসমূহ:</span>
                        <NuxtLink :to="getCategoryUrl(cat)" @click="closeDrawer" class="text-red-600 font-bold hover:underline">
                          সকল {{ cat.name }} →
                        </NuxtLink>
                      </div>

                      <div class="grid grid-cols-2 gap-1.5 pt-0.5">
                        <NuxtLink
                          v-for="sub in cat.children"
                          :key="sub.id"
                          :to="`/category/${sub.slug}`"
                          @click="closeDrawer"
                          class="px-2.5 py-2 min-h-[38px] rounded-xl text-xs font-medium flex items-center gap-1.5 border transition-all"
                          :class="route.path === `/category/${sub.slug}`
                            ? 'bg-red-600 text-white border-red-600 font-bold shadow-2xs'
                            : 'bg-white hover:bg-red-50 text-gray-700 hover:text-red-600 border-gray-200/80'"
                        >
                          <span class="shrink-0 text-sm">{{ getCategoryIcon(sub.slug) }}</span>
                          <span class="truncate">{{ sub.name }}</span>
                        </NuxtLink>
                      </div>
                    </template>
                  </div>
                </div>
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
            <p class="text-xs text-gray-400 text-center">
              {{ hfConfig.copyright_text || '© ২০২৬ দৈনিক বাংলা। সর্বস্বত্ব সংরক্ষিত।' }}
            </p>
          </div>
        </aside>
      </Transition>
    </Teleport>
  </header>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'

const config = useRuntimeConfig()
const route = useRoute()
const siteName = config.public.siteName || 'দৈনিক বাংলা'

const refreshTrigger = useState('home-refresh-trigger', () => 0)
const isRefreshingNews = useState('is-refreshing-news', () => false)

// Refresh news without full screen reload (SPA refresh)
const handleLogoClick = async (e) => {
  if (e && (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey)) {
    return
  }
  if (e) {
    e.preventDefault()
  }

  if (route.path !== '/') {
    await navigateTo('/')
  } else {
    if (typeof window !== 'undefined') {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }

  // Trigger repeated news refresh without full screen reload
  refreshTrigger.value++
  isRefreshingNews.value = true
  try {
    await refreshNuxtData([
      'home-posts-lead',
      'home-posts-featured',
      'app-breaking-news',
      'home-dynamic-sections',
      'home-sports-score'
    ])
  } finally {
    setTimeout(() => {
      isRefreshingNews.value = false
    }, 700)
  }
}

const currentDate = new Date().toLocaleDateString('bn-BD', {
  timeZone: 'Asia/Dhaka',
  weekday: 'long',
  year: 'numeric',
  month: 'long',
  day: 'numeric'
})

// Accurate Dhaka Live Time (UTC+6 / Asia/Dhaka)
const currentTime = ref('')
let timeInterval = null

const updateCurrentTime = () => {
  const now = new Date()
  // Format in Asia/Dhaka timezone with Bengali numerals
  try {
    const formatter = new Intl.DateTimeFormat('bn-BD', {
      timeZone: 'Asia/Dhaka',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    })
    currentTime.value = formatter.format(now)
  } catch (e) {
    // Fallback calculation for UTC+6
    const utc = now.getTime() + (now.getTimezoneOffset() * 60000)
    const dhakaDate = new Date(utc + (3600000 * 6))
    const hours = dhakaDate.getHours()
    const minutes = dhakaDate.getMinutes()
    const isPM = hours >= 12
    const formattedHours = hours % 12 || 12
    const pad = (n) => String(n).padStart(2, '0')
    const toBanglaNum = (str) => {
      const bnNums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
      return String(str).replace(/\d/g, (d) => bnNums[d])
    }
    const period = isPM ? 'PM' : 'AM'
    currentTime.value = `${toBanglaNum(formattedHours)}:${toBanglaNum(pad(minutes))} ${period}`
  }
}

// Initial time evaluation
updateCurrentTime()

// Accurate Islamic Prayer Times for Dhaka, Bangladesh
// Standard calculation based on Islamic Foundation Bangladesh for Dhaka
const dhakaMonthlyTimes = [
  // 1: January
  { fajr: '০৫:২২ AM', dhuhr: '১২:০৮ PM', asr: '০৪:০০ PM', maghrib: '০৫:৩৫ PM', isha: '০৬:৫২ PM', fajrM: 322, dhuhrM: 728, asrM: 960, maghribM: 1055, ishaM: 1132, sunriseM: 402 },
  // 2: February
  { fajr: '০৫:১৪ AM', dhuhr: '১২:১২ PM', asr: '০৪:২২ PM', maghrib: '০৫:৫৬ PM', isha: '০৭:১১ PM', fajrM: 314, dhuhrM: 732, asrM: 982, maghribM: 1076, ishaM: 1151, sunriseM: 390 },
  // 3: March
  { fajr: '০৪:৫২ AM', dhuhr: '১২:০৭ PM', asr: '০৪:৩০ PM', maghrib: '০৬:১১ PM', isha: '০৭:২৫ PM', fajrM: 292, dhuhrM: 727, asrM: 990, maghribM: 1091, ishaM: 1165, sunriseM: 366 },
  // 4: April
  { fajr: '০৪:২০ AM', dhuhr: '১১:৫৯ AM', asr: '০৪:৩০ PM', maghrib: '০৬:২২ PM', isha: '০৭:৩৮ PM', fajrM: 260, dhuhrM: 719, asrM: 990, maghribM: 1102, ishaM: 1178, sunriseM: 334 },
  // 5: May
  { fajr: '০৩:৫৮ AM', dhuhr: '১১:৫৬ AM', asr: '০৪:৩৫ PM', maghrib: '০৬:৩৬ PM', isha: '০৭:৫৪ PM', fajrM: 238, dhuhrM: 716, asrM: 995, maghribM: 1116, ishaM: 1194, sunriseM: 314 },
  // 6: June
  { fajr: '০৩:৫২ AM', dhuhr: '১১:৫৯ AM', asr: '০৪:৪২ PM', maghrib: '০৬:৪৯ PM', isha: '০৮:০৯ PM', fajrM: 232, dhuhrM: 719, asrM: 1002, maghribM: 1129, ishaM: 1209, sunriseM: 310 },
  // 7: July
  { fajr: '০৪:০১ AM', dhuhr: '১২:০৪ PM', asr: '০৪:৪৪ PM', maghrib: '০৬:৪৯ PM', isha: '০৮:০৮ PM', fajrM: 241, dhuhrM: 724, asrM: 1004, maghribM: 1129, ishaM: 1208, sunriseM: 318 },
  // 8: August
  { fajr: '০৪:১৬ AM', dhuhr: '১২:০৪ PM', asr: '০৪:৩৬ PM', maghrib: '০৬:৩৩ PM', isha: '০৭:৫১ PM', fajrM: 256, dhuhrM: 724, asrM: 996, maghribM: 1093, ishaM: 1171, sunriseM: 332 },
  // 9: September
  { fajr: '০৪:২৮ AM', dhuhr: '১১:৫৭ AM', asr: '০৪:১৮ PM', maghrib: '০৬:০৫ PM', isha: '০৭:২১ PM', fajrM: 268, dhuhrM: 717, asrM: 978, maghribM: 1065, ishaM: 1141, sunriseM: 345 },
  // 10: October
  { fajr: '০৪:৪৪ AM', dhuhr: '১১:৪৭ AM', asr: '০৩:৫৫ PM', maghrib: '০৫:২৭ PM', isha: '০৬:৪৪ PM', fajrM: 285, dhuhrM: 707, asrM: 955, maghribM: 1047, ishaM: 1124, sunriseM: 358 },
  // 11: November
  { fajr: '০৪:৫৮ AM', dhuhr: '১১:৪৬ AM', asr: '০৩:৪২ PM', maghrib: '০৫:১৪ PM', isha: '০৬:৩৩ PM', fajrM: 298, dhuhrM: 706, asrM: 942, maghribM: 1034, ishaM: 1113, sunriseM: 372 },
  // 12: December
  { fajr: '০৫:১৪ AM', dhuhr: '১১:৫৬ AM', asr: '০৩:৪৫ PM', maghrib: '০৫:১৭ PM', isha: '০৬:৩৭ PM', fajrM: 314, dhuhrM: 716, asrM: 945, maghribM: 1037, ishaM: 1117, sunriseM: 395 },
]

const getDhakaPrayerTimes = (month, date) => {
  const m = Math.max(1, Math.min(12, month)) - 1
  return dhakaMonthlyTimes[m]
}

const activePrayerName = computed(() => {
  // Read currentTime so this re-evaluates automatically as the live clock ticks
  const _tick = currentTime.value
  const custom = hfConfig.value?.prayer_time?.trim()
  if (
    custom &&
    custom !== '🕌 মাগরিব ০৬:০৮ PM' &&
    custom !== 'মাগরিব ০৬:০৮ PM' &&
    custom !== '🕌 নামাজের সময়সূচি (অটোমেটিক)' &&
    custom !== 'নামাজের সময়সূচি (অটোমেটিক)' &&
    custom !== 'স্বয়ংক্রিয়' &&
    custom !== '🕌 এশা ০৬:৪৪ PM' &&
    custom !== 'এশা ০৬:৪৪ PM'
  ) {
    return custom.replace(/^🕌\s*/, '')
  }

  const now = new Date()
  const utc = now.getTime() + (now.getTimezoneOffset() * 60000)
  const dhakaDate = new Date(utc + (3600000 * 6))
  const currentMinutes = dhakaDate.getHours() * 60 + dhakaDate.getMinutes()
  const month = dhakaDate.getMonth() + 1
  const date = dhakaDate.getDate()
  const times = getDhakaPrayerTimes(month, date)

  if (currentMinutes >= times.fajrM && currentMinutes < times.sunriseM) {
    return 'ফজর'
  } else if (currentMinutes >= times.sunriseM && currentMinutes < times.dhuhrM) {
    return 'যোহর'
  } else if (currentMinutes >= times.dhuhrM && currentMinutes < times.asrM) {
    return 'আসর'
  } else if (currentMinutes >= times.asrM && currentMinutes < times.maghribM) {
    return 'মাগরিব'
  } else if (currentMinutes >= times.maghribM && currentMinutes < times.ishaM) {
    return currentMinutes < times.maghribM + 25 ? 'মাগরিব' : 'এশা'
  } else {
    return 'এশা'
  }
})

const currentPrayerDisplay = computed(() => {
  return activePrayerName.value ? `🕌 ${activePrayerName.value}` : ''
})

// Dynamic Header/Footer Config from CMS
const { data: hfResponse } = useFetch(`${config.public.apiBase}/header-footer-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'app-header-footer-config',
  lazy: true,
  default: () => ({ success: true, data: {} }),
})

const hfConfig = computed(() => hfResponse.value?.data || {})

// Trending Topics (Exact match to requested pills with CMS fallback support)
const defaultTrendingTopics = [
  'ডেঙ্গু',
  'দিল্লির বিক্ষোভ',
  'জ্বালানি তেল',
  'হাম',
  'প্রধানমন্ত্রী',
  'আজকের আবহাওয়া',
  'নামাজের সময়সূচি',
  'চাকরির খবর',
]

const trendingTopics = computed(() => {
  if (Array.isArray(hfConfig.value?.trending_topics) && hfConfig.value.trending_topics.length > 0) {
    return hfConfig.value.trending_topics
  }
  return defaultTrendingTopics
})

// Dynamic category tree from Headless CMS
const { data: categoriesResponse } = useFetch(`${config.public.apiBase}/categories/tree?show_in_nav=1`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'header-categories-tree',
  lazy: true,
  default: () => ({ data: [] }),
})

// Dynamic Area list (Divisions & Districts) from Headless CMS
const { data: areasResponse } = useFetch(`${config.public.apiBase}/areas`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'header-areas-list',
  lazy: true,
  default: () => ({ data: [] }),
})

// Fallback category hierarchy (Pure editorial categories)
const defaultCategories = [
  { id: 1, name: 'জাতীয়', slug: 'national', children: [] },
  { id: 2, name: 'রাজনীতি', slug: 'politics', children: [] },
  { id: 3, name: 'সারা দেশ', slug: 'saradesh', children: [] },
  {
    id: 4,
    name: 'আন্তর্জাতিক',
    slug: 'international',
    children: [
      { id: 41, name: 'মধ্যপ্রাচ্য', slug: 'modhyaprachyo' },
      { id: 42, name: 'যুক্তরাষ্ট্র', slug: 'juktorastro' },
      { id: 43, name: 'এশিয়া', slug: 'asia' },
      { id: 44, name: 'ইউরোপ', slug: 'europ' },
    ],
  },
  {
    id: 5,
    name: 'খেলা',
    slug: 'sports',
    children: [
      { id: 51, name: 'ক্রিকেট', slug: 'cricket' },
      { id: 52, name: 'ফুটবল', slug: 'football' },
      { id: 53, name: 'অন্যান্য', slug: 'sports-others' },
    ],
  },
  { id: 6, name: 'বিনোদন', slug: 'entertainment', children: [] },
  { id: 7, name: 'বাণিজ্য', slug: 'economy', children: [] },
  { id: 8, name: 'মতামত', slug: 'opinion', children: [] },
  { id: 9, name: 'লাইফস্টাইল', slug: 'lifestyle', children: [] },
  { id: 10, name: 'আইন ও আদালত', slug: 'law-and-court', children: [] },
  { id: 11, name: 'প্রযুক্তি', slug: 'technology', children: [] },
  { id: 12, name: 'ভিডিও', slug: 'video', children: [] },
  {
    id: 13,
    name: 'অন্যান্য',
    slug: 'others',
    children: [
      { id: 1301, name: 'শিক্ষা', slug: 'shikkha' },
      { id: 1302, name: 'ধর্ম', slug: 'dhormo' },
      { id: 1303, name: 'স্বাস্থ্য', slug: 'swasthyo' },
      { id: 1304, name: 'কৃষি', slug: 'krishi' },
      { id: 1305, name: 'প্রেস রিলিজ', slug: 'press-release' },
      { id: 1306, name: 'স্টার বিশেষ', slug: 'ster-bisesh' },
      { id: 1307, name: 'আবহাওয়া', slug: 'abhawa' },
      { id: 1308, name: 'চাকরি', slug: 'chakri' },
      { id: 1309, name: 'প্রবাস', slug: 'probas' },
      { id: 1310, name: 'ক্যাম্পাস', slug: 'campus' },
    ],
  },
]

// Fallback areas list (8 Divisions & 64 Districts)
const defaultAreas = [
  {
    id: 1,
    name: 'ঢাকা',
    name_en: 'Dhaka',
    districts: [
      { id: 1, name: 'ঢাকা' },
      { id: 2, name: 'ফরিদপুর' },
      { id: 3, name: 'গাজীপুর' },
      { id: 4, name: 'গোপালগঞ্জ' },
      { id: 5, name: 'কিশোরগঞ্জ' },
      { id: 6, name: 'মাদারীপুর' },
      { id: 7, name: 'মানিকগঞ্জ' },
      { id: 8, name: 'মুন্সীগঞ্জ' },
      { id: 9, name: 'নারায়ণগঞ্জ' },
      { id: 10, name: 'নরসিংদী' },
      { id: 11, name: 'রাজবাড়ী' },
      { id: 12, name: 'শরীয়তপুর' },
      { id: 13, name: 'টাঙ্গাইল' },
    ],
  },
  {
    id: 2,
    name: 'চট্টগ্রাম',
    name_en: 'Chattogram',
    districts: [
      { id: 14, name: 'বান্দরবান' },
      { id: 15, name: 'ব্রাহ্মণবাড়িয়া' },
      { id: 16, name: 'চাঁদপুর' },
      { id: 17, name: 'চট্টগ্রাম' },
      { id: 18, name: 'কুমিল্লা' },
      { id: 19, name: 'কক্সবাজার' },
      { id: 20, name: 'ফেনী' },
      { id: 21, name: 'খাগড়াছড়ি' },
      { id: 22, name: 'লক্ষ্মীপুর' },
      { id: 23, name: 'নোয়াখালী' },
      { id: 24, name: 'রাঙ্গামাটি' },
    ],
  },
  {
    id: 3,
    name: 'রাজশাহী',
    name_en: 'Rajshahi',
    districts: [
      { id: 25, name: 'বগুড়া' },
      { id: 26, name: 'চাঁপাইনবাবগঞ্জ' },
      { id: 27, name: 'জয়পুরহাট' },
      { id: 28, name: 'নওগাঁ' },
      { id: 29, name: 'নাটোর' },
      { id: 30, name: 'পাবনা' },
      { id: 31, name: 'রাজশাহী' },
      { id: 32, name: 'সিরাজগঞ্জ' },
    ],
  },
  {
    id: 4,
    name: 'খুলনা',
    name_en: 'Khulna',
    districts: [
      { id: 33, name: 'বাগেরহাট' },
      { id: 34, name: 'চুয়াডাঙ্গা' },
      { id: 35, name: 'যশোর' },
      { id: 36, name: 'ঝিনাইদহ' },
      { id: 37, name: 'খুলনা' },
      { id: 38, name: 'কুষ্টিয়া' },
      { id: 39, name: 'মাগুরা' },
      { id: 40, name: 'মেহেরপুর' },
      { id: 41, name: 'নড়াইল' },
      { id: 42, name: 'সাতক্ষীরা' },
    ],
  },
  {
    id: 5,
    name: 'বরিশাল',
    name_en: 'Barishal',
    districts: [
      { id: 43, name: 'বরগুনা' },
      { id: 44, name: 'বরিশাল' },
      { id: 45, name: 'ভোলা' },
      { id: 46, name: 'ঝালকাঠি' },
      { id: 47, name: 'পটুয়াখালী' },
      { id: 48, name: 'পিরোজপুর' },
    ],
  },
  {
    id: 6,
    name: 'সিলেট',
    name_en: 'Sylhet',
    districts: [
      { id: 49, name: 'হবিগঞ্জ' },
      { id: 50, name: 'মৌলভীবাজার' },
      { id: 51, name: 'সুনামগঞ্জ' },
      { id: 52, name: 'সিলেট' },
    ],
  },
  {
    id: 7,
    name: 'রংপুর',
    name_en: 'Rangpur',
    districts: [
      { id: 53, name: 'দিনাজপুর' },
      { id: 54, name: 'গাইবান্ধা' },
      { id: 55, name: 'কুড়িগ্রাম' },
      { id: 56, name: 'লালমনিরহাট' },
      { id: 57, name: 'নীলফামারী' },
      { id: 58, name: 'পঞ্চগড়' },
      { id: 59, name: 'রংপুর' },
      { id: 60, name: 'ঠাকুরগাঁও' },
    ],
  },
  {
    id: 8,
    name: 'ময়মনসিংহ',
    name_en: 'Mymensingh',
    districts: [
      { id: 61, name: 'জামালপুর' },
      { id: 62, name: 'ময়মনসিংহ' },
      { id: 63, name: 'নেত্রকোণা' },
      { id: 64, name: 'শেরপুর' },
    ],
  },
]

const categories = computed(() => {
  const remote = categoriesResponse.value?.data
  return (remote && remote.length > 0) ? remote : defaultCategories
})

const areas = computed(() => {
  const remote = areasResponse.value?.data
  return (Array.isArray(remote) && remote.length > 0) ? remote : defaultAreas
})

// Active division for সারা দেশ mega dropdown
const activeDivisionId = ref(null)

const activeDivision = computed(() => {
  if (!areas.value || areas.value.length === 0) return null
  if (activeDivisionId.value) {
    const found = areas.value.find(d => d.id === activeDivisionId.value)
    if (found) return found
  }
  return areas.value[0]
})

// Helpers for URLs and Active state
const getCategoryUrl = (cat) => {
  if (cat.slug === 'video') return '/video'
  return `/category/${cat.slug}`
}

const currentArticleCategorySlug = useState('current-article-category-slug', () => '')

const isCategoryActive = (cat) => {
  if (cat.slug === 'video' && route.path === '/video') return true
  if (route.path === `/category/${cat.slug}`) return true
  // Also match if current news article belongs to this category
  if (route.path.startsWith('/news/') && currentArticleCategorySlug.value === cat.slug) return true
  if (cat.children && cat.children.length > 0) {
    return cat.children.some(child => {
      if (route.path === `/category/${child.slug}`) return true
      if (route.path.startsWith('/news/') && currentArticleCategorySlug.value === child.slug) return true
      if (child.children && child.children.length > 0) {
        return child.children.some(sub => {
          if (route.path === `/category/${sub.slug}`) return true
          if (route.path.startsWith('/news/') && currentArticleCategorySlug.value === sub.slug) return true
          return false
        })
      }
      return false
    })
  }
  return false
}

// Category icons map
const getCategoryIcon = (slug) => {
  const icons = {
    national: '🇧🇩',
    politics: '🏛️',
    saradesh: '🗺️',
    international: '🌐',
    sports: '🏆',
    cricket: '🏏',
    football: '⚽',
    entertainment: '🎬',
    economy: '💼',
    opinion: '✍️',
    lifestyle: '☕',
    'law-and-court': '⚖️',
    technology: '💻',
    video: '🎥',
    others: '📁',
    modhyaprachyo: '🌍',
    juktorastro: '🗽',
    asia: '🌏',
    europ: '🏰',
    shikkha: '🎓',
    dhormo: '🕌',
    swasthyo: '🩺',
    krishi: '🌾',
    'press-release': '📢',
    'ster-bisesh': '⭐',
    abhawa: '⛅',
    chakri: '💼',
    probas: '✈️',
    campus: '🏫',
    education: '🎓',
    religion: '🕌',
    health: '🩺',
    agriculture: '🌾',
    jobs: '💼',
    probash: '✈️',
  }
  return icons[slug] || '📌'
}

// Mobile category touch navigation & scrolling
const navScrollContainer = ref(null)
const canScrollLeft = ref(false)
const canScrollRight = ref(false)

const updateNavScrollState = () => {
  if (!navScrollContainer.value) return
  const { scrollLeft, scrollWidth, clientWidth } = navScrollContainer.value
  canScrollLeft.value = scrollLeft > 8
  canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 8
}

const scrollNavLeft = () => {
  if (navScrollContainer.value) {
    navScrollContainer.value.scrollBy({ left: -160, behavior: 'smooth' })
  }
}

const scrollNavRight = () => {
  if (navScrollContainer.value) {
    navScrollContainer.value.scrollBy({ left: 160, behavior: 'smooth' })
  }
}

const scrollActiveCategoryIntoView = () => {
  if (!process.client || !navScrollContainer.value) return
  nextTick(() => {
    // ONLY match top-level category tabs, NEVER inner dropdown buttons!
    const activeEl = navScrollContainer.value?.querySelector('.nav-category-tab.bg-red-600')
    if (activeEl) {
      activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' })
    } else {
      // If no category tab is active (e.g. on news page without active tab), reset to start!
      navScrollContainer.value.scrollLeft = 0
    }
    updateNavScrollState()
  })
}

// Drawer state and handling
const isDrawerOpen = ref(false)
const mobileAccordions = ref({})
const mobileDivisionAccordions = ref({})

const closeDrawer = () => {
  isDrawerOpen.value = false
}

const toggleAccordion = (slug) => {
  mobileAccordions.value[slug] = !mobileAccordions.value[slug]
}

const toggleDivisionAccordion = (slug) => {
  mobileDivisionAccordions.value[slug] = !mobileDivisionAccordions.value[slug]
}

// Mobile subcategories quick bar
const activeParentCategorySlug = computed(() => {
  if (!route.path.startsWith('/category/')) return ''
  const currentSlug = String(route.params.slug || route.path.replace('/category/', '').split('/')[0] || '')
  if (!currentSlug || currentSlug === 'latest' || currentSlug === 'sorbosesh') return ''

  const parent = categories.value.find(c => c.slug === currentSlug)
  if (parent && parent.children && parent.children.length > 0) {
    return parent.slug
  }

  for (const cat of categories.value) {
    if (cat.children && cat.children.length > 0) {
      if (cat.children.some(c => c.slug === currentSlug)) {
        return cat.slug
      }
    }
  }

  return ''
})

const activeSubcategories = computed(() => {
  if (!route.path.startsWith('/category/')) return []
  const currentSlug = String(route.params.slug || route.path.replace('/category/', '').split('/')[0] || '')
  if (!currentSlug || currentSlug === 'latest' || currentSlug === 'sorbosesh') return []

  const parent = categories.value.find(c => c.slug === currentSlug)
  if (parent && parent.children && parent.children.length > 0) {
    return parent.children
  }

  for (const cat of categories.value) {
    if (cat.children && cat.children.length > 0) {
      if (cat.children.some(c => c.slug === currentSlug)) {
        return cat.children
      }
    }
  }

  return []
})

// Auto-expand accordion for active category on initial mount & route change
const autoExpandActiveAccordion = () => {
  for (const cat of categories.value) {
    if (isCategoryActive(cat)) {
      if ((cat.children && cat.children.length > 0) || cat.slug === 'saradesh') {
        mobileAccordions.value[cat.slug] = true
      }
    }
  }
}

watch(() => route.path, () => {
  autoExpandActiveAccordion()
  scrollActiveCategoryIntoView()
}, { immediate: true })

watch(categories, () => {
  autoExpandActiveAccordion()
  updateNavScrollState()
})

watch(currentArticleCategorySlug, () => {
  if (route.path.startsWith('/news/')) {
    scrollActiveCategoryIntoView()
  }
})

// Automatically close drawer on page change
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
  updateCurrentTime()
  if (process.client) {
    timeInterval = setInterval(updateCurrentTime, 1000)
    window.addEventListener('keydown', onKeydown)
    if (navScrollContainer.value) {
      navScrollContainer.value.addEventListener('scroll', updateNavScrollState, { passive: true })
      window.addEventListener('resize', updateNavScrollState, { passive: true })
      setTimeout(() => {
        updateNavScrollState()
        scrollActiveCategoryIntoView()
      }, 150)
    }
  }
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
    timeInterval = null
  }
  if (process.client) {
    window.removeEventListener('keydown', onKeydown)
    if (navScrollContainer.value) {
      navScrollContainer.value.removeEventListener('scroll', updateNavScrollState)
    }
    window.removeEventListener('resize', updateNavScrollState)
    document.body.style.overflow = ''
  }
})
</script>
