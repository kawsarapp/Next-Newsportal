<template>
  <div class="space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 md:pb-8">
    <!-- Breadcrumb & Page Header -->
    <div class="border-b border-gray-200 pb-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
          <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
          <span class="text-gray-300">/</span>
          <span class="text-red-600 font-semibold">ডিজিটাল ই-পেপার</span>
        </nav>
        <div class="flex items-center gap-3">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
            <span class="p-2 bg-red-600 text-white rounded-xl shadow-xs">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
              </svg>
            </span>
            দৈনিক বাংলা — ই-পেপার সংস্করণ
          </h1>
          <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
            লাইভ সংস্করণ
          </span>
        </div>
      </div>

      <!-- Edition Selector & Quick Actions -->
      <div class="flex flex-wrap items-center gap-2.5">
        <!-- Date Selector -->
        <div class="relative flex items-center bg-white border border-gray-300 rounded-xl px-3 py-1.5 shadow-2xs text-xs">
          <svg class="w-4 h-4 text-gray-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span class="text-gray-500 mr-1.5 hidden xs:inline">তারিখ:</span>
          <select 
            v-model="selectedDate" 
            class="bg-transparent font-bold text-gray-800 outline-none cursor-pointer pr-3"
          >
            <option v-for="d in availableDates" :key="d.value" :value="d.value">
              {{ d.label }}
            </option>
          </select>
        </div>

        <!-- Full PDF Download Button -->
        <button
          @click="downloadPage('pdf')"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer"
          title="সম্পূর্ণ পাতা PDF ডাউনলোড করুন"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          <span>PDF ডাউনলোড</span>
        </button>

        <!-- Social Share Button -->
        <button
          @click="openShareModal"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition border border-gray-200 cursor-pointer"
          title="ই-পেপার শেয়ার করুন"
        >
          <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
          </svg>
          <span class="hidden sm:inline">শেয়ার</span>
        </button>
      </div>
    </div>

    <!-- TOP SPONSORED AD BANNER (Digital Monetization) -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-950 via-slate-900 to-red-900 text-white p-4 sm:p-5 shadow-sm border border-red-900/40">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase bg-amber-400 text-black rounded">
            {{ epaperData?.monetization?.top_banner?.badge || 'স্পনসরড বিজ্ঞাপন' }}
          </span>
          <div>
            <h4 class="text-sm sm:text-base font-bold text-amber-300">
              {{ epaperData?.monetization?.top_banner?.title || 'গ্রামীণফোন স্মার্ট ৫জি নেটওয়ার্ক' }}
            </h4>
            <p class="text-xs text-gray-300">
              {{ epaperData?.monetization?.top_banner?.tagline || 'দেশজুড়ে সুপারফাস্ট ইন্টারনেটের সাথে যুক্ত থাকুন নির্বিঘ্নে' }}
            </p>
          </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <span class="text-xs text-gray-400 hidden lg:inline">{{ epaperData?.monetization?.top_banner?.sponsor || 'Grameenphone 5G' }}</span>
          <a
            :href="epaperData?.monetization?.top_banner?.url || 'https://www.grameenphone.com'"
            target="_blank"
            rel="noopener noreferrer"
            class="px-4 py-2 bg-amber-400 hover:bg-amber-300 active:bg-amber-500 text-slate-950 text-xs font-bold rounded-xl transition shadow-xs inline-flex items-center gap-1.5"
          >
            <span>{{ epaperData?.monetization?.top_banner?.cta || 'অফার দেখুন' }}</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </a>
        </div>
      </div>
    </div>

    <!-- E-PAPER WORKSPACE -->
    <div class="bg-gray-100 rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
      <!-- CONTROL TOOLBAR: Dual Mode Switcher, Page Selector, Bottom Sheet Trigger & Zoom -->
      <div class="bg-white border-b border-gray-200 px-3 sm:px-4 py-3 flex flex-wrap items-center justify-between gap-3 sticky top-0 z-20 shadow-2xs">
        
        <!-- Left: Dual View Mode Segmented Switcher -->
        <div class="flex items-center gap-2">
          <div class="inline-flex p-1 bg-gray-100 rounded-xl border border-gray-200">
            <button
              @click="viewMode = 'broadsheet'"
              :class="[
                'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                viewMode === 'broadsheet'
                  ? 'bg-white text-red-600 shadow-2xs font-extrabold'
                  : 'text-gray-600 hover:text-gray-900'
              ]"
              title="ঐতিহ্যবাহী পত্রিকা ব্রডশিট ভিউ"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
              </svg>
              <span>পত্রিকা ভিউ</span>
            </button>
            <button
              @click="viewMode = 'feed'"
              :class="[
                'px-2.5 sm:px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer',
                viewMode === 'feed'
                  ? 'bg-white text-red-600 shadow-2xs font-extrabold'
                  : 'text-gray-600 hover:text-gray-900'
              ]"
              title="মোবাইল-বান্ধব সহজ পঠন ভিউ"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h7"/>
              </svg>
              <span>সহজ পঠন ভিউ</span>
            </button>
          </div>

          <!-- Bottom Sheet Drawer Trigger Button (All screens) -->
          <button
            @click="openBottomSheet"
            class="px-2.5 sm:px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
            title="সকল পাতার ড্রয়ার খুলুন"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="hidden sm:inline">পৃষ্ঠা নির্বাচন:</span>
            <span>({{ activePage + 1 }}/{{ epaperData?.pages?.length || 6 }})</span>
          </button>
        </div>

        <!-- Center: Desktop Page Switching Pill Bar -->
        <div class="hidden lg:flex items-center gap-1.5 overflow-x-auto py-1">
          <button
            @click="prevPage"
            :disabled="activePage === 0"
            class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 transition"
            title="পূর্ববর্তী পাতা"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>

          <div class="flex items-center gap-1.5">
            <button
              v-for="(page, idx) in epaperData?.pages"
              :key="page.page_no"
              @click="activePage = idx"
              :class="[
                'px-2.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1 whitespace-nowrap cursor-pointer',
                activePage === idx 
                  ? 'bg-red-600 text-white shadow-xs' 
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border border-transparent'
              ]"
            >
              <span>পৃষ্ঠা {{ page.page_no }}</span>
            </button>
          </div>

          <button
            @click="nextPage"
            :disabled="activePage === (epaperData?.pages?.length || 1) - 1"
            class="p-2 rounded-lg border border-gray-200 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed text-gray-700 transition"
            title="পরবর্তী পাতা"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>

        <!-- Right Side: Zoom Controls (In Broadsheet mode) & Fullscreen Switcher -->
        <div class="flex items-center gap-2">
          <!-- Hotspots Highlight Toggle (Broadsheet mode only) -->
          <button
            v-if="viewMode === 'broadsheet'"
            @click="showHotspots = !showHotspots"
            :class="[
              'px-2.5 py-1.5 rounded-lg text-xs font-medium border flex items-center gap-1.5 transition cursor-pointer',
              showHotspots 
                ? 'bg-red-50 text-red-700 border-red-300' 
                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
            ]"
            title="সংবাদ কাটিং জোন নির্দেশক চালু বা বন্ধ করুন"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span class="hidden sm:inline">কাটিং জোন:</span>
            <span class="font-bold">{{ showHotspots ? 'চালু' : 'বন্ধ' }}</span>
          </button>

          <!-- Zoom In / Zoom Out Controls (Broadsheet mode only) -->
          <div v-if="viewMode === 'broadsheet'" class="flex items-center bg-gray-50 border border-gray-200 rounded-xl p-0.5">
            <button
              @click="zoomOut"
              :disabled="zoomLevel <= 60"
              class="p-1.5 hover:bg-white rounded-lg text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
              title="জুম আউট (-)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
              </svg>
            </button>
            <span class="px-2 text-xs font-mono font-bold text-gray-800 min-w-[45px] text-center">
              {{ zoomLevel }}%
            </span>
            <button
              @click="zoomIn"
              :disabled="zoomLevel >= 180"
              class="p-1.5 hover:bg-white rounded-lg text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition"
              title="জুম ইন (+)"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
            </button>
            <button
              @click="resetZoom"
              class="hidden sm:inline-block px-2 py-1 text-[11px] font-semibold text-gray-600 hover:text-red-600 border-l border-gray-200 transition"
              title="রিসেট ভিউ (১০০%)"
            >
              রিসেট
            </button>
          </div>

          <!-- Fullscreen Toggle -->
          <button
            @click="toggleFullscreen"
            class="p-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-gray-700 transition"
            :title="isFullscreen ? 'ফুলস্ক্রিন থেকে বের হন' : 'ফুলস্ক্রিন মোড'"
          >
            <svg v-if="!isFullscreen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0 0l-5-5m-6 16H4m0 0v-4m0 4l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
            </svg>
            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Current Page Information Ribbon -->
      <div class="bg-stone-50 border-b border-stone-200 px-4 sm:px-6 py-2.5 flex flex-wrap items-center justify-between gap-2 text-xs text-stone-600">
        <div class="flex items-center gap-2">
          <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-600 shrink-0"></span>
          <span class="font-bold text-stone-900">পৃষ্ঠা {{ currentPage?.page_no }}: {{ currentPage?.title }}</span>
          <span class="text-stone-400 hidden sm:inline">•</span>
          <span class="text-stone-600 hidden sm:inline">{{ currentPage?.subtitle }}</span>
        </div>
        <div class="flex items-center gap-3 text-[11px]">
          <span class="hidden md:inline text-stone-500">সোয়াইপ করে পাতা পরিবর্তন করুন ‹ ›</span>
          <span class="hidden md:inline text-stone-300">|</span>
          <button @click="downloadPage('image')" class="hover:text-red-600 font-semibold underline cursor-pointer flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>ইমেজ (JPG) ডাউনলোড</span>
          </button>
        </div>
      </div>

      <!-- MAIN CONTENT VIEWPORT CONTAINER WITH MOBILE TOUCH GESTURES -->
      <div 
        ref="viewerContainer"
        @touchstart="handleTouchStart"
        @touchend="handleTouchEnd"
        class="relative transition-all duration-150"
      >
        <!-- ================= MODE 1: TRADITIONAL BROADSHEET CANVAS ================= -->
        <div 
          v-if="viewMode === 'broadsheet'"
          class="p-2 sm:p-8 overflow-auto flex justify-start md:justify-center bg-stone-300 min-h-[700px] relative"
        >
          <!-- Mobile Scroll / Tip hint -->
          <div class="absolute top-2 left-1/2 -translate-x-1/2 md:hidden z-10 pointer-events-none">
            <span class="px-2.5 py-1 bg-stone-900/80 text-white text-[10px] font-sans rounded-full backdrop-blur-xs flex items-center gap-1">
              <span>👈 সোয়াইপ করুন অথবা জুম করুন 👉</span>
            </span>
          </div>

          <!-- SCALED NEWSPAPER SHEET -->
          <div 
            :style="{ transform: `scale(${zoomLevel / 100})`, transformOrigin: 'top center' }"
            class="transition-transform duration-200 ease-out will-change-transform shadow-2xl rounded-sm my-2"
          >
            <!-- Authentic Broadsheet Newspaper Canvas -->
            <div 
              class="w-[880px] bg-[#fcfbf7] text-stone-900 border border-stone-400 p-8 select-none relative font-serif"
              style="min-height: 1220px; box-shadow: inset 0 0 60px rgba(0,0,0,0.03);"
            >
              <!-- NEWSPAPER MASTHEAD (HEADER) -->
              <div class="border-b-4 border-stone-900 pb-3 mb-4">
                <!-- Top Info Bar -->
                <div class="flex items-center justify-between text-[11px] font-sans text-stone-600 border-b border-stone-300 pb-1 mb-2">
                  <div>
                    <strong>বর্ষ ৫০</strong> • <strong>সংখ্যা ২৭৪</strong> • <strong>রেজিস্ট্রেশন নং: ডিএ-১২৫</strong>
                  </div>
                  <div>
                    ঢাকা, বাংলাদেশ • {{ currentPage?.edition_date || epaperData?.edition_date_formatted }} • মূল্য: ১০.০০ টাকা
                  </div>
                  <div class="font-bold text-red-700 uppercase tracking-widest">
                    {{ currentPage?.edition || 'ঢাকা সংস্করণ' }}
                  </div>
                </div>

                <!-- Big Bold Masthead Title -->
                <div class="text-center py-2 relative">
                  <div class="text-xs font-sans tracking-widest text-stone-500 uppercase mb-1">
                    স্বাধীন ও নিরপেক্ষ সাংবাদিকতার পথিকৃৎ
                  </div>
                  <h2 class="text-5xl font-black tracking-tight text-stone-900 font-serif drop-shadow-2xs">
                    দৈনিক বাংলা
                  </h2>
                  <div class="text-[11px] font-sans text-stone-600 italic mt-1">
                    DAINIK BANGLA • সত্য ও ন্যায়ের নির্ভীক কণ্ঠস্বর
                  </div>
                </div>

                <!-- Page Subhead Ribbon -->
                <div class="flex items-center justify-between bg-stone-900 text-[#fcfbf7] px-4 py-1.5 text-xs font-sans font-bold uppercase tracking-wider mt-2">
                  <span>পৃষ্ঠা {{ currentPage?.page_no }}: {{ currentPage?.title }}</span>
                  <span>{{ currentPage?.category }}</span>
                  <span>ডিজিটাল ই-পেপার সংস্করণ</span>
                </div>
              </div>

              <!-- BROADSHEET EDITORIAL CONTENT & HOTSPOT GRID -->
              <div class="relative w-full" style="min-height: 980px;">
                <!-- REALISTIC NEWSPAPER LAYOUT BACKDROP BASED ON ACTIVE PAGE -->
                <div class="space-y-6">
                  <!-- Top Section / Lead Articles Grid -->
                  <div class="grid grid-cols-12 gap-5 pb-6 border-b-2 border-stone-800">
                    <!-- Main Lead Story (Columns 1-8) -->
                    <div 
                      v-if="currentPage?.clippings?.[0]"
                      @click="openClipping(currentPage.clippings[0])"
                      class="col-span-8 pr-4 border-r border-stone-300 cursor-pointer group transition-all"
                    >
                      <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] uppercase font-sans font-bold bg-red-600 text-white px-2 py-0.5 rounded-xs">
                          {{ currentPage.clippings[0].kicker }}
                        </span>
                        <span class="text-[10px] font-sans text-stone-500 font-medium">
                          {{ currentPage.clippings[0].author }}
                        </span>
                      </div>

                      <h3 class="text-2xl font-black leading-snug text-stone-900 group-hover:text-red-700 transition font-serif mb-2.5">
                        {{ currentPage.clippings[0].title }}
                      </h3>

                      <!-- Photo & Caption -->
                      <div class="mb-3 overflow-hidden border border-stone-400 bg-stone-200 relative">
                        <img 
                          :src="currentPage.clippings[0].image" 
                          :alt="currentPage.clippings[0].title"
                          class="w-full h-56 object-cover filter contrast-105 group-hover:scale-102 transition duration-300"
                        />
                        <div class="p-1.5 bg-stone-100 text-[10px] font-sans text-stone-600 italic border-t border-stone-300 leading-tight">
                          {{ currentPage.clippings[0].caption }}
                        </div>
                        <div class="absolute top-2 right-2 bg-stone-900/80 text-white text-[10px] font-sans px-2 py-0.5 rounded backdrop-blur-xs flex items-center gap-1">
                          <span>🔍 খবরটি পড়তে ক্লিক করুন</span>
                        </div>
                      </div>

                      <!-- Multi-column newspaper paragraph simulation -->
                      <div class="grid grid-cols-2 gap-4 text-xs font-serif text-stone-800 leading-relaxed text-justify">
                        <p>
                          <span class="float-left text-3xl font-black leading-none mr-2 text-stone-900 font-serif">স</span>
                          {{ currentPage.clippings[0].summary }}
                        </p>
                        <p class="text-stone-600">
                          সংশ্লিষ্ট নীতিনির্ধারকদের বরাত দিয়ে জানানো হয়েছে, মাঠপর্যায়ে কাজের কঠোর জবাবদিহিতা নিশ্চিত করা হবে। কাজের গতি তদারকি করতে আধুনিক সফটওয়্যার মনিটরিং ব্যবহৃত হচ্ছে... 
                          <span class="text-red-700 font-bold group-hover:underline block mt-1 font-sans text-[11px]">[বিস্তারিত কাটিং পড়ুন ➔]</span>
                        </p>
                      </div>
                    </div>

                    <!-- Right Secondary Story (Columns 9-12) -->
                    <div 
                      v-if="currentPage?.clippings?.[1]"
                      @click="openClipping(currentPage.clippings[1])"
                      class="col-span-4 pl-1 cursor-pointer group transition-all flex flex-col justify-between"
                    >
                      <div>
                        <div class="text-[10px] uppercase font-sans font-bold text-red-600 mb-1">
                          {{ currentPage.clippings[1].kicker }}
                        </div>
                        <h4 class="text-lg font-bold leading-snug text-stone-900 group-hover:text-red-700 transition font-serif mb-2">
                          {{ currentPage.clippings[1].title }}
                        </h4>

                        <div class="mb-2.5 overflow-hidden border border-stone-300 bg-stone-200">
                          <img 
                            :src="currentPage.clippings[1].image" 
                            :alt="currentPage.clippings[1].title"
                            class="w-full h-36 object-cover filter contrast-105 group-hover:scale-102 transition"
                          />
                        </div>

                        <p class="text-[11px] font-serif text-stone-700 leading-normal text-justify mb-2">
                          {{ currentPage.clippings[1].summary }}
                        </p>
                      </div>

                      <div class="pt-2 border-t border-stone-200 text-right">
                        <span class="text-[10px] font-sans font-bold text-red-700 group-hover:underline">
                          কাটিং দেখুন ➔
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Bottom Section / Additional Hotspot Articles -->
                  <div class="grid grid-cols-12 gap-5 pt-2">
                    <!-- Bottom Left Story (Columns 1-6) -->
                    <div 
                      v-if="currentPage?.clippings?.[2]"
                      @click="openClipping(currentPage.clippings[2])"
                      class="col-span-6 pr-4 border-r border-stone-300 cursor-pointer group transition"
                    >
                      <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] uppercase font-sans font-bold bg-stone-800 text-white px-1.5 py-0.5 rounded-xs">
                          {{ currentPage.clippings[2].kicker }}
                        </span>
                        <span class="text-[10px] font-sans text-stone-500">
                          {{ currentPage.clippings[2].author }}
                        </span>
                      </div>
                      <h4 class="text-base font-bold text-stone-900 group-hover:text-red-700 transition mb-2">
                        {{ currentPage.clippings[2].title }}
                      </h4>
                      <div class="flex gap-3">
                        <img 
                          :src="currentPage.clippings[2].image" 
                          class="w-24 h-20 object-cover border border-stone-300 shrink-0"
                        />
                        <p class="text-[11px] font-serif text-stone-700 leading-relaxed text-justify">
                          {{ currentPage.clippings[2].summary }}
                        </p>
                      </div>
                      <div class="mt-2 text-right">
                        <span class="text-[10px] font-sans font-bold text-red-700 group-hover:underline">
                          ক্লিক করে বিস্তারিত পড়ুন ➔
                        </span>
                      </div>
                    </div>

                    <!-- Bottom Right Story (Columns 7-12) -->
                    <div 
                      v-if="currentPage?.clippings?.[3]"
                      @click="openClipping(currentPage.clippings[3])"
                      class="col-span-6 pl-2 cursor-pointer group transition"
                    >
                      <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] uppercase font-sans font-bold bg-stone-800 text-white px-1.5 py-0.5 rounded-xs">
                          {{ currentPage.clippings[3].kicker }}
                        </span>
                        <span class="text-[10px] font-sans text-stone-500">
                          {{ currentPage.clippings[3].author }}
                        </span>
                      </div>
                      <h4 class="text-base font-bold text-stone-900 group-hover:text-red-700 transition mb-2">
                        {{ currentPage.clippings[3].title }}
                      </h4>
                      <div class="flex gap-3">
                        <img 
                          :src="currentPage.clippings[3].image" 
                          class="w-24 h-20 object-cover border border-stone-300 shrink-0"
                        />
                        <p class="text-[11px] font-serif text-stone-700 leading-relaxed text-justify">
                          {{ currentPage.clippings[3].summary }}
                        </p>
                      </div>
                      <div class="mt-2 text-right">
                        <span class="text-[10px] font-sans font-bold text-red-700 group-hover:underline">
                          ক্লিক করে বিস্তারিত পড়ুন ➔
                        </span>
                      </div>
                    </div>

                    <!-- Fallback Notice Box if fewer articles -->
                    <div 
                      v-if="!currentPage?.clippings?.[3]"
                      class="col-span-6 pl-2 p-3 bg-stone-100 border border-dashed border-stone-400 rounded flex flex-col justify-center items-center text-center"
                    >
                      <span class="text-[10px] font-sans uppercase font-bold text-stone-400 tracking-wider">ই-পেপার নোটিস</span>
                      <h5 class="text-sm font-bold text-stone-800 mt-1">দৈনিক বাংলা ই-পেপার আর্কাইভে স্বাগতম</h5>
                      <p class="text-[11px] text-stone-600 mt-1">যেকোনো সংবাদে ক্লিক করে সম্পূর্ণ প্রতিবেদন পড়ুন, অডিও শুনুন ও সোশ্যাল মিডিয়ায় শেয়ার করুন।</p>
                    </div>
                  </div>
                </div>

                <!-- INTERACTIVE OVERLAY HOTSPOT ZONES -->
                <template v-if="showHotspots">
                  <div
                    v-for="clip in currentPage?.clippings"
                    :key="clip.id"
                    @click.stop="openClipping(clip)"
                    :style="{
                      top: `${clip.hotspot.top}%`,
                      left: `${clip.hotspot.left}%`,
                      width: `${clip.hotspot.width}%`,
                      height: `${clip.hotspot.height}%`
                    }"
                    class="absolute border-2 border-dashed border-red-500/70 hover:border-red-600 bg-red-500/5 hover:bg-red-500/15 cursor-pointer transition-all duration-150 rounded flex flex-col justify-start items-end p-2 group pointer-events-auto"
                    :title="`ক্লিক করে পড়ুন: ${clip.title}`"
                  >
                    <div class="bg-red-600 text-white text-[10px] font-sans font-bold px-2 py-0.5 rounded shadow-sm opacity-90 group-hover:opacity-100 flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                      </svg>
                      <span>{{ clip.category }}</span>
                    </div>
                  </div>
                </template>
              </div>

              <!-- Broadsheet Footer & Barcode -->
              <div class="border-t-2 border-stone-900 pt-3 mt-6 flex items-center justify-between text-[10px] font-sans text-stone-500">
                <div>
                  © ২০২৬ দৈনিক বাংলা পাবলিশিং লিমিটেড। সর্বস্বত্ব সংরক্ষিত।
                </div>
                <div class="flex items-center gap-3">
                  <span class="font-mono tracking-widest text-stone-700">|||| | ||||| || |||||| ||| |||</span>
                  <span>পৃষ্ঠা {{ currentPage?.page_no }} এর সমাপ্তি</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= MODE 2: RESPONSIVE EASY READ ARTICLE FEED ================= -->
        <div v-else-if="viewMode === 'feed'" class="p-4 sm:p-6 space-y-6 max-w-4xl mx-auto min-h-[600px]">
          <!-- Feed Header Ribbon -->
          <div class="flex items-center justify-between pb-3 border-b border-gray-200 text-xs text-gray-500">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-red-600 shrink-0"></span>
              <span class="font-bold text-gray-900 text-sm">পৃষ্ঠা {{ currentPage?.page_no }}: {{ currentPage?.title }}</span>
              <span>•</span>
              <span class="font-medium text-gray-600">মোট {{ currentPage?.clippings?.length || 0 }}টি প্রতিবেদন</span>
            </div>
            <span class="text-stone-500 hidden sm:inline">মোবাইল-বান্ধব সহজ পাঠযোগ্য সংস্করণ</span>
          </div>

          <!-- Mobile Swipe Hint -->
          <div class="flex sm:hidden justify-center">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-stone-200/70 text-stone-700 rounded-full text-[11px] font-medium shadow-2xs">
              <span>‹</span>
              <span>সোয়াইপ করে পাতা পরিবর্তন করুন</span>
              <span>›</span>
            </div>
          </div>

          <!-- Vertical Article Feed Cards -->
          <div class="space-y-4 sm:space-y-6">
            <article
              v-for="(clip, idx) in currentPage?.clippings"
              :key="clip.id || idx"
              class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs hover:shadow-md transition duration-200 group flex flex-col md:flex-row"
            >
              <!-- Card Image (Left/Top) -->
              <div v-if="clip.image" class="md:w-2/5 shrink-0 relative overflow-hidden bg-stone-100 max-h-56 md:max-h-none">
                <img
                  :src="clip.image"
                  :alt="clip.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition duration-300 min-h-[180px] md:min-h-full"
                  loading="lazy"
                />
                <div class="absolute top-3 left-3">
                  <span class="px-2 py-0.5 bg-red-600 text-white text-[11px] font-bold rounded uppercase tracking-wider shadow-xs">
                    {{ clip.category }}
                  </span>
                </div>
              </div>

              <!-- Card Content (Right/Bottom) -->
              <div class="p-4 sm:p-6 flex-1 flex flex-col justify-between">
                <div>
                  <!-- Kicker, Author, Read Time -->
                  <div class="flex flex-wrap items-center gap-2 mb-2 text-xs">
                    <span class="font-bold text-red-600 uppercase tracking-wide">
                      {{ clip.kicker }}
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="text-gray-600 font-medium">{{ clip.author }}</span>
                    <span class="text-gray-300">•</span>
                    <span class="text-gray-400">{{ clip.read_time }}</span>
                  </div>

                  <!-- Headline -->
                  <h3 
                    @click="openClipping(clip)"
                    class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-red-700 transition cursor-pointer leading-snug font-serif mb-2"
                  >
                    {{ clip.title }}
                  </h3>

                  <!-- Excerpt -->
                  <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-serif line-clamp-3 mb-4">
                    {{ clip.summary }}
                  </p>
                </div>

                <!-- Actions Footer -->
                <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                  <div class="flex items-center gap-2">
                    <!-- 1-Tap Read Full Story -->
                    <button
                      @click="openClipping(clip)"
                      class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-xl font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer"
                    >
                      <span>সম্পূর্ণ পড়ুন</span>
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                      </svg>
                    </button>

                    <!-- Listen Audio Quick Button -->
                    <button
                      @click="openClippingWithAudio(clip)"
                      class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-xl font-semibold transition flex items-center gap-1 cursor-pointer"
                      title="খবর শুনুন"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                      </svg>
                      <span>শুনুন</span>
                    </button>
                  </div>

                  <!-- Quick Native Share -->
                  <button
                    @click="nativeShareClipping(clip)"
                    class="p-2 text-gray-500 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition"
                    title="শেয়ার করুন"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                  </button>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>

      <!-- BOTTOM PAGE THUMBNAILS CAROUSEL / STRIP (Desktop & Tablet) -->
      <div class="hidden sm:block bg-white border-t border-gray-200 p-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            সকল পাতার থাম্বনেইল প্রিভিউ (মোট {{ epaperData?.pages?.length || 6 }} পৃষ্ঠা)
          </span>
          <span class="text-xs text-gray-500">
            যেকোনো পৃষ্ঠায় ক্লিক করে সরাসরি যান
          </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
          <button
            v-for="(page, idx) in epaperData?.pages"
            :key="page.page_no"
            @click="activePage = idx"
            :class="[
              'group relative rounded-xl border p-2 text-left transition flex flex-col justify-between cursor-pointer',
              activePage === idx 
                ? 'border-red-600 ring-2 ring-red-600/30 bg-red-50/50' 
                : 'border-gray-200 hover:border-gray-300 bg-gray-50 hover:bg-white'
            ]"
          >
            <!-- Simulated Page Thumbnail Graphic -->
            <div class="w-full h-24 bg-[#fcfbf7] border border-stone-300 rounded p-1.5 flex flex-col justify-between overflow-hidden shadow-2xs mb-2">
              <div class="border-b border-stone-300 pb-1 flex justify-between items-center text-[7px] text-stone-500 font-mono">
                <span>পৃষ্ঠা {{ page.page_no }}</span>
                <span>দৈনিক বাংলা</span>
              </div>
              <div class="space-y-1 my-auto">
                <div class="h-1 bg-stone-800 rounded-full w-4/5"></div>
                <div class="h-1 bg-stone-400 rounded-full w-full"></div>
                <div class="h-1 bg-stone-400 rounded-full w-2/3"></div>
              </div>
              <div class="text-[8px] font-bold text-red-700 truncate font-sans">
                {{ page.title }}
              </div>
            </div>

            <div>
              <div class="text-xs font-bold text-gray-900 group-hover:text-red-600 flex items-center justify-between">
                <span>পাতা {{ page.page_no }}</span>
                <span v-if="activePage === idx" class="text-[10px] text-red-600 font-semibold">বর্তমান</span>
              </div>
              <div class="text-[11px] text-gray-500 truncate">
                {{ page.category }}
              </div>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- BOTTOM SPONSORED AD BANNER (Digital Monetization) -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-pink-950 via-slate-900 to-rose-950 text-white p-4 sm:p-5 shadow-sm border border-pink-900/40">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <span class="px-2 py-0.5 text-[10px] font-bold tracking-wider uppercase bg-pink-500 text-white rounded">
            {{ epaperData?.monetization?.bottom_banner?.badge || 'স্পনসরড পার্টনার' }}
          </span>
          <div>
            <h4 class="text-sm sm:text-base font-bold text-pink-300">
              {{ epaperData?.monetization?.bottom_banner?.title || 'বিকাশ পেমেন্টে ক্যাশব্যাক উৎসব' }}
            </h4>
            <p class="text-xs text-gray-300">
              {{ epaperData?.monetization?.bottom_banner?.tagline || 'ই-পেপার সাবস্ক্রিপশন ও অনলাইন পেমেন্টে আকর্ষণীয় ক্যাশব্যাক বোনাস' }}
            </p>
          </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
          <span class="text-xs text-gray-400 hidden lg:inline">{{ epaperData?.monetization?.bottom_banner?.sponsor || 'bKash Limited' }}</span>
          <a
            :href="epaperData?.monetization?.bottom_banner?.url || 'https://www.bkash.com'"
            target="_blank"
            rel="noopener noreferrer"
            class="px-4 py-2 bg-pink-500 hover:bg-pink-600 active:bg-pink-700 text-white text-xs font-bold rounded-xl transition shadow-xs inline-flex items-center gap-1.5"
          >
            <span>{{ epaperData?.monetization?.bottom_banner?.cta || 'ক্যাশব্যাক নিন' }}</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
          </a>
        </div>
      </div>
    </div>

    <!-- ================= MOBILE STICKY BOTTOM BAR ================= -->
    <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-gray-200 py-2.5 px-4 shadow-xl md:hidden">
      <div class="max-w-md mx-auto flex items-center justify-between gap-2">
        <!-- Prev Button -->
        <button
          @click="prevPage"
          :disabled="activePage === 0"
          class="p-2 rounded-xl border border-gray-200 text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-100 transition active:scale-95"
          title="পূর্ববর্তী পাতা"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>

        <!-- Center Button: Open Bottom Sheet Drawer -->
        <button
          @click="openBottomSheet"
          class="flex-1 py-2 px-3 bg-stone-100 hover:bg-stone-200 active:bg-stone-300 rounded-xl text-xs font-bold text-gray-800 flex items-center justify-center gap-1.5 border border-gray-300 shadow-2xs transition"
        >
          <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
          </svg>
          <span>পৃষ্ঠা নির্বাচন করুন ({{ activePage + 1 }}/{{ epaperData?.pages?.length || 6 }})</span>
          <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
          </svg>
        </button>

        <!-- Next Button -->
        <button
          @click="nextPage"
          :disabled="activePage === (epaperData?.pages?.length || 1) - 1"
          class="p-2 rounded-xl border border-gray-200 text-gray-700 disabled:opacity-30 disabled:cursor-not-allowed hover:bg-gray-100 transition active:scale-95"
          title="পরবর্তী পাতা"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- ================= MOBILE BOTTOM SHEET THUMBNAIL DRAWER ================= -->
    <Transition name="drawer-slide">
      <div 
        v-if="showBottomSheet" 
        class="fixed inset-0 z-50 overflow-hidden flex flex-col justify-end"
      >
        <!-- Backdrop Overlay -->
        <div 
          class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity"
          @click="closeBottomSheet"
        ></div>

        <!-- Sheet Content Container -->
        <div 
          class="relative bg-white rounded-t-3xl shadow-2xl max-h-[85vh] flex flex-col z-10 border-t border-gray-200 overflow-hidden"
        >
          <!-- Drag handle indicator -->
          <div class="pt-3 pb-1 flex justify-center">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
          </div>

          <!-- Sheet Header -->
          <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
            <div>
              <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                <span>📑</span> সকল পৃষ্ঠা ব্রাউজ করুন
              </h3>
              <p class="text-xs text-gray-500">
                {{ epaperData?.edition_name }} • মোট {{ epaperData?.pages?.length || 6 }} পৃষ্ঠা
              </p>
            </div>
            <button 
              @click="closeBottomSheet"
              class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Thumbnails Grid -->
          <div class="p-4 sm:p-6 overflow-y-auto grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3.5">
            <button
              v-for="(page, idx) in epaperData?.pages"
              :key="page.page_no"
              @click="selectPageFromDrawer(idx)"
              :class="[
                'text-left rounded-2xl p-3 border transition flex flex-col justify-between group relative cursor-pointer',
                activePage === idx 
                  ? 'border-red-600 bg-red-50/60 ring-2 ring-red-600/30' 
                  : 'border-gray-200 bg-gray-50 hover:bg-white hover:border-gray-300'
              ]"
            >
              <div class="w-full h-28 bg-[#fcfbf7] border border-stone-300 rounded-xl p-2 flex flex-col justify-between overflow-hidden shadow-2xs mb-2.5">
                <div class="border-b border-stone-200 pb-1 flex justify-between items-center text-[8px] text-stone-500 font-mono">
                  <span class="font-bold">পৃষ্ঠা {{ page.page_no }}</span>
                  <span>দৈনিক বাংলা</span>
                </div>
                <div class="space-y-1.5 my-auto">
                  <div class="h-1.5 bg-stone-800 rounded-full w-4/5"></div>
                  <div class="h-1 bg-stone-400 rounded-full w-full"></div>
                  <div class="h-1 bg-stone-400 rounded-full w-3/4"></div>
                </div>
                <div class="text-[9px] font-bold text-red-700 truncate">
                  {{ page.title }}
                </div>
              </div>
              <div>
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-gray-900 group-hover:text-red-600">
                    পৃষ্ঠা {{ page.page_no }}
                  </span>
                  <span v-if="activePage === idx" class="text-[10px] font-bold text-red-600 bg-red-100 px-1.5 py-0.5 rounded-full">
                    বর্তমান
                  </span>
                </div>
                <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ page.category }}</p>
              </div>
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- ================= INTERACTIVE ARTICLE CLIPPING MODAL WITH WEB SPEECH AUDIO READER ================= -->
    <Transition name="modal-fade">
      <div 
        v-if="selectedClipping" 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6"
        @click.self="closeClipping"
      >
        <div 
          class="bg-[#fdfcf9] rounded-2xl shadow-2xl max-w-3xl w-full border border-stone-300 overflow-hidden transform transition-all flex flex-col max-h-[92vh]"
          @keydown.esc="closeClipping"
        >
          <!-- Modal Top Bar -->
          <div class="bg-stone-900 text-white px-5 sm:px-6 py-3.5 flex items-center justify-between border-b border-stone-800">
            <div class="flex items-center gap-2 sm:gap-3">
              <span class="px-2 py-0.5 bg-red-600 text-white text-[11px] font-bold rounded uppercase tracking-wider">
                {{ selectedClipping.category }}
              </span>
              <span class="text-xs text-stone-300 hidden sm:inline">
                পৃষ্ঠা {{ currentPage?.page_no }} • {{ selectedClipping.kicker }}
              </span>
            </div>

            <!-- Modal Action Controls -->
            <div class="flex items-center gap-2">
              <!-- Font Size Adjuster -->
              <div class="flex items-center bg-stone-800 rounded-lg p-0.5 text-xs">
                <button 
                  @click="decreaseArticleFont" 
                  class="px-2 py-1 text-stone-300 hover:text-white"
                  title="লেখা ছোট করুন"
                >A-</button>
                <button 
                  @click="increaseArticleFont" 
                  class="px-2 py-1 text-stone-300 hover:text-white font-bold"
                  title="লেখা বড় করুন"
                >A+</button>
              </div>

              <!-- Close Button -->
              <button 
                @click="closeClipping"
                class="p-1.5 text-stone-400 hover:text-white hover:bg-stone-800 rounded-lg transition"
                title="বন্ধ করুন (Esc)"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
          </div>

          <!-- Modal Scrollable Content -->
          <div class="p-5 sm:p-8 overflow-y-auto space-y-4 flex-1 font-serif">
            <!-- Article Header -->
            <div class="border-b border-stone-300 pb-4">
              <div class="text-xs font-sans font-bold text-red-600 uppercase tracking-wider mb-1">
                {{ selectedClipping.kicker }}
              </div>
              <h2 class="text-xl sm:text-3xl font-black text-stone-900 leading-snug tracking-tight">
                {{ selectedClipping.title }}
              </h2>
              <div class="flex flex-wrap items-center justify-between gap-2 mt-3 text-xs font-sans text-stone-500">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-stone-800">{{ selectedClipping.author }}</span>
                  <span>•</span>
                  <span>{{ selectedClipping.published_at }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-stone-600 bg-stone-100 px-2.5 py-1 rounded-md">
                  <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span>{{ selectedClipping.read_time }}</span>
                </div>
              </div>
            </div>

            <!-- BENGALI AUDIO VOICE READER (Web Speech API) -->
            <div class="bg-gradient-to-r from-red-50 via-amber-50 to-red-50 border border-red-200/90 rounded-2xl p-3.5 sm:p-4 my-2 flex flex-col sm:flex-row items-center justify-between gap-3 font-sans">
              <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center shadow-xs shrink-0">
                  <svg v-if="!isSpeaking" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                  </svg>
                  <!-- Animated Waveform Equalizer -->
                  <div v-else class="flex items-end gap-1 h-4">
                    <span class="w-1 bg-white rounded-full animate-bounce h-2"></span>
                    <span class="w-1 bg-white rounded-full animate-bounce h-4" style="animation-delay: 0.15s"></span>
                    <span class="w-1 bg-white rounded-full animate-bounce h-3" style="animation-delay: 0.3s"></span>
                  </div>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <h4 class="text-xs sm:text-sm font-bold text-gray-900">খবর শুনুন (বাংলা অডিও রিডার)</h4>
                    <span v-if="isSpeaking && !isPaused" class="px-2 py-0.5 bg-red-600 text-white text-[10px] font-bold rounded-full animate-pulse">পড়া হচ্ছে</span>
                    <span v-else-if="isPaused" class="px-2 py-0.5 bg-amber-500 text-white text-[10px] font-bold rounded-full">বিরতি</span>
                  </div>
                  <p class="text-[11px] text-gray-600">স্মার্ট ভয়েস অ্যাসিস্ট্যান্ট দিয়ে সম্পূর্ণ খবর শুনুন</p>
                </div>
              </div>

              <!-- Audio Controls Buttons -->
              <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto justify-end">
                <!-- Play / Resume button -->
                <button
                  v-if="!isSpeaking || isPaused"
                  @click="playAudio"
                  class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                  </svg>
                  <span>{{ isPaused ? 'চালিয়ে যান' : '▶ শুনুন' }}</span>
                </button>

                <!-- Pause button -->
                <button
                  v-if="isSpeaking && !isPaused"
                  @click="pauseAudio"
                  class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                  <span>⏸ বিরতি</span>
                </button>

                <!-- Stop button -->
                <button
                  v-if="isSpeaking || isPaused"
                  @click="stopAudio"
                  class="px-3 py-1.5 bg-stone-200 hover:bg-stone-300 text-stone-800 rounded-xl text-xs font-bold transition flex items-center gap-1 cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                  </svg>
                  <span>⏹ বন্ধ</span>
                </button>
              </div>
            </div>

            <!-- Enlarged Article Image -->
            <div v-if="selectedClipping.image" class="border border-stone-300 bg-stone-100 rounded-lg overflow-hidden">
              <img 
                :src="selectedClipping.image" 
                :alt="selectedClipping.title" 
                class="w-full max-h-96 object-cover"
              />
              <p v-if="selectedClipping.caption" class="p-2 text-xs font-sans text-stone-600 italic border-t border-stone-200 bg-stone-50">
                {{ selectedClipping.caption }}
              </p>
            </div>

            <!-- Article Body Text -->
            <div 
              :style="{ fontSize: `${articleFontSize}px` }"
              class="text-stone-800 leading-relaxed space-y-4 font-serif text-justify font-normal"
              v-html="selectedClipping.content"
            ></div>
          </div>

          <!-- Modal Footer: Social Share & Download Article -->
          <div class="bg-stone-100 border-t border-stone-300 px-5 sm:px-6 py-3.5 flex flex-wrap items-center justify-between gap-3 font-sans text-xs">
            <!-- Share Buttons -->
            <div class="flex flex-wrap items-center gap-2">
              <!-- Native Share API Button -->
              <button 
                @click="nativeShareClipping(selectedClipping)"
                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold flex items-center gap-1.5 transition shadow-2xs cursor-pointer"
                title="শেয়ার করুন"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                <span>শেয়ার</span>
              </button>

              <button 
                @click="shareOnFacebook(selectedClipping)"
                class="px-2.5 py-1.5 bg-[#1877F2] hover:bg-[#166fe5] text-white rounded-lg font-bold flex items-center gap-1 transition"
                title="Facebook এ শেয়ার করুন"
              >
                <span>FB</span>
              </button>

              <button 
                @click="shareOnWhatsApp(selectedClipping)"
                class="px-2.5 py-1.5 bg-[#25D366] hover:bg-[#20ba59] text-white rounded-lg font-bold flex items-center gap-1 transition"
                title="WhatsApp এ শেয়ার করুন"
              >
                <span>WhatsApp</span>
              </button>

              <button 
                @click="copyArticleLink(selectedClipping)"
                class="px-2.5 py-1.5 bg-stone-200 hover:bg-stone-300 text-stone-800 rounded-lg font-bold transition flex items-center gap-1"
                title="লিঙ্ক কপি করুন"
              >
                <span>{{ copiedText ? '✓ কপি হয়েছে' : 'লিঙ্ক কপি' }}</span>
              </button>
            </div>

            <!-- Print & Close -->
            <div class="flex items-center gap-2">
              <button 
                @click="printArticle"
                class="px-3 py-1.5 bg-stone-200 hover:bg-stone-300 text-stone-800 rounded-lg font-semibold transition hidden sm:inline-block"
                title="প্রিন্ট করুন"
              >
                🖨️ প্রিন্ট
              </button>
              <button 
                @click="closeClipping"
                class="px-4 py-1.5 bg-stone-800 hover:bg-stone-900 text-white rounded-lg font-bold transition cursor-pointer"
              >
                বন্ধ করুন
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- GENERAL SOCIAL SHARE MODAL -->
    <Transition name="modal-fade">
      <div 
        v-if="showShareModal" 
        class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
        @click.self="showShareModal = false"
      >
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
              <span>📰</span> ই-পেপার শেয়ার করুন
            </h3>
            <button @click="showShareModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
          </div>
          <p class="text-xs text-gray-600 mb-4">
            {{ currentPage?.title }} ({{ currentPage?.edition_date || epaperData?.edition_date_formatted }}) এর ই-পেপার সংস্করণ আপনার বন্ধুদের সাথে শেয়ার করুন:
          </p>
          <div class="grid grid-cols-2 gap-3 mb-4">
            <button 
              @click="shareEdition('native')"
              class="col-span-2 p-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition flex items-center justify-center gap-2 shadow-xs cursor-pointer"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
              </svg>
              <span>ডিভাইস শেয়ার (Share Sheet)</span>
            </button>
            <button 
              @click="shareEdition('facebook')"
              class="p-2.5 bg-[#1877F2] text-white rounded-xl text-xs font-bold hover:opacity-95 transition flex items-center justify-center gap-2"
            >
              Facebook এ শেয়ার
            </button>
            <button 
              @click="shareEdition('whatsapp')"
              class="p-2.5 bg-[#25D366] text-white rounded-xl text-xs font-bold hover:opacity-95 transition flex items-center justify-center gap-2"
            >
              WhatsApp এ পাঠান
            </button>
          </div>
          <div class="flex items-center gap-2 bg-gray-100 p-2 rounded-xl border border-gray-200">
            <input 
              type="text" 
              :value="currentShareUrl" 
              readonly 
              class="bg-transparent text-xs text-gray-700 w-full outline-none font-mono"
            />
            <button 
              @click="copyShareUrl"
              class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs font-bold shrink-0 hover:bg-red-700 cursor-pointer"
            >
              {{ copiedText ? 'কপি হয়েছে' : 'কপি' }}
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- FLOATING TOAST NOTIFICATION -->
    <Transition name="toast-fade">
      <div 
        v-if="toastMessage" 
        class="fixed bottom-16 sm:bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2 bg-stone-900/90 backdrop-blur-md text-white text-xs sm:text-sm font-semibold rounded-full shadow-lg border border-stone-700 flex items-center gap-2 pointer-events-none"
      >
        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
        <span>{{ toastMessage }}</span>
      </div>
    </Transition>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

// View Mode State: 'broadsheet' or 'feed'
const viewMode = ref('broadsheet')

// Navigation & Viewer State
const activePage = ref(0)
const zoomLevel = ref(100)
const showHotspots = ref(true)
const isFullscreen = ref(false)
const selectedClipping = ref(null)
const articleFontSize = ref(17)
const copiedText = ref(false)
const showShareModal = ref(false)
const showBottomSheet = ref(false)
const selectedDate = ref(new Date().toISOString().split('T')[0])
const viewerContainer = ref(null)
const toastMessage = ref('')
let toastTimer = null

// Audio Reader (Web Speech API) State
const isSpeaking = ref(false)
const isPaused = ref(false)

// Fallback Rich 6 Pages Edition Data
const defaultEpaperData = {
  success: true,
  portal_name: 'দৈনিক বাংলা',
  edition_name: 'ঢাকা সংস্করণ',
  edition_date: '2026-10-10',
  edition_date_formatted: 'শনিবার, ১০ অক্টোবর ২০২৬',
  total_pages: 6,
  monetization: {
    top_banner: {
      title: 'গ্রামীণফোন স্মার্ট ৫জি নেটওয়ার্ক',
      sponsor: 'Grameenphone 5G',
      tagline: 'দেশজুড়ে সুপারফাস্ট ইন্টারনেটের সাথে যুক্ত থাকুন নির্বিঘ্নে',
      cta: 'অফার দেখুন',
      url: 'https://www.grameenphone.com',
      badge: 'স্পনসরড বিজ্ঞাপন'
    },
    bottom_banner: {
      title: 'বিকাশ পেমেন্টে ক্যাশব্যাক উৎসব',
      sponsor: 'bKash Limited',
      tagline: 'ই-পেপার সাবস্ক্রিপশন ও কেনাকাটায় আকর্ষণীয় ক্যাশব্যাক বোনাস',
      cta: 'ক্যাশব্যাক নিন',
      url: 'https://www.bkash.com',
      badge: 'স্পনসরড পার্টনার'
    }
  },
  pages: [
    {
      page_no: 1,
      title: 'প্রথম পাতা',
      category: 'প্রধান শিরোনাম ও লিড স্টোরি',
      subtitle: 'জাতীয় ও আন্তর্জাতিক গুরুত্বপূর্ণ শীর্ষ সংবাদের প্রচ্ছদ',
      edition: 'ঢাকা সংস্করণ',
      edition_date: 'শনিবার, ১০ অক্টোবর ২০২৬',
      pdf_url: '/storage/epaper/pdf/page_1.pdf',
      image_url: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=1400&q=80',
      clippings: [
        {
          id: 'art-1-1',
          title: 'মেগা প্রকল্পগুলোতে স্বচ্ছতা ও নির্দিষ্ট সময়ে বাস্তবায়ন নিশ্চিতের কড়া নির্দেশ',
          kicker: 'শীর্ষ অগ্রাধিকার',
          category: 'প্রধান খবর',
          author: 'নিজস্ব প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:০০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1541888946425-d0fbb186c5f7?auto=format&fit=crop&w=800&q=80',
          caption: 'জাতীয় অর্থনৈতিক নির্বাহী কমিটির বিশেষ পর্যালোচনা সভায় উন্নয়ন কাজের অগ্রগতি মূল্যায়ন।',
          summary: 'চলমান জাতীয় অগ্রাধিকারপ্রাপ্ত মেগা প্রকল্পসমূহের গুণগত মান ও নির্ধারিত মেয়াদের মধ্যে কাজ সমাপ্ত করতে সর্বোচ্চ নজরদারির নির্দেশ দেওয়া হয়েছে। অপচয় রোধে কঠোর আর্থিক তদারকির ওপর গুরুত্বারোপ করা হয়েছে।',
          content: '<p><strong>ঢাকা:</strong> চলমান জাতীয় অগ্রাধিকারপ্রাপ্ত সকল মেগা প্রকল্পে আর্থিক স্বচ্ছতা নিশ্চিত করা এবং নির্ধারিত মেয়াদের মধ্যেই মানসম্পন্ন বাস্তবায়ন নিশ্চিত করতে প্রকল্প পরিচালকদের কঠোর বার্তা দেওয়া হয়েছে। গতকাল অনুষ্ঠিত জাতীয় অর্থনৈতিক নির্বাহী কমিটির বিশেষ পর্যালোচনা সভায় এই দিকনির্দেশনা প্রদান করা হয়।</p><p>সভায় জানানো হয়, প্রতিটি অবকাঠামো উন্নয়ন প্রকল্পে আধুনিক ডিজিটাল মনিটরিং সিস্টেম চালু করা হয়েছে। এর মাধ্যমে ড্রোন সার্ভে ও রিয়েল-টাইম তথ্য যাচাইয়ের ফলে অপ্রয়োজনীয় কালক্ষেপণ ও ব্যয়বৃদ্ধি রোধ করা সম্ভব হবে।</p><p>বিশেষজ্ঞরা অভিমত ব্যক্ত করেছেন, মেগা প্রকল্পগুলো দ্রুত শেষ হলে জাতীয় অর্থনৈতিক প্রবৃদ্ধির গতি বৃদ্ধি পাবে এবং কর্মসংস্থানে নতুন দ্বার উন্মোচিত হবে। সরকার সকল স্টেকহোল্ডারদের সমন্বিতভাবে দায়িত্ব পালনের আহ্বান জানিয়েছে।</p>',
          hotspot: { top: 12, left: 6, width: 58, height: 38 }
        },
        {
          id: 'art-1-2',
          title: 'মূল্যস্ফীতি নিয়ন্ত্রণে খাদ্যশস্য মজুত ও বাজারে সরবরাহ বৃদ্ধির বহুমুখী উদ্যোগ',
          kicker: 'বাজার পরিস্থিতি',
          category: 'জাতীয়',
          author: 'অর্থনৈতিক প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:১৫',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1578916171728-46686eac8d58?auto=format&fit=crop&w=800&q=80',
          caption: 'সরকারি গুদামে পর্যাপ্ত খাদ্যশস্য মজুত রয়েছে বলে খাদ্য অধিদপ্তর নিশ্চিত করেছে।',
          summary: 'নিত্যপ্রয়োজনীয় পণ্যের দাম সহনীয় রাখতে সরকারি গুদামে রেকর্ড খাদ্যশস্য মজুত নিশ্চিত করা হয়েছে এবং খোলা বাজারে ওএমএস কার্যক্রম দ্বিগুণ করা হয়েছে।',
          content: '<p>সাধারণ মানুষের স্বস্তি ফেরাতে খাদ্যশস্য ও নিত্যপ্রয়োজনীয় পণ্যের সরবরাহ চেইন নিরবচ্ছিন্ন রাখতে নানামুখী উদ্যোগ গ্রহণ করেছে সরকার। বন্দরগুলোতে পণ্য খালাসে অগ্রাধিকার প্রদান এবং মজুতদারদের বিরুদ্ধে ভ্রাম্যমাণ আদালতের অভিযান জোরদার করা হয়েছে।</p><p>বাণিজ্য মন্ত্রণালয়ের পক্ষ থেকে জানানো হয়েছে, টিসিবির মাধ্যমে ফ্যামিলি কার্ডধারীদের মাঝে সাশ্রয়ী মূল্যে চাল, ডাল ও ভোজ্যতেল বিতরণ অব্যাহত থাকবে।</p>',
          hotspot: { top: 12, left: 66, width: 28, height: 38 }
        },
        {
          id: 'art-1-3',
          title: 'ডিজিটাল রূপান্তর থেকে স্মার্ট অর্থনীতিতে উত্তরণে নতুন জাতীয় আইসিটি ফ্রেমওয়ার্ক',
          kicker: 'প্রযুক্তি বিপ্লব',
          category: 'তথ্যপ্রযুক্তি',
          author: 'বিজ্ঞান ও প্রযুক্তি ডেস্ক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৩০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?auto=format&fit=crop&w=800&q=80',
          caption: 'রাজধানীতে আয়োজিত জাতীয় তথ্যপ্রযুক্তি কংগ্রেসে স্মার্ট ফ্রেমওয়ার্কের মোড়ক উন্মোচন।',
          summary: 'কৃত্রিম বুদ্ধিমত্তা, ব্লকচেইন ও হাইপার-অটোমেশন নির্ভর স্মার্ট প্রশাসন ও উদ্যোক্তা গড়ে তুলতে জাতীয় ফ্রেমওয়ার্ক আনুষ্ঠানিকভাবে কার্যকর হয়েছে।',
          content: '<p>প্রযুক্তিভিত্তিক উদ্ভাবনে দেশের তরুণ সমাজকে সম্পৃক্ত করতে জাতীয় আইসিটি ফ্রেমওয়ার্ক কার্যকর হয়েছে। এতে স্টার্টআপ তহবিলে সহজ ঋণ এবং গবেষণাগার স্থাপনের বিশেষ বরাদ্দ অন্তর্ভুক্ত রয়েছে।</p><p>সংশ্লিষ্ট নীতিনির্ধারকরা জানিয়েছেন, এই পদক্ষেপের ফলে আগামী তিন বছরে লক্ষাধিক দক্ষ প্রযুক্তিবিদ তৈরি হবে।</p>',
          hotspot: { top: 52, left: 6, width: 44, height: 42 }
        },
        {
          id: 'art-1-4',
          title: 'পরিবেশবান্ধব নবায়নযোগ্য জ্বালানিতে নতুন আন্তর্জাতিক বিনিয়োগ চুক্তি সম্পন্ন',
          kicker: 'সবুজ শক্তি',
          category: 'অর্থনীতি',
          author: 'বিশেষ প্রতিনিধি',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৪৫',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1509391365360-2e959784a276?auto=format&fit=crop&w=800&q=80',
          caption: 'সৌরবিদ্যুৎ ও বায়ুবিদ্যুৎ প্রকল্পে আন্তর্জাতিক অর্থায়নের যৌথ সমঝোতা স্মারক স্বাক্ষর।',
          summary: 'পরিবেশ রক্ষায় কার্বন নিঃসরণ হ্রাসে ৫০০ মেগাওয়াট সক্ষমতার সৌর ও বায়ুবিদ্যুৎ প্রকল্পে নতুন আন্তর্জাতিক বিনিয়োগের চুক্তি স্বাক্ষরিত হয়েছে।',
          content: '<p>বিদ্যুৎ ও জ্বালানি খাতে পরিবেশবান্ধব প্রযুক্তির প্রসারে আন্তর্জাতিক আর্থিক সংস্থার সাথে নতুন অংশীদারিত্ব চুক্তি হয়েছে। প্রকল্পটি বাস্তবায়িত হলে জাতীয় গ্রিডে সবুজ বিদ্যুতের অংশ উল্লেখযোগ্য হারে বৃদ্ধি পাবে।</p>',
          hotspot: { top: 52, left: 52, width: 42, height: 42 }
        }
      ]
    },
    {
      page_no: 2,
      title: 'জাতীয় ও সংসদ',
      category: 'জাতীয় ও সংসদ',
      subtitle: 'আইনসভা কার্যক্রম, প্রশাসনিক সংস্কার ও জাতীয় উন্নয়ন নীতি',
      edition: 'ঢাকা সংস্করণ',
      edition_date: 'শনিবার, ১০ অক্টোবর ২০২৬',
      pdf_url: '/storage/epaper/pdf/page_2.pdf',
      image_url: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=1400&q=80',
      clippings: [
        {
          id: 'art-2-1',
          title: 'সংসদে বাজেট অধিবেশনে জনস্বার্থ সংশ্লিষ্ট তিনটি গুরুত্বপূর্ণ সংস্কার বিল পাস',
          kicker: 'সংসদ অধিবেশন',
          category: 'আইনসভা',
          author: 'সংসদ প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:০০',
          read_time: '৪ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=800&q=80',
          caption: 'জাতীয় সংসদ ভবনে বাজেট অধিবেশনের মুলতবি বৈঠকে আইন প্রণয়ন প্রক্রিয়া সম্পন্ন।',
          summary: 'নাগরিক সেবা সহজীকরণ, পরিবেশ সংরক্ষণ এবং ভোক্তা অধিকার সুরক্ষায় সংসদে আজ তিনটি যুগান্তকারী আইন কণ্ঠভোটে পাস হয়েছে।',
          content: '<p><strong>জাতীয় সংসদ ভবন:</strong> জাতীয় সংসদে আজ দীর্ঘ আলোচনার পর নাগরিক অধিকার ও টেকসই উন্নয়ন সংক্রান্ত তিনটি পৃথক বিল পাস হয়েছে। স্পিকারের সভাপতিত্বে অধিবেশনে সংসদ সদস্যরা বিস্তারিত বিতর্ক শেষে বিলগুলোর পক্ষে ভোট দেন।</p><p>নতুন আইনের আওতায় সরকারি সেবা প্রাপ্তিতে অহেতুক বিলম্ব হলে সংশ্লিষ্ট কর্মকর্তার বিরুদ্ধে জবাবদিহিতা নিশ্চিত করার সুস্পষ্ট ধারা সংযোজন করা হয়েছে।</p>',
          hotspot: { top: 12, left: 6, width: 50, height: 40 }
        },
        {
          id: 'art-2-2',
          title: 'উপকূলীয় অঞ্চলে টেকসই বেড়িবাঁধ নির্মাণে ৩ হাজার কোটি টাকার মেগা প্রকল্প',
          kicker: 'জলবায়ু অভিঘাত',
          category: 'জাতীয় উন্নয়ন',
          author: 'উপকূলীয় প্রতিনিধি',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:২০',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=800&q=80',
          caption: 'ঘূর্ণিঝড় ও জোয়ারের জলোচ্ছ্বাস থেকে রক্ষা পেতে আধুনিক কংক্রিট ব্লক বেড়িবাঁধ নির্মাণ কাজ চলমান।',
          summary: 'উপকূলীয় ১৬টি জেলায় স্থায়ী বেড়িবাঁধ ও আধুনিক জলকপাট নির্মাণে নতুন প্রকল্প একনেক সভায় চূড়ান্ত অনুমোদন লাভ করেছে।',
          content: '<p>উপকূলের প্রান্তিক মানুষকে প্রাকৃতিক বিপর্যয় থেকে স্থায়ী সুরক্ষা প্রদানে বিশ্বমানের জিও-ব্লক প্রযুক্তিতে বেড়িবাঁধ নির্মাণ করা হবে। এতে করে প্রতি বছর বর্ষাকালে বাঁধ ভেঙে প্লাবনের ভোগান্তি চিরতরে দূর হবে বলে আশা করা হচ্ছে।</p>',
          hotspot: { top: 12, left: 58, width: 36, height: 40 }
        },
        {
          id: 'art-2-3',
          title: 'প্রাথমিক ও মাধ্যমিক শিক্ষায় ডিজিটাল স্মার্ট ক্লাসরুম ও নতুন শিক্ষক নিয়োগ',
          kicker: 'শিক্ষা সংস্কার',
          category: 'শিক্ষা',
          author: 'শিক্ষা প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৪০',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80',
          caption: 'গ্রামাঞ্চলের বিদ্যালয়গুলোতে আধুনিক কম্পিউটার ল্যাব ও ইন্টারঅ্যাকটিভ প্যানেল স্থাপন।',
          summary: 'সারাদেশে ৫ হাজার সরকারি বিদ্যালয়ে আধুনিক স্মার্ট ক্লাসরুম চালু ও ১৫ হাজার দক্ষ শিক্ষক নিয়োগের চূড়ান্ত অনুমোদন দেওয়া হয়েছে।',
          content: '<p>শিক্ষার মানোন্নয়ন ও চতুর্থ শিল্পবিপ্লবের উপযোগী মেধা গঠনে প্রাথমিক পর্যায় থেকেই কোডিং ও গণিত অলিম্পিয়াড ভিত্তিক পাঠ্যক্রম অন্তর্ভুক্তির ঘোষণা দিয়েছে প্রাথমিক ও গণশিক্ষা মন্ত্রণালয়।</p>',
          hotspot: { top: 54, left: 6, width: 88, height: 40 }
        }
      ]
    },
    {
      page_no: 3,
      title: 'রাজনীতি ও অভিমত',
      category: 'রাজনীতি ও অভিমত',
      subtitle: 'সম্পাদকীয় মন্তব্য, রাজনৈতিক কলাম ও সমকালীন গভীর বিশ্লেষণ',
      edition: 'ঢাকা সংস্করণ',
      edition_date: 'শনিবার, ১০ অক্টোবর ২০২৬',
      pdf_url: '/storage/epaper/pdf/page_3.pdf',
      image_url: 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=1400&q=80',
      clippings: [
        {
          id: 'art-3-1',
          title: 'সম্পাদকীয়: অর্থনৈতিক স্থিতিশীলতা ও প্রাতিষ্ঠানিক সুশাসনের যৌথ রোডম্যাপ',
          kicker: 'দৈনিক বাংলা সম্পাদকীয়',
          category: 'সম্পাদকীয়',
          author: 'সম্পাদকীয় পরিষদ',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:০০',
          read_time: '৫ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=800&q=80',
          caption: 'গণতান্ত্রিক প্রাতিষ্ঠানিক উৎকর্ষ এবং সুশাসনই জাতীয় অগ্রগতির প্রধান চালিকাশক্তি।',
          summary: 'উন্নয়নের ধারাবাহিকতা বজায় রাখতে হলে ব্যাংকিং খাত, বাজার অর্থনীতি ও শাসনব্যবস্থায় স্বচ্ছতা বজায় রাখার বিকল্প নেই। সংস্কারগুলো দ্রুত ও কার্যকর হওয়া প্রয়োজন।',
          content: '<p>দেশের সামষ্টিক অর্থনীতি যখন নানামুখী বৈশ্বিক সংকটের সাথে খাপ খাইয়ে নেওয়ার লড়াই করছে, তখন অভ্যন্তরীণ সুশাসন নিশ্চিত করা সবচেয়ে বড় অগ্রাধিকার হওয়া উচিত। দুর্বল আর্থিক প্রতিষ্ঠানগুলোর পুনর্গঠন এবং ঋণখেলাপিদের বিরুদ্ধে শূন্য সহনশীলতা প্রদর্শন সময়ের দাবি।</p><p>সরকারের সাম্প্রতিক সংস্কারমূলক পদক্ষেপ ইতিবাচক, তবে তা যেন কেবল নীতিমালায় সীমাবদ্ধ না থাকে, মাঠপর্যায়ে কঠোরভাবে বাস্তবায়িত হতে হবে।</p>',
          hotspot: { top: 12, left: 6, width: 46, height: 82 }
        },
        {
          id: 'art-3-2',
          title: 'গণতান্ত্রিক ধারাবাহিকতা ও রাজনৈতিক দলগুলোর গঠনমূলক ডায়ালগের তাৎপর্য',
          kicker: 'বিশেষ কলাম',
          category: 'অভিমত',
          author: 'ড. আবুল ফজল রহমান, বিশিষ্ট রাষ্ট্রবিজ্ঞানী',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:১৫',
          read_time: '৪ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1450133064473-71024230f91b?auto=format&fit=crop&w=800&q=80',
          caption: 'রাজনৈতিক সহনশীলতা ও জাতীয় ঐকমত্য গড়ে তোলার আহ্বান।',
          summary: 'রাজনৈতিক পরিমণ্ডলে সহনশীল সংস্কৃতি ও জাতীয় সংকটে দল-মত নির্বিশেষে আলোচনার টেবিলে বসার সংস্কৃতি জাতীয় সমৃদ্ধিকে এগিয়ে নেয়।',
          content: '<p>একটি গতিশীল ও সমৃদ্ধ সমাজের জন্য বহুদলীয় গণতন্ত্রের সুস্থ চর্চা অপরিহার্য। রাজনৈতিক বিতণ্ডা যখন জাতীয় স্বার্থে প্রতিবন্ধকতা সৃষ্টি করে, তখন সাধারণ মানুষ হতাশাগ্রস্ত হয়। মতপার্থক্য থাকবেই, কিন্তু তা যেন জনকল্যাণের পথে অন্তরায় না হয়।</p>',
          hotspot: { top: 12, left: 54, width: 40, height: 40 }
        },
        {
          id: 'art-3-3',
          title: 'উপ-সম্পাদকীয়: সবুজ নগরায়ণ ও বাসযোগ্য ঢাকা বিনির্মাণে নাগরিক দায়িত্ববোধ',
          kicker: 'নগর ভাবনা',
          category: 'উপ-সম্পাদকীয়',
          author: 'স্থপতি ফারহানা জামান',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৪৫',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1477959858617-67f30bc75b82?auto=format&fit=crop&w=800&q=80',
          caption: 'রাজধানীতে সবুজ পার্ক ও খাল পুনরুদ্ধারের বহুমুখী উদ্যোগ।',
          summary: 'বর্জ্য ব্যবস্থাপনা, ছাদবাগান এবং জলাশয় রক্ষার মাধ্যমে ঢাকাকে আধুনিক বাসযোগ্য শহরে রূপান্তর সম্ভব।',
          content: '<p>উচ্চ অট্টালিকার জঞ্জাল পেরিয়ে নগরবাসীকে উন্মুক্ত নিঃশ্বাস নেওয়ার সুযোগ তৈরি করতে হবে। খালগুলোর অবৈধ দখল উচ্ছেদ এবং প্রতিটি ওয়ার্ডে খেলার মাঠ নিশ্চিত করা এখন সময়ের দাবি।</p>',
          hotspot: { top: 54, left: 54, width: 40, height: 40 }
        }
      ]
    },
    {
      page_no: 4,
      title: 'বাণিজ্য ও অর্থনীতি',
      category: 'বাণিজ্য ও অর্থনীতি',
      subtitle: 'রপ্তানি প্রবৃদ্ধি, পুঁজিবাজার, ব্যাংকিং খাত ও করপোরেট সংবাদ',
      edition: 'ঢাকা সংস্করণ',
      edition_date: 'শনিবার, ১০ অক্টোবর ২০২৬',
      pdf_url: '/storage/epaper/pdf/page_4.pdf',
      image_url: 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=1400&q=80',
      clippings: [
        {
          id: 'art-4-1',
          title: 'পোশাক রপ্তানিতে নতুন মাইলফলক: বিকল্প বাজারে রপ্তানি প্রবৃদ্ধি ১২ শতাংশ ছাড়াল',
          kicker: 'রপ্তানি বাণিজ্য',
          category: 'বাণিজ্যিক খবর',
          author: 'বাণিজ্য প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:০০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=800&q=80',
          caption: 'পরিবেশবান্ধব সবুজ কারখানায় বিশ্বমানের তৈরি পোশাক উৎপাদন কার্যক্রম।',
          summary: 'জাপান, অস্ট্রেলিয়া ও দক্ষিণ কোরিয়ার মতো অপ্রচলিত বাজারে বাংলাদেশের পোশাক রপ্তানি অভূতপূর্ব ইতিবাচক সাড়া পেয়েছে।',
          content: '<p><strong>বাণিজ্য ডেস্ক:</strong> দেশের তৈরি পোশাক খাত প্রচলিত ইউরোপ-আমেরিকা বাজারের পাশাপাশি নতুন গন্তব্যে শক্তিশালী অবস্থান গড়ে তুলেছে। চলতি অর্থবছরের প্রথম প্রান্তিকে অপ্রচলিত বাজারে রপ্তানি আয় ১২.৪ শতাংশ বৃদ্ধি পেয়েছে।</p><p>বিজিএমইএ সভাপতি জানিয়েছেন, টেকসই পরিবেশবান্ধব লিড সার্টিফাইড কারখানার সংখ্যা বাড়ায় বৈক্সিক ক্রেতারা বাংলাদেশকে শীর্ষ অগ্রাধিকার দিচ্ছেন।</p>',
          hotspot: { top: 12, left: 6, width: 58, height: 44 }
        },
        {
          id: 'art-4-2',
          title: 'শেয়ারবাজারে সূচকের বড় উত্থান: দৈনিক লেনদেন হাজার কোটি টাকা স্পর্শ করল',
          kicker: 'পুঁজিবাজার',
          category: 'অর্থনীতি',
          author: 'পুঁজিবাজার বিশ্লেষক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:২০',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=800&q=80',
          caption: 'ডিএসইতে সাধারণ বিনিয়োগকারীদের উৎসাহব্যঞ্জক উপস্থিতি।',
          summary: 'ভালো মৌলভিত্তিসম্পন্ন কোম্পানির শেয়ারের প্রতি আগ্রহ বৃদ্ধি পাওয়ায় টানা তিন কার্যদিবস ঊর্ধ্বমুখী শেয়ারবাজার।',
          content: '<p>ঢাকা স্টক এক্সচেঞ্জে (ডিএসই) সার্বিক সূচক ৬২ পয়েন্ট বৃদ্ধি পেয়েছে। প্রাতিষ্ঠানিক বিনিয়োগকারীদের সক্রিয় ভূমিকা এবং নিয়ন্ত্রক সংস্থার সুনির্দিষ্ট নীতিমালার ফলে আস্থার সংকট কেটে যাচ্ছে বলে বাজার অংশীজনরা জানান।</p>',
          hotspot: { top: 12, left: 66, width: 28, height: 44 }
        },
        {
          id: 'art-4-3',
          title: 'ক্ষুদ্র ও মাঝারি কুটির শিল্পে জামানতবিহীন সহজ ঋণে গতি পাচ্ছে গ্রামীণ অর্থনীতি',
          kicker: 'এসএমই খাত',
          category: 'ব্যাংকিং',
          author: 'আর্থিক প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৫০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1556742049-0a67c5574f73?auto=format&fit=crop&w=800&q=80',
          caption: 'নারী উদ্যোক্তাদের উৎপাদিত পণ্য জাতীয় ও আঞ্চলিক প্রদর্শনীতে প্রদর্শিত হচ্ছে।',
          summary: 'কেন্দ্রীয় ব্যাংকের বিশেষ পুনঃঅর্থায়ন তহবিলের সহায়তায় প্রত্যন্ত অঞ্চলের নারী ও তরুণ উদ্যোক্তারা স্বল্প সুদে ঋণ সহায়তা পাচ্ছেন।',
          content: '<p>গ্রামাঞ্চলের কুটির শিল্প ও কৃষিভিত্তিক প্রক্রিয়াকরণ শিল্পে বিপ্লব ঘটাতে বাণিজ্যিক ব্যাংকগুলো তৃণমূল পর্যায়ে সহজ শর্তে ঋণ বিতরণ শুরু করেছে। এতে কর্মসংস্থান সম্প্রসারিত হচ্ছে।</p>',
          hotspot: { top: 58, left: 6, width: 88, height: 36 }
        }
      ]
    },
    {
      page_no: 5,
      title: 'বিশ্ব সংবাদ',
      category: 'বিশ্ব সংবাদ',
      subtitle: 'আন্তর্জাতিক কূটনীতি, ভূ-রাজনীতি, বিজ্ঞান গবেষণা ও সংঘাত পর্যবেক্ষণ',
      edition: 'ঢাকা সংস্করণ',
      edition_date: 'শনিবার, ১০ অক্টোবর ২০২৬',
      pdf_url: '/storage/epaper/pdf/page_5.pdf',
      image_url: 'https://images.unsplash.com/photo-1569163139599-0f4517e36f51?auto=format&fit=crop&w=1400&q=80',
      clippings: [
        {
          id: 'art-5-1',
          title: 'বৈশ্বিক জলবায়ু শীর্ষ সম্মেলনে উন্নয়নশীল দেশগুলোর ক্ষতিপূরণে ঐতিহাসিক সম্মতি',
          kicker: 'আন্তর্জাতিক পরিবেশ',
          category: 'বিশ্ব সংবাদ',
          author: 'আন্তর্জাতিক ডেস্ক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:০০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1569163139599-0f4517e36f51?auto=format&fit=crop&w=800&q=80',
          caption: 'জাতিসংঘের তত্ত্বাবধানে জলবায়ু সম্মেলনে বিশ্বনেতাদের যৌথ ঘোষণা।',
          summary: 'কার্বন নির্গমন রোধ ও ক্ষতিগ্রস্ত দেশগুলোর পুনর্বাসনে ১০০ বিলিয়ন ডলারের আন্তর্জাতিক তহবিল গঠনে ঐকমত্যে পৌঁছেছে উন্নত বিশ্ব।',
          content: '<p><strong>জেনেভা:</strong> দীর্ঘদিনের কূটনৈতিক দরকষাকষির পর ঐতিহাসিক লস অ্যান্ড ড্যামেজ তহবিল পুরোপুরি কার্যকর করার কাঠামো গৃহীত হয়েছে। বাংলাদেশসহ জলবায়ু ঝুঁকিপূর্ণ দেশগুলো এই সিদ্ধান্তকে বড় বিজয় হিসেবে দেখছে।</p>',
          hotspot: { top: 12, left: 6, width: 54, height: 42 }
        },
        {
          id: 'art-5-2',
          title: 'মধ্যপ্রাচ্যে যুদ্ধবিরতি কার্যকরে কূটনৈতিক তৎপরতা তুঙ্গে: শান্তির আশাবাদ',
          kicker: 'ভূ-রাজনীতি',
          category: 'কূটনীতি',
          author: 'মধ্যপ্রাচ্য প্রতিনিধি',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:২৫',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?auto=format&fit=crop&w=800&q=80',
          caption: 'ত্রাণবাহী গাড়িবহর সীমান্ত দিয়ে দুর্গত এলাকায় প্রবেশ করছে।',
          summary: 'মানবিক সহায়তা নির্বিঘ্ন করতে স্থায়ী শান্তি প্রতিষ্ঠার লক্ষ্যে দোহা ও কায়রোতে আন্তর্জাতিক মধ্যস্থতাকারীদের বৈঠক অব্যাহত।',
          content: '<p>আন্তর্জাতিক সম্প্রদায়ের ক্রমাগত চাপের মুখে উভয় পক্ষই বন্দী বিনিময় ও দীর্ঘমেয়াদি মানবিক করিডোর উন্মুক্ত রাখতে সম্মত হয়েছে বলে সংশ্লিষ্ট কূটনৈতিক সূত্র নিশ্চিত করেছে।</p>',
          hotspot: { top: 12, left: 62, width: 32, height: 42 }
        },
        {
          id: 'art-5-3',
          title: 'মহাকাশ গবেষণায় যুগান্তকারী আবিষ্কার: সৌরজগতের বাইরের গ্রহে বায়ুমণ্ডলের উপাদান শনাক্ত',
          kicker: 'বিজ্ঞান ও মহাকাশ',
          category: 'বিজ্ঞান',
          author: 'বিজ্ঞান ডেস্ক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৪৫',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=800&q=80',
          caption: 'জেমস ওয়েব স্পেস টেলিস্কোপের মাধ্যমে তোলা দূরবর্তী এক্সোপ্ল্যানেটের নতুন চিত্র।',
          summary: 'নাসার গবেষকরা জানিয়েছেন, নতুন আবিষ্কৃত গ্রহে কার্বন ও মিথেনের উপস্থিতির কারণে সেখানে প্রাণের উপযোগী পরিবেশ থাকার সম্ভাবনা উজ্জ্বল।',
          content: '<p>বিজ্ঞানীরা দূরবর্তী সৌরজগতের একটি পাথুরে গ্রহে পানির বাষ্প এবং জৈব যৌগের আভাস পেয়েছেন, যা জ্যোতির্বিজ্ঞানের ইতিহাসে নতুন সম্ভাবনার দ্বার খুলে দিয়েছে।</p>',
          hotspot: { top: 56, left: 6, width: 88, height: 38 }
        }
      ]
    },
    {
      page_no: 6,
      title: 'খেলাধুলা ও বিনোদন',
      category: 'খেলাধুলা ও বিনোদন',
      subtitle: 'ক্রিকেট, ফুটবল, জাতীয় লীগ ও দেশ-বিদেশের সংস্কৃতি ও সিনেমা',
      edition: 'ঢাকা সংস্করণ',
      edition_date: 'শনিবার, ১০ অক্টোবর ২০২৬',
      pdf_url: '/storage/epaper/pdf/page_6.pdf',
      image_url: 'https://images.unsplash.com/photo-1531415074868-036b1c57e329?auto=format&fit=crop&w=1400&q=80',
      clippings: [
        {
          id: 'art-6-1',
          title: 'টাইগারদের রোমাঞ্চকর জয়: শেষ ওভারের রুদ্ধশ্বাস নাটকীয়তায় সিরিজ নিশ্চিত',
          kicker: 'মিরপুর টেস্ট ও ওয়ানডে',
          category: 'ক্রিকেট',
          author: 'ক্রীড়া প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:০০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1531415074868-036b1c57e329?auto=format&fit=crop&w=800&q=80',
          caption: 'ম্যাচ জেতানো উইকেটের পর টাইগারদের বুনো উল্লাস।',
          summary: 'বোলারদের দুর্দান্ত নৈপুণ্য ও অধিনায়কের দায়িত্বশীল অর্ধশতকে শক্তিশালী প্রতিপক্ষকে ৩ উইকেটে হারিয়ে ট্রফি জয় করল বাংলাদেশ।',
          content: '<p><strong>মিরপুর শেরেবাংলা স্টেডিয়াম:</strong> গ্যালারিভরা দর্শকের গর্জন আর উত্তেজনার পারদ চড়িয়ে শেষ ওভারে মাত্র ৪ রান দিয়ে ২ উইকেট তুলে নিয়ে নাটকীয় জয় ছিনিয়ে নিয়েছে লাল-সবুজের দল। ম্যাচসেরা নির্বাচিত হয়েছেন দলের তরুণ অলরাউন্ডার।</p><p>ম্যাচ শেষে সংবাদ সম্মেলনে অধিনায়ক বলেন, "দলের প্রত্যেকের আত্মবিশ্বাসই এই অর্জনের মূল চাবিকাঠি ছিল।"</p>',
          hotspot: { top: 12, left: 6, width: 56, height: 44 }
        },
        {
          id: 'art-6-2',
          title: 'চ্যাম্পিয়ন্স লিগে রুদ্ধশ্বাস কোয়ার্টার ফাইনাল: অতিরিক্ত সময়ে নাটকীয় গোল',
          kicker: 'ইউরোপিয়ান ফুটবল',
          category: 'ফুটবল',
          author: 'ক্রীড়া ডেস্ক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:২০',
          read_time: '২ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80',
          caption: 'অতিরিক্ত সময়ের ১২০তম মিনিটে গোল উদযাপনে মাতোয়ারা খেলোয়াড়রা।',
          summary: 'দুর্দান্ত প্রত্যাবর্তনে দুই গোলের ব্যবধান ঘুচিয়ে সেমিফাইনালে টিকিট নিশ্চিত করল স্প্যানিশ জায়ান্টরা।',
          content: '<p>ইউরোপীয় ফুটবলের সেরা লড়াইয়ে গতকাল রাতে অনুষ্ঠিত ম্যাচে অতিরিক্ত সময়ের শেষ মুহূর্তে দুর্দান্ত ভলিতে জয় নিশ্চিত হয়, যা টুর্নামেন্টের অন্যতম সেরা গোল হিসেবে প্রশংসিত হচ্ছে।</p>',
          hotspot: { top: 12, left: 64, width: 30, height: 44 }
        },
        {
          id: 'art-6-3',
          title: 'আন্তর্জাতিক চলচ্চিত্র উৎসবে বাংলা সিনেমার জয়জয়কার: সেরা পরিচালকের পুরস্কার',
          kicker: 'রূপালী পর্দা',
          category: 'বিনোদন ও সংস্কৃতি',
          author: 'বিনোদন প্রতিবেদক',
          published_at: '১০ অক্টোবর ২০২৬, ভোর ৫:৫০',
          read_time: '৩ মিনিট পাঠ',
          image: 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80',
          caption: 'বিখ্যাত চলচ্চিত্র উৎসবে রেড কার্পেটে নির্মাতা ও কলাকুশলীদের সম্মাননা গ্রহণ।',
          summary: 'বাংলার আবহমান লোকসংস্কৃতি ও নদীমাতৃক জীবন নিয়ে নির্মিত স্বাধীন পূর্ণদৈর্ঘ্য চলচ্চিত্র বিশ্বমঞ্চে ভূয়সী প্রশংসা অর্জন করেছে।',
          content: '<p>চলচ্চিত্র সমালোচকদের মতে, সিনেমার ক্যামেরার কাজ, আবহসংগীত ও হৃদয়স্পর্শী গল্প বিশ্বদর্শকদের মুগ্ধ করেছে। আগামী মাসে সিনেমাটি দেশের প্রেক্ষাগৃহে মুক্তি পাবে।</p>',
          hotspot: { top: 58, left: 6, width: 88, height: 36 }
        }
      ]
    }
  ]
}

// Fetch Edition Data from backend API with fallback
const { data: epaperResponse } = await useFetch(() => `${config.public.apiBase}/m/EPaperViewer?date=${selectedDate.value}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: `page-epaper-viewer-${selectedDate.value}`,
  default: () => defaultEpaperData,
})

const epaperData = computed(() => {
  return epaperResponse.value?.success ? epaperResponse.value : defaultEpaperData
})

const currentPage = computed(() => {
  return epaperData.value?.pages?.[activePage.value] || epaperData.value?.pages?.[0]
})

// Available date options for selector
const availableDates = computed(() => {
  const dates = epaperData.value?.available_dates || [
    new Date().toISOString().split('T')[0]
  ]
  
  return dates.map((d, idx) => {
    let label = d
    if (idx === 0) label = `আজকের সংস্করণ (${d})`
    else if (idx === 1) label = `গতকালের সংস্করণ (${d})`
    else label = `${d} সংস্করণ`
    return { value: d, label }
  })
})

// Current Page Share URL
const currentShareUrl = computed(() => {
  if (import.meta.client) {
    return `${window.location.origin}/epaper?page=${activePage.value + 1}&date=${selectedDate.value}`
  }
  return `/epaper?page=${activePage.value + 1}&date=${selectedDate.value}`
})

// Page Navigation
const nextPage = () => {
  if (activePage.value < (epaperData.value?.pages?.length || 1) - 1) {
    activePage.value++
    stopAudio()
  }
}

const prevPage = () => {
  if (activePage.value > 0) {
    activePage.value--
    stopAudio()
  }
}

// Mobile Bottom Sheet Drawer Handlers
const openBottomSheet = () => {
  showBottomSheet.value = true
}

const closeBottomSheet = () => {
  showBottomSheet.value = false
}

const selectPageFromDrawer = (idx) => {
  activePage.value = idx
  closeBottomSheet()
  stopAudio()
}

// Toast Feedback
const showToastMessage = (msg) => {
  toastMessage.value = msg
  if (toastTimer) clearTimeout(toastTimer)
  toastTimer = setTimeout(() => {
    toastMessage.value = ''
  }, 2500)
}

// Touch Gestures for Mobile Swipe (Left -> Next, Right -> Prev)
let touchStartX = 0
let touchStartY = 0

const handleTouchStart = (e) => {
  if (e.touches && e.touches.length === 1) {
    touchStartX = e.touches[0].clientX
    touchStartY = e.touches[0].clientY
  }
}

const handleTouchEnd = (e) => {
  if (e.changedTouches && e.changedTouches.length === 1) {
    const touchEndX = e.changedTouches[0].clientX
    const touchEndY = e.changedTouches[0].clientY
    const diffX = touchEndX - touchStartX
    const diffY = touchEndY - touchStartY

    // Detect horizontal swipe if delta X > 60 and horizontal dominance
    if (Math.abs(diffX) > 60 && Math.abs(diffX) > Math.abs(diffY) * 1.5) {
      if (diffX < 0) {
        // Swipe Left -> Next Page
        if (activePage.value < (epaperData.value?.pages?.length || 1) - 1) {
          nextPage()
          showToastMessage(`পৃষ্ঠা ${activePage.value + 1}: ${currentPage.value?.title}`)
        }
      } else {
        // Swipe Right -> Prev Page
        if (activePage.value > 0) {
          prevPage()
          showToastMessage(`পৃষ্ঠা ${activePage.value + 1}: ${currentPage.value?.title}`)
        }
      }
    }
  }
}

// Zoom Controls
const zoomIn = () => {
  if (zoomLevel.value < 180) {
    zoomLevel.value = Math.min(180, zoomLevel.value + 15)
  }
}

const zoomOut = () => {
  if (zoomLevel.value > 60) {
    zoomLevel.value = Math.max(60, zoomLevel.value - 15)
  }
}

const resetZoom = () => {
  zoomLevel.value = 100
}

const toggleFullscreen = () => {
  isFullscreen.value = !isFullscreen.value
  if (import.meta.client && viewerContainer.value) {
    if (isFullscreen.value) {
      if (viewerContainer.value.requestFullscreen) {
        viewerContainer.value.requestFullscreen().catch(() => {})
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen().catch(() => {})
      }
    }
  }
}

// Clipping Modal Handler
const openClipping = (clip) => {
  stopAudio()
  selectedClipping.value = clip
  articleFontSize.value = 17
  copiedText.value = false
}

const openClippingWithAudio = (clip) => {
  openClipping(clip)
  setTimeout(() => {
    playAudio()
  }, 250)
}

const closeClipping = () => {
  stopAudio()
  selectedClipping.value = null
}

const increaseArticleFont = () => {
  if (articleFontSize.value < 24) articleFontSize.value += 2
}

const decreaseArticleFont = () => {
  if (articleFontSize.value > 14) articleFontSize.value -= 2
}

// Web Speech API Voice Reader (বাংলা অডিও রিডার)
const stripHtml = (html) => {
  if (!html) return ''
  return html.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}

const playAudio = () => {
  if (!import.meta.client) return
  if (!('speechSynthesis' in window)) {
    alert('আপনার ডিভাইসে বা ব্রাউজারে স্পিচ সিন্থেসিস অডিও রিডার সমর্থিত নয়।')
    return
  }

  if (isPaused.value) {
    window.speechSynthesis.resume()
    isPaused.value = false
    isSpeaking.value = true
    return
  }

  // Cancel prior speech instances
  window.speechSynthesis.cancel()

  if (!selectedClipping.value) return

  const rawText = `${selectedClipping.value.title}। ${selectedClipping.value.summary || ''}। ${stripHtml(selectedClipping.value.content)}`
  const utterance = new SpeechSynthesisUtterance(rawText)
  
  utterance.lang = 'bn-BD'
  utterance.rate = 0.95
  utterance.pitch = 1.0

  // Check for Bengali voice in system voices
  const voices = window.speechSynthesis.getVoices()
  const bnVoice = voices.find(v => v.lang === 'bn-BD' || v.lang === 'bn_BD' || v.lang.startsWith('bn'))
  if (bnVoice) {
    utterance.voice = bnVoice
  }

  utterance.onstart = () => {
    isSpeaking.value = true
    isPaused.value = false
  }

  utterance.onpause = () => {
    isPaused.value = true
  }

  utterance.onresume = () => {
    isPaused.value = false
  }

  utterance.onend = () => {
    isSpeaking.value = false
    isPaused.value = false
  }

  utterance.onerror = () => {
    isSpeaking.value = false
    isPaused.value = false
  }

  window.speechSynthesis.speak(utterance)
}

const pauseAudio = () => {
  if (import.meta.client && 'speechSynthesis' in window) {
    window.speechSynthesis.pause()
    isPaused.value = true
  }
}

const stopAudio = () => {
  if (import.meta.client && 'speechSynthesis' in window) {
    window.speechSynthesis.cancel()
    isSpeaking.value = false
    isPaused.value = false
  }
}

// Social Sharing & Web Share API
const nativeShareClipping = async (clip) => {
  if (!import.meta.client) return
  const title = clip?.title || 'দৈনিক বাংলা ই-পেপার'
  const text = `${title} — ${clip?.summary || ''}`
  const url = `${window.location.origin}/epaper?page=${activePage.value + 1}&article=${clip?.id}`

  if (navigator.share) {
    try {
      await navigator.share({
        title,
        text,
        url
      })
      return
    } catch (err) {
      if (err.name !== 'AbortError') {
        shareOnWhatsApp(clip)
      }
    }
  } else {
    shareOnWhatsApp(clip)
  }
}

const shareOnFacebook = (clip) => {
  if (import.meta.client) {
    const url = encodeURIComponent(window.location.href)
    const title = encodeURIComponent(clip?.title || 'দৈনিক বাংলা ই-পেপার')
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400')
  }
}

const shareOnWhatsApp = (clip) => {
  if (import.meta.client) {
    const text = encodeURIComponent(`*${clip?.title}*\n${clip?.summary}\nদৈনিক বাংলা ই-পেপার পড়ুন: ${window.location.href}`)
    window.open(`https://api.whatsapp.com/send?text=${text}`, '_blank')
  }
}

const copyArticleLink = async (clip) => {
  if (import.meta.client) {
    try {
      const link = `${window.location.origin}/epaper?page=${activePage.value + 1}&article=${clip?.id}`
      await navigator.clipboard.writeText(link)
      copiedText.value = true
      showToastMessage('খবরের লিঙ্ক কপি হয়েছে')
      setTimeout(() => {
        copiedText.value = false
      }, 3000)
    } catch {
      copiedText.value = true
    }
  }
}

const printArticle = () => {
  if (import.meta.client) {
    window.print()
  }
}

const openShareModal = () => {
  showShareModal.value = true
  copiedText.value = false
}

const shareEdition = async (platform) => {
  if (!import.meta.client) return
  const url = currentShareUrl.value
  const text = `দৈনিক বাংলা ই-পেপার — ${currentPage.value?.title} (${epaperData.value?.edition_date_formatted})`

  if (platform === 'native' && navigator.share) {
    try {
      await navigator.share({
        title: 'দৈনিক বাংলা ই-পেপার',
        text,
        url
      })
      showShareModal.value = false
      return
    } catch {
      // User cancelled
    }
  }

  const encodedUrl = encodeURIComponent(url)
  const encodedText = encodeURIComponent(text)

  if (platform === 'facebook') {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}&quote=${encodedText}`, '_blank', 'width=600,height=400')
  } else if (platform === 'whatsapp') {
    window.open(`https://api.whatsapp.com/send?text=${encodedText}%20${encodedUrl}`, '_blank')
  }
}

const copyShareUrl = async () => {
  if (import.meta.client) {
    try {
      await navigator.clipboard.writeText(currentShareUrl.value)
      copiedText.value = true
      showToastMessage('ই-পেপার সংস্করণ লিঙ্ক কপি হয়েছে')
      setTimeout(() => {
        copiedText.value = false
      }, 3000)
    } catch {
      copiedText.value = true
    }
  }
}

// Full Page PDF and JPG Image Download
const downloadPage = (format) => {
  if (!import.meta.client) return
  const pageNo = currentPage.value?.page_no || 1
  const filename = `dainik-bangla-epaper-page-${pageNo}-${selectedDate.value}.${format}`
  
  const fileUrl = format === 'pdf'
    ? (currentPage.value?.pdf_url || `/storage/epaper/pdf/page_${pageNo}.pdf`)
    : (currentPage.value?.image_url || 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=1600&q=80')

  const link = document.createElement('a')
  link.href = fileUrl
  link.download = filename
  link.target = '_blank'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  
  showToastMessage(`পৃষ্ঠা ${pageNo} (${format.toUpperCase()}) ডাউনলোড শুরু হয়েছে`)
}

// Cleanup on unmount
onBeforeUnmount(() => {
  stopAudio()
})

// Keyboard shortcuts for browsing
if (import.meta.client) {
  window.addEventListener('keydown', (e) => {
    if (selectedClipping.value && e.key === 'Escape') {
      closeClipping()
    } else if (!selectedClipping.value) {
      if (e.key === 'ArrowRight') nextPage()
      if (e.key === 'ArrowLeft') prevPage()
      if (e.key === '+' || e.key === '=') zoomIn()
      if (e.key === '-') zoomOut()
    }
  })
}

useHead({
  title: computed(() => `ই-পেপার | ${currentPage.value?.title || 'প্রথম পাতা'} — দৈনিক বাংলা`),
  meta: [
    { name: 'description', content: 'দৈনিক বাংলা ডিজিটাল ই-পেপার সংস্করণ। মুদ্রিত পত্রিকার মতো পেজ ফ্লিপ, জুম, বাংলা অডিও ভয়েস রিডার এবং সহজ পঠন ভিউ সুবিধা।' }
  ]
})
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.98);
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
  transition: opacity 0.25s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.drawer-slide-enter-from,
.drawer-slide-leave-to {
  opacity: 0;
  transform: translateY(100%);
}

.toast-fade-enter-active,
.toast-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.toast-fade-enter-from,
.toast-fade-leave-to {
  opacity: 0;
  transform: translate(-50%, 10px);
}
</style>
