<template>
  <AppLayout>
    <div class="p-4 sm:p-6 space-y-6 max-w-7xl mx-auto">
      <!-- Flash Messages -->
      <div
        v-if="$page.props.flash?.success"
        class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center justify-between shadow-sm animate-fadeIn"
      >
        <div class="flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">✓</span>
          <span class="font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <div
        v-if="$page.props.flash?.error"
        class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm flex items-center justify-between shadow-sm animate-fadeIn"
      >
        <div class="flex items-center gap-2">
          <span class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs">✕</span>
          <span class="font-medium">{{ $page.props.flash.error }}</span>
        </div>
      </div>

      <!-- Top Header & Navigation -->
      <div class="bg-gradient-to-r from-gray-900 via-slate-900 to-red-950 text-white p-6 rounded-3xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 border border-gray-800">
        <div>
          <div class="flex items-center gap-2.5 mb-2">
            <span class="bg-red-600/90 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
              সোশ্যাল অটোমেশন
            </span>
            <span v-if="facebookSettings.is_connected" class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs px-2.5 py-0.5 rounded-full flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
              {{ facebookSettings.page_name }} (সংযুক্ত)
            </span>
            <span v-else class="bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs px-2.5 py-0.5 rounded-full flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
              সিমুলেশন মোড (ডেমো)
            </span>
          </div>

          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">ফটাকার্ড স্টুডিও ও ডুয়েল পাবলিশার</h1>
          <p class="text-gray-300 text-sm mt-1 max-w-2xl leading-relaxed">
            এক ক্লিকেই ওয়েবসাইটে সংবাদ প্রকাশ এবং স্বয়ংক্রিয়ভাবে ফেসবুকে হাই-কোয়ালিটি ফটাকার্ড পোস্ট ও ১ম কমেন্টে নিউজ লিংক পাঠান!
          </p>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="flex flex-wrap items-center gap-3">
          <button
            @click="activeTab = 'studio'"
            :class="[
              'px-4 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-sm',
              activeTab === 'studio' ? 'bg-red-600 text-white shadow-red-600/30 shadow-lg' : 'bg-gray-800 hover:bg-gray-700 text-gray-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            নতুন ফটাকার্ড ও প্রকাশনা
          </button>

          <button
            @click="activeTab = 'history'"
            :class="[
              'px-4 py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center gap-2 shadow-sm',
              activeTab === 'history' ? 'bg-red-600 text-white shadow-red-600/30 shadow-lg' : 'bg-gray-800 hover:bg-gray-700 text-gray-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            প্রকাশনার ইতিহাস ({{ stats.total_photocards }})
          </button>

          <button
            @click="showSettingsModal = true"
            class="px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-gray-800 hover:bg-gray-700 text-gray-200 border border-gray-700 transition-colors flex items-center gap-1.5"
            title="ফেসবুক পেজ সেটিংস ও সংযোগ"
          >
            <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 24 24">
              <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            পেজ সেটিংস
          </button>
        </div>
      </div>

      <!-- Quick Metrics Counters -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500">মোট ফটাকার্ড তৈরি</p>
            <p class="text-2xl font-black text-gray-800 mt-0.5">{{ stats.total_photocards }}</p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold">
            📷
          </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500">ফেসবুকে পাবলিশড</p>
            <p class="text-2xl font-black text-blue-600 mt-0.5">{{ stats.fb_published }}</p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
            f
          </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500">আজকের প্রকাশনা</p>
            <p class="text-2xl font-black text-emerald-600 mt-0.5">{{ stats.today_count }}</p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
            ⚡
          </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-medium text-gray-500">১ম কমেন্ট স্ট্র্যাটেজি</p>
            <p class="text-sm font-bold text-gray-800 mt-1 flex items-center gap-1">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span> সক্রিয় (জিরো পেনাল্টি)
            </p>
          </div>
          <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
            💬
          </div>
        </div>
      </div>

      <!-- MAIN TAB 1: STUDIO & DUAL PUBLISHER -->
      <div v-show="activeTab === 'studio'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- LEFT COLUMN: Canvas Live Studio (5 cols) -->
        <div class="lg:col-span-6 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-5 sticky top-4">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="p-1.5 bg-red-100 text-red-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </span>
              <h2 class="font-bold text-gray-800 text-base">লাইভ ফটাকার্ড স্টুডিও</h2>

              <button
                type="button"
                @click="isPreviousCardsModalOpen = true"
                class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 active:scale-95 rounded-xl shadow-xs transition-all cursor-pointer"
                title="আগের কার্ডগুলো দেখুন ও যেকোনো কার্ড পছন্দ করুন"
              >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                <span>+ আগের কার্ড / টেমপ্লেট</span>
              </button>
            </div>

            <!-- Download Button -->
            <button
              @click="downloadPhotocard"
              type="button"
              class="px-3 py-1.5 text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors flex items-center gap-1.5 shadow-sm"
              title="ফটাকার্ডটি কম্পিউটারে ডাউনলোড করুন"
            >
              <svg class="w-3.5 h-3.5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
              </svg>
              ডাউনলোড PNG
            </button>
          </div>

          <!-- The Live Canvas Preview Area -->
          <div class="relative group rounded-2xl overflow-hidden shadow-2xl border border-gray-200 bg-gray-900 aspect-square flex items-center justify-center">
            <canvas
              ref="photocardCanvas"
              width="1200"
              height="1200"
              class="w-full h-full object-cover select-none"
            ></canvas>

            <!-- Loading overlay when rendering -->
            <div v-if="isCanvasRendering" class="absolute inset-0 bg-black/40 backdrop-blur-xs flex items-center justify-center text-white text-xs font-semibold">
              <span class="animate-spin mr-2">⏳</span> রেন্ডার হচ্ছে...
            </div>
          </div>

          <!-- Preset Backgrounds Picker -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-gray-700">ব্যাকগ্রাউন্ড ছবি নির্বাচন করুন:</label>
                <button
                  type="button"
                  @click="showMediaPickerModal = true"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-bold text-white bg-red-600 hover:bg-red-700 active:scale-95 rounded-lg shadow-xs transition-all"
                  title="পূর্বের ছবি ও মিডিয়া গ্যালারি থেকে ব্যাকগ্রাউন্ড বাছুন"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                  </svg>
                  + গ্যালারি / পূর্বের ছবি
                </button>
              </div>
              <label class="text-xs text-red-600 hover:text-red-700 font-semibold cursor-pointer flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                নিজের ছবি আপলোড
                <input type="file" accept="image/*" class="hidden" @change="handleCustomImageUpload" />
              </label>
            </div>

            <!-- Thumbnail Presets with Quick + Gallery Tile -->
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
              <button
                type="button"
                @click="showMediaPickerModal = true"
                class="flex flex-col items-center justify-center border-2 border-dashed border-red-300 hover:border-red-500 bg-red-50/50 hover:bg-red-100/50 rounded-xl aspect-video transition-all text-red-600 group"
                title="মিডিয়া লাইব্রেরি থেকে পূর্বে আপলোডকৃত ছবি বাছুন"
              >
                <span class="text-lg font-bold group-hover:scale-110 transition-transform">➕</span>
                <span class="text-[10px] font-bold mt-0.5">সব ছবি</span>
              </button>

              <button
                v-for="(preset, idx) in backgroundPresets"
                :key="idx"
                type="button"
                @click="selectPreset(preset)"
                :class="[
                  'relative rounded-xl overflow-hidden border-2 aspect-video transition-all shadow-xs group',
                  selectedPresetUrl === preset.url ? 'border-red-600 ring-2 ring-red-500/30 scale-105' : 'border-gray-200 hover:border-gray-400'
                ]"
              >
                <img :src="preset.url" class="w-full h-full object-cover group-hover:scale-110 transition-transform" />
                <span class="absolute inset-x-0 bottom-0 bg-black/70 text-[9px] text-white py-0.5 truncate px-1 text-center">
                  {{ preset.name }}
                </span>
              </button>
            </div>

            <!-- Recent Selected / Uploaded Images Row -->
            <div v-if="recentUploads.length > 0" class="pt-2 border-t border-gray-100">
              <div class="flex items-center justify-between mb-1.5">
                <span class="text-[11px] font-semibold text-gray-500 flex items-center gap-1">
                  <span>🕒</span> সম্প্রতি ব্যবহৃত ছবি:
                </span>
                <button
                  type="button"
                  @click="clearRecentUploads"
                  class="text-[10px] text-gray-400 hover:text-red-500 transition-colors"
                >
                  ক্লিয়ার
                </button>
              </div>
              <div class="flex items-center gap-2 overflow-x-auto pb-1">
                <button
                  v-for="(imgUrl, rIdx) in recentUploads"
                  :key="rIdx"
                  type="button"
                  @click="selectPreset({ url: imgUrl })"
                  :class="[
                    'relative w-14 h-10 shrink-0 rounded-lg overflow-hidden border-2 transition-all shadow-2xs group',
                    selectedPresetUrl === imgUrl ? 'border-red-600 ring-2 ring-red-500/30 scale-105' : 'border-gray-200 hover:border-gray-400'
                  ]"
                  title="এই ছবিটি ব্যবহার করুন"
                >
                  <img :src="imgUrl" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                </button>
              </div>
            </div>
          </div>

          <!-- Canvas Controls: Gradients, Font Size, Badges -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
            <!-- Gradient Overlay Style -->
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1.5">ওভারলে গ্রেডিয়েন্ট প্রেসেট</label>
              <select
                v-model="studioConfig.gradientPreset"
                @change="renderCanvas"
                class="w-full text-xs font-medium border border-gray-200 rounded-xl p-2.5 bg-gray-50 focus:bg-white focus:border-red-500 focus:ring-1 focus:ring-red-500"
              >
                <option value="cinematic_dark">ডার্ক সিনেমাটিক (কালো শেড)</option>
                <option value="breaking_red">ব্রেকিং রেড অ্যাকসেন্ট</option>
                <option value="royal_navy">রয়্যাল নেভি ব্লু</option>
                <option value="emerald_green">এমেরাল্ড গ্রিন</option>
                <option value="sunset_amber">গোল্ডেন সানসেট</option>
                <option value="purple_rose">পার্পল ও রোজ আভা</option>
              </select>
            </div>

            <!-- Headline Font Size Slider -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-bold text-gray-700">ফন্ট সাইজ</label>
                <span class="text-xs font-mono font-bold text-red-600">{{ studioConfig.fontSize }}px</span>
              </div>
              <input
                type="range"
                min="38"
                max="62"
                step="2"
                v-model="studioConfig.fontSize"
                @input="renderCanvas"
                class="w-full accent-red-600 cursor-pointer"
              />
            </div>

            <!-- Headline Text Color Accent & Custom Color Picker -->
            <div class="sm:col-span-2 bg-gray-50/70 p-3 rounded-2xl border border-gray-200/60">
              <div class="flex items-center justify-between mb-2">
                <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                  <span>🎨</span> শিরোনামের টেক্সট কালার
                </label>
                <div class="flex items-center gap-2">
                  <input
                    type="color"
                    v-model="studioConfig.textColor"
                    @input="renderCanvas"
                    class="w-8 h-8 rounded-lg cursor-pointer border border-gray-300 p-0.5 bg-white shadow-2xs"
                    title="কাস্টম কালার নির্বাচন করুন"
                  />
                  <span class="text-[11px] font-mono font-bold text-gray-600 uppercase">{{ studioConfig.textColor }}</span>
                </div>
              </div>

              <!-- Quick Palette Chips -->
              <div class="flex flex-wrap items-center gap-1.5">
                <button
                  type="button"
                  @click="studioConfig.textColor = '#FFFFFF'; renderCanvas()"
                  :class="['px-2.5 py-1 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5', studioConfig.textColor.toUpperCase() === '#FFFFFF' ? 'bg-gray-900 text-white border-gray-900 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100']"
                >
                  <span class="w-3 h-3 rounded-full bg-white border border-gray-300"></span>
                  ⚪ সাদা
                </button>
                <button
                  type="button"
                  @click="studioConfig.textColor = '#FDE047'; renderCanvas()"
                  :class="['px-2.5 py-1 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5', studioConfig.textColor.toUpperCase() === '#FDE047' ? 'bg-amber-100 text-amber-900 border-amber-400 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-amber-50']"
                >
                  <span class="w-3 h-3 rounded-full bg-[#FDE047] border border-amber-400"></span>
                  🟡 উজ্জ্বল হলুদ
                </button>
                <button
                  type="button"
                  @click="studioConfig.textColor = '#F59E0B'; renderCanvas()"
                  :class="['px-2.5 py-1 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5', studioConfig.textColor.toUpperCase() === '#F59E0B' ? 'bg-amber-100 text-amber-900 border-amber-500 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-amber-50']"
                >
                  <span class="w-3 h-3 rounded-full bg-[#F59E0B]"></span>
                  🟠 সোনালী
                </button>
                <button
                  type="button"
                  @click="studioConfig.textColor = '#EF4444'; renderCanvas()"
                  :class="['px-2.5 py-1 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5', studioConfig.textColor.toUpperCase() === '#EF4444' ? 'bg-red-100 text-red-900 border-red-400 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-red-50']"
                >
                  <span class="w-3 h-3 rounded-full bg-[#EF4444]"></span>
                  🔴 লাল
                </button>
                <button
                  type="button"
                  @click="studioConfig.textColor = '#10B981'; renderCanvas()"
                  :class="['px-2.5 py-1 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5', studioConfig.textColor.toUpperCase() === '#10B981' ? 'bg-emerald-100 text-emerald-900 border-emerald-400 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-emerald-50']"
                >
                  <span class="w-3 h-3 rounded-full bg-[#10B981]"></span>
                  🟢 সবুজ
                </button>
                <button
                  type="button"
                  @click="studioConfig.textColor = '#06B6D4'; renderCanvas()"
                  :class="['px-2.5 py-1 rounded-lg text-xs font-bold border transition-all flex items-center gap-1.5', studioConfig.textColor.toUpperCase() === '#06B6D4' ? 'bg-cyan-100 text-cyan-900 border-cyan-400 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-cyan-50']"
                >
                  <span class="w-3 h-3 rounded-full bg-[#06B6D4]"></span>
                  🔵 সায়ান
                </button>
              </div>
            </div>

            <!-- Branding, Date & Watermark Customization Panel -->
            <div class="sm:col-span-2 bg-gradient-to-br from-slate-50 via-gray-50 to-white rounded-2xl p-4 border border-gray-200/80 shadow-xs space-y-3">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-gray-200/70 pb-2.5">
                <div class="flex items-center gap-2">
                  <span class="text-sm">⚙️</span>
                  <h3 class="text-xs font-extrabold text-gray-800">ব্র্যান্ডিং, ওয়াটারমার্ক ও তারিখ কাস্টমাইজেশন</h3>
                </div>
                <!-- Sub-tabs navigation -->
                <div class="flex items-center gap-1 bg-gray-200/70 p-1 rounded-xl">
                  <button
                    type="button"
                    @click="activeBrandingTab = 'date'"
                    :class="['px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all flex items-center gap-1', activeBrandingTab === 'date' ? 'bg-white text-gray-800 shadow-2xs' : 'text-gray-600 hover:text-gray-900']"
                  >
                    <span>📅</span> তারিখ
                    <span v-if="studioConfig.showDate" class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  </button>
                  <button
                    type="button"
                    @click="activeBrandingTab = 'watermark'"
                    :class="['px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all flex items-center gap-1', activeBrandingTab === 'watermark' ? 'bg-white text-gray-800 shadow-2xs' : 'text-gray-600 hover:text-gray-900']"
                  >
                    <span>🌐</span> পোর্টাল লিংক
                    <span v-if="studioConfig.showWatermark" class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  </button>
                  <button
                    type="button"
                    @click="activeBrandingTab = 'branding'"
                    :class="['px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all flex items-center gap-1', activeBrandingTab === 'branding' ? 'bg-white text-gray-800 shadow-2xs' : 'text-gray-600 hover:text-gray-900']"
                  >
                    <span>🏛️</span> হেডার ব্র্যান্ডিং
                    <span v-if="studioConfig.showHeaderBranding" class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  </button>
                </div>
              </div>

              <!-- Tab 1: Date Configuration -->
              <div v-show="activeBrandingTab === 'date'" class="space-y-3 pt-1">
                <div class="flex items-center justify-between">
                  <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                    <input
                      type="checkbox"
                      v-model="studioConfig.showDate"
                      @change="renderCanvas"
                      class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer"
                    />
                    <span>📅 তারিখ প্রদর্শন করুন</span>
                  </label>
                  <span class="text-[11px] text-gray-400">ফটাকার্ডের নিচের বাম অংশে প্রদর্শিত হবে</span>
                </div>

                <div v-if="studioConfig.showDate" class="space-y-3 bg-white p-3 rounded-xl border border-gray-100 shadow-2xs">
                  <div>
                    <div class="flex items-center justify-between mb-1">
                      <label class="text-[11px] font-bold text-gray-700">ম্যানুয়াল তারিখ টেক্সট (ঐচ্ছিক):</label>
                      <button
                        v-if="studioConfig.customDateText"
                        type="button"
                        @click="studioConfig.customDateText = ''; renderCanvas()"
                        class="text-[10px] text-red-500 hover:underline"
                      >
                        অটোমেটিক তারিখে ফিরুন
                      </button>
                    </div>
                    <input
                      type="text"
                      v-model="studioConfig.customDateText"
                      @input="renderCanvas"
                      :placeholder="'📅 ' + bengaliTodayDate"
                      class="w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                    />
                    <p class="text-[10px] text-gray-400 mt-0.5">খালি রাখলে আজকের স্বয়ংক্রিয় বাংলা তারিখ বসবে।</p>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Font Size Slider -->
                    <div>
                      <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold text-gray-700">তারিখ ফন্ট সাইজ:</label>
                        <span class="text-[11px] font-mono font-bold text-red-600">{{ studioConfig.dateFontSize }}px</span>
                      </div>
                      <input
                        type="range"
                        min="14"
                        max="36"
                        step="1"
                        v-model.number="studioConfig.dateFontSize"
                        @input="renderCanvas"
                        class="w-full accent-red-600 cursor-pointer"
                      />
                    </div>

                    <!-- Color Picker -->
                    <div>
                      <label class="text-[11px] font-bold text-gray-700 block mb-1">তারিখের কালার:</label>
                      <div class="flex items-center gap-2">
                        <input
                          type="color"
                          v-model="studioConfig.dateColor"
                          @input="renderCanvas"
                          class="w-7 h-7 rounded-lg cursor-pointer border border-gray-300 p-0.5 bg-white shadow-2xs"
                        />
                        <div class="flex items-center gap-1">
                          <button
                            type="button"
                            @click="studioConfig.dateColor = '#94A3B8'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700"
                          >
                            সিলভার
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.dateColor = '#FFFFFF'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700"
                          >
                            সাদা
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.dateColor = '#FDE047'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800"
                          >
                            হলুদ
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tab 2: Watermark / Portal Link Configuration -->
              <div v-show="activeBrandingTab === 'watermark'" class="space-y-3 pt-1">
                <div class="flex items-center justify-between">
                  <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                    <input
                      type="checkbox"
                      v-model="studioConfig.showWatermark"
                      @change="renderCanvas"
                      class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer"
                    />
                    <span>🌐 পোর্টাল লিংক / ওয়াটারমার্ক প্রদর্শন করুন</span>
                  </label>
                  <span class="text-[11px] text-gray-400">ফটাকার্ডের নিচের ডান অংশে প্রদর্শিত হবে</span>
                </div>

                <div v-if="studioConfig.showWatermark" class="space-y-3 bg-white p-3 rounded-xl border border-gray-100 shadow-2xs">
                  <div>
                    <div class="flex items-center justify-between mb-1">
                      <label class="text-[11px] font-bold text-gray-700">ম্যানুয়াল পোর্টাল লিংক / ডোমেইন:</label>
                      <button
                        v-if="studioConfig.customWatermarkText"
                        type="button"
                        @click="studioConfig.customWatermarkText = ''; renderCanvas()"
                        class="text-[10px] text-red-500 hover:underline"
                      >
                        ডিফল্ট ডোমেইনে ফিরুন
                      </button>
                    </div>
                    <input
                      type="text"
                      v-model="studioConfig.customWatermarkText"
                      @input="renderCanvas"
                      :placeholder="props.portal?.domain || 'www.newsportal.com'"
                      class="w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                    />
                    <p class="text-[10px] text-gray-400 mt-0.5">খালি রাখলে স্বয়ংক্রিয়ভাবে পোর্টালের ডোমেইন ব্যবহৃত হবে।</p>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Font Size Slider -->
                    <div>
                      <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold text-gray-700">লিংক ফন্ট সাইজ:</label>
                        <span class="text-[11px] font-mono font-bold text-red-600">{{ studioConfig.watermarkFontSize }}px</span>
                      </div>
                      <input
                        type="range"
                        min="14"
                        max="36"
                        step="1"
                        v-model.number="studioConfig.watermarkFontSize"
                        @input="renderCanvas"
                        class="w-full accent-red-600 cursor-pointer"
                      />
                    </div>

                    <!-- Color Picker -->
                    <div>
                      <label class="text-[11px] font-bold text-gray-700 block mb-1">লিংকের কালার:</label>
                      <div class="flex items-center gap-2">
                        <input
                          type="color"
                          v-model="studioConfig.watermarkColor"
                          @input="renderCanvas"
                          class="w-7 h-7 rounded-lg cursor-pointer border border-gray-300 p-0.5 bg-white shadow-2xs"
                        />
                        <div class="flex items-center gap-1">
                          <button
                            type="button"
                            @click="studioConfig.watermarkColor = '#E2E8F0'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700"
                          >
                            সিলভার
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.watermarkColor = '#FFFFFF'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700"
                          >
                            সাদা
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.watermarkColor = '#BAE6FD'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-sky-50 hover:bg-sky-100 border border-sky-200 text-sky-800"
                          >
                            লাইট ব্লু
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.watermarkColor = '#FDE047'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800"
                          >
                            হলুদ
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tab 3: Header Branding Configuration -->
              <div v-show="activeBrandingTab === 'branding'" class="space-y-3 pt-1">
                <div class="flex items-center justify-between">
                  <label class="flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                    <input
                      type="checkbox"
                      v-model="studioConfig.showHeaderBranding"
                      @change="renderCanvas"
                      class="rounded text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer"
                    />
                    <span>🏛️ হেডার ব্র্যান্ডিং (নাম ও লোগো) প্রদর্শন করুন</span>
                  </label>
                  <span class="text-[11px] text-gray-400">ফটাকার্ডের উপরের বাম অংশে প্রদর্শিত হবে</span>
                </div>

                <div v-if="studioConfig.showHeaderBranding" class="space-y-3 bg-white p-3 rounded-xl border border-gray-100 shadow-2xs">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label class="text-[11px] font-bold text-gray-700 block mb-1">কাস্টম পোর্টাল নাম:</label>
                      <input
                        type="text"
                        v-model="studioConfig.customPortalName"
                        @input="renderCanvas"
                        :placeholder="props.portal?.name || 'নিউজ পোর্টাল'"
                        class="w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                      />
                    </div>
                    <div>
                      <label class="text-[11px] font-bold text-gray-700 block mb-1">কাস্টম ট্যাগলাইন / স্লোগান:</label>
                      <input
                        type="text"
                        v-model="studioConfig.customTagline"
                        @input="renderCanvas"
                        :placeholder="props.portal?.tagline || 'সত্যের সন্ধানে প্রতিদিন'"
                        class="w-full text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                      />
                    </div>
                  </div>

                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <!-- Header Font Size Slider -->
                    <div>
                      <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-bold text-gray-700">পোর্টাল নাম ফন্ট সাইজ:</label>
                        <span class="text-[11px] font-mono font-bold text-red-600">{{ studioConfig.headerFontSize }}px</span>
                      </div>
                      <input
                        type="range"
                        min="24"
                        max="52"
                        step="2"
                        v-model.number="studioConfig.headerFontSize"
                        @input="renderCanvas"
                        class="w-full accent-red-600 cursor-pointer"
                      />
                    </div>

                    <!-- Header Color Picker -->
                    <div>
                      <label class="text-[11px] font-bold text-gray-700 block mb-1">পোর্টাল নামের কালার:</label>
                      <div class="flex items-center gap-2">
                        <input
                          type="color"
                          v-model="studioConfig.headerColor"
                          @input="renderCanvas"
                          class="w-7 h-7 rounded-lg cursor-pointer border border-gray-300 p-0.5 bg-white shadow-2xs"
                        />
                        <div class="flex items-center gap-1">
                          <button
                            type="button"
                            @click="studioConfig.headerColor = '#FFFFFF'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 border border-gray-200 text-gray-700"
                          >
                            সাদা
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.headerColor = '#FDE047'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800"
                          >
                            হলুদ
                          </button>
                          <button
                            type="button"
                            @click="studioConfig.headerColor = '#F59E0B'; renderCanvas()"
                            class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800"
                          >
                            সোনালী
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: Publishing Form (7 cols) -->
        <div class="lg:col-span-6 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm space-y-6">
          <div class="border-b border-gray-100 pb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="p-1.5 bg-blue-100 text-blue-600 rounded-lg">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </span>
              <h2 class="font-bold text-gray-800 text-base">সংবাদ রচনা ও প্রকাশনা ফর্ম</h2>
            </div>
            <span class="text-xs bg-red-50 text-red-600 px-2.5 py-0.5 rounded-full font-semibold">ডুয়েল পাবলিশ মোড</span>
          </div>

          <form @submit.prevent="submitPublishForm" class="space-y-4">
            <!-- Headline (Title) -->
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">
                সংবাদের শিরোনাম (Headline) <span class="text-red-500">*</span>
              </label>
              <input
                type="text"
                v-model="form.title"
                @input="renderCanvas"
                placeholder="যেমন: পদ্মা সেতুতে নতুন রেকর্ড, একদিনে টোল আদায় ৫ কোটি টাকা"
                class="w-full text-base font-bold border border-gray-200 rounded-2xl p-3 focus:border-red-500 focus:ring-2 focus:ring-red-500/20 transition-all placeholder:text-gray-300"
                required
              />
              <p class="text-[11px] text-gray-400 mt-1">
                💡 আপনি এখানে যা টাইপ করবেন তা সরাসরি বাম পাশের ফটাকার্ডে রিয়েল-টাইমে আপডেট হবে।
              </p>
            </div>

            <!-- Category & Subcategory -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">
                  প্রধান ক্যাটাগরি <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="form.category_id"
                  @change="onCategoryChange"
                  class="w-full text-xs font-medium border border-gray-200 rounded-xl p-2.5 bg-white focus:border-red-500 focus:ring-1 focus:ring-red-500"
                  required
                >
                  <option value="" disabled>ক্যাটাগরি বেছে নিন</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                    {{ '— '.repeat(cat.depth || 0) }}{{ cat.name }}
                  </option>
                </select>
              </div>

              <!-- Division / Region (Optional) -->
              <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">বিভাগ (ঐচ্ছিক)</label>
                <select
                  v-model="form.division_id"
                  class="w-full text-xs font-medium border border-gray-200 rounded-xl p-2.5 bg-white focus:border-red-500 focus:ring-1 focus:ring-red-500"
                >
                  <option :value="null">সারাদেশ / প্রযোজ্য নয়</option>
                  <option v-for="div in divisions" :key="div.id" :value="div.id">
                    {{ div.bn_name || div.name }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Excerpt / Summary -->
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">
                সংবাদের সারসংক্ষেপ (Excerpt) <span class="text-gray-400 font-normal">(ফেসবুক ক্যাপশন ও লিংকের সাথে ব্যবহৃত হবে)</span>
              </label>
              <textarea
                v-model="form.excerpt"
                rows="2"
                placeholder="সংবাদের মূল সারসংক্ষেপ ২-৩ লাইনে লিখুন..."
                class="w-full text-xs border border-gray-200 rounded-xl p-2.5 focus:border-red-500 focus:ring-1 focus:ring-red-500"
              ></textarea>
            </div>

            <!-- News Full Body -->
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">
                সংবাদের বিস্তারিত বিবরণ (Body) <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.body"
                rows="4"
                placeholder="ওয়েবসাইটের বিস্তারিত সংবাদ অনুচ্ছেদ এখানে লিখুন..."
                class="w-full text-xs border border-gray-200 rounded-xl p-2.5 focus:border-red-500 focus:ring-1 focus:ring-red-500"
                required
              ></textarea>
            </div>

            <!-- News Badges / Flags -->
            <div class="flex items-center gap-6 p-3 bg-gray-50 rounded-2xl border border-gray-100">
              <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="form.is_breaking"
                  @change="renderCanvas"
                  class="rounded text-red-600 focus:ring-red-500 w-4 h-4"
                />
                <span class="text-red-600 font-bold">🔴 ব্রেকিং নিউজ</span>
              </label>

              <label class="flex items-center gap-2 text-xs font-bold text-gray-700 cursor-pointer">
                <input
                  type="checkbox"
                  v-model="form.is_featured"
                  class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4"
                />
                <span class="text-blue-600">⭐ হোমপেজ ফিচার্ড</span>
              </label>
            </div>

            <!-- Facebook Auto-Publish Box & Strategy -->
            <div class="bg-gradient-to-br from-blue-50 via-indigo-50/40 to-white p-4 rounded-2xl border border-blue-100 space-y-3">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                    f
                  </div>
                  <div>
                    <h3 class="text-xs font-bold text-gray-800">ফেসবুকে সরাসরি ফটাকার্ড পোস্ট</h3>
                    <p class="text-[11px] text-gray-500">প্রথম কমেন্টে অটোমেটিক নিউজ লিংক সংযুক্ত হবে</p>
                  </div>
                </div>

                <!-- Toggle Switch -->
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" v-model="form.post_to_facebook" class="sr-only peer" />
                  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
              </div>

              <!-- Facebook First Comment Explanation & Preview -->
              <div v-if="form.post_to_facebook" class="space-y-2 pt-2 border-t border-blue-100/70">
                <div class="text-[11px] text-blue-900 bg-blue-100/60 p-2.5 rounded-xl flex items-start gap-2">
                  <span class="text-sm">🎯</span>
                  <span>
                    <strong>টপ নিউজপেপার স্ট্র্যাটেজি:</strong> ফেসবুক অ্যালগরিদমে লিংকের রিচ কমে যাওয়া ঠেকাতে ফটাকার্ডটি মূল ছবি হিসেবে যাবে এবং সংবাদের লাইভ লিংকটি ১ম কমেন্টে বসবে!
                  </span>
                </div>

                <!-- 1st Comment Preview -->
                <div class="bg-white p-2.5 rounded-xl border border-blue-200 text-xs text-gray-700 flex items-center gap-2">
                  <span class="text-blue-600 font-bold">১ম কমেন্ট প্রিভিউ:</span>
                  <span class="text-gray-500 font-mono text-[11px] truncate">
                    বিস্তারিত পড়তে ক্লিক করুন: {{ liveArticlePreviewUrl }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Submit Action Button -->
            <div class="pt-2">
              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-red-600 via-rose-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-extrabold text-base shadow-xl shadow-red-600/25 transition-all flex items-center justify-center gap-3 disabled:opacity-50 cursor-pointer"
              >
                <span v-if="isSubmitting" class="animate-spin text-lg">⚙️</span>
                <svg v-else class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>{{ isSubmitting ? 'ওয়েবসাইট ও ফেসবুকে পোস্ট হচ্ছে...' : 'ওয়েবসাইট ও ফেসবুকে প্রকাশ করুন' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- MAIN TAB 2: PUBLISH HISTORY & LOG TABLE -->
      <div v-show="activeTab === 'history'" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-4">
          <div>
            <h2 class="text-lg font-bold text-gray-800">প্রকাশিত ফটাকার্ড ও সোশ্যাল লগ</h2>
            <p class="text-xs text-gray-500 mt-0.5">ওয়েবসাইট ও ফেসবুক পোস্টের বর্তমান স্ট্যাটাস ও রিশেয়ারিং</p>
          </div>
          <button
            @click="activeTab = 'studio'"
            class="text-xs font-bold text-white bg-red-600 hover:bg-red-700 px-3.5 py-2 rounded-xl transition-colors inline-flex items-center gap-1.5 shadow-sm"
          >
            + নতুন ফটাকার্ড তৈরি করুন
          </button>
        </div>

        <div v-if="history.data.length === 0" class="p-12 text-center text-gray-400 space-y-2">
          <p class="text-3xl">📷</p>
          <p class="text-sm font-semibold text-gray-600">এখনও কোনো ফটাকার্ড সংবাদ প্রকাশিত হয়নি।</p>
          <p class="text-xs">উপরের "নতুন ফটাকার্ড ও প্রকাশনা" ট্যাবে গিয়ে আপনার প্রথম ফটাকার্ড পোস্ট তৈরি করুন।</p>
        </div>

        <!-- History Table -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-wider border-b border-gray-100">
              <tr>
                <th class="py-3 px-4">ফটাকার্ড</th>
                <th class="py-3 px-4">শিরোনাম ও ক্যাটাগরি</th>
                <th class="py-3 px-4">ওয়েবসাইট স্ট্যাটাস</th>
                <th class="py-3 px-4">ফেসবুক স্ট্যাটাস ও কমেন্ট</th>
                <th class="py-3 px-4">লেখক ও তারিখ</th>
                <th class="py-3 px-4 text-right">অ্যাকশন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="item in history.data" :key="item.id" class="hover:bg-gray-50/70 transition-colors">
                <!-- Thumbnail -->
                <td class="py-3 px-4">
                  <div
                    @click="previewImage(item.featured_image?.original || item.featured_image?.sm)"
                    class="w-16 h-16 rounded-xl overflow-hidden border border-gray-200 shadow-xs cursor-pointer group relative bg-gray-100 flex-shrink-0"
                  >
                    <img
                      :src="item.featured_image?.original || item.featured_image?.sm"
                      class="w-full h-full object-cover group-hover:scale-110 transition-transform"
                    />
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-bold">
                      🔍
                    </div>
                  </div>
                </td>

                <!-- Title & Category -->
                <td class="py-3 px-4 max-w-xs">
                  <div class="space-y-1">
                    <span v-if="item.category" class="inline-block px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-[10px] font-semibold">
                      {{ item.category }}
                    </span>
                    <h3 class="font-bold text-gray-900 text-sm line-clamp-2 leading-snug">
                      {{ item.title }}
                    </h3>
                  </div>
                </td>

                <!-- Website Status -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <div class="space-y-1">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-semibold">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                      লাইভ
                    </span>
                    <div>
                      <a
                        :href="item.news_url"
                        target="_blank"
                        class="text-[11px] text-red-600 hover:text-red-700 hover:underline font-semibold flex items-center gap-0.5"
                      >
                        সংবাদ দেখুন ↗
                      </a>
                    </div>
                  </div>
                </td>

                <!-- Facebook Status -->
                <td class="py-3 px-4 whitespace-nowrap">
                  <div v-if="item.facebook_post" class="space-y-1">
                    <!-- Status Badge -->
                    <div class="flex items-center gap-1.5">
                      <span
                        v-if="item.facebook_post.status === 'published'"
                        :class="[
                          'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold',
                          item.facebook_post.is_simulation
                            ? 'bg-amber-50 text-amber-700 border border-amber-200'
                            : 'bg-blue-50 text-blue-700 border border-blue-200'
                        ]"
                      >
                        <span class="w-1.5 h-1.5 rounded-full" :class="item.facebook_post.is_simulation ? 'bg-amber-500' : 'bg-blue-600'"></span>
                        {{ item.facebook_post.is_simulation ? 'সিমুলেশন পাবলিশড' : 'ফেসবুকে প্রকাশিত' }}
                      </span>

                      <span v-else class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200 text-[11px] font-semibold">
                        ব্যর্থ
                      </span>
                    </div>

                    <!-- 1st Comment Badge -->
                    <div v-if="item.facebook_post.has_first_comment" class="text-[10px] text-gray-500 flex items-center gap-1 font-medium">
                      <span>💬</span> ১ম কমেন্টে লিংক যুক্ত হয়েছে
                    </div>

                    <!-- FB Post Link if live -->
                    <div v-if="item.facebook_post.post_url">
                      <a
                        :href="item.facebook_post.post_url"
                        target="_blank"
                        class="text-[11px] text-blue-600 hover:underline font-medium"
                      >
                        ফেসবুক পোস্ট দেখুন ↗
                      </a>
                    </div>
                  </div>

                  <div v-else class="text-gray-400 text-[11px]">
                    ফেসবুকে শেয়ার করা হয়নি
                  </div>
                </td>

                <!-- Author & Date -->
                <td class="py-3 px-4 whitespace-nowrap text-gray-500 text-[11px]">
                  <p class="font-semibold text-gray-700">{{ item.author }}</p>
                  <p>{{ item.published_at }}</p>
                </td>

                <!-- Actions -->
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <button
                    @click="reSharePost(item)"
                    :disabled="reSharingId === item.id"
                    class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-blue-50 text-gray-700 hover:text-blue-700 text-xs font-bold transition-all border border-gray-200 inline-flex items-center gap-1 disabled:opacity-50"
                    title="ফেসবুকে পুনরায় শেয়ার করুন"
                  >
                    <span v-if="reSharingId === item.id" class="animate-spin text-xs">⚙️</span>
                    <svg v-else class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    পুনরায় শেয়ার
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- MODAL: FACEBOOK SETTINGS & TEST CONNECTION -->
      <div
        v-if="showSettingsModal"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 animate-fadeIn"
      >
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-5">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <div class="flex items-center gap-2.5">
              <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold">
                f
              </div>
              <h3 class="font-bold text-gray-800 text-base">ফেসবুক পেজ সেটিংস ও সংযোগ</h3>
            </div>
            <button
              @click="showSettingsModal = false"
              class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-sm font-bold"
            >
              ✕
            </button>
          </div>

          <!-- Connection Status Card -->
          <div
            :class="[
              'p-3.5 rounded-2xl border text-xs flex items-center justify-between',
              facebookSettings.is_connected
                ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
                : 'bg-amber-50 border-amber-200 text-amber-800'
            ]"
          >
            <div>
              <p class="font-bold">
                {{ facebookSettings.is_connected ? '✓ ফেসবুক পেজ সক্রিয়' : '⚠️ সিমুলেশন / ডেমো মোড সক্রিয়' }}
              </p>
              <p class="text-[11px] mt-0.5 opacity-80">
                {{ facebookSettings.is_connected ? 'পোস্ট সরাসরি মেটা গ্রাফ এপিআই দিয়ে ফেসবুকে যাবে।' : 'টোকেন না দিলে ড্যাশবোর্ডে সিমুলেশন হিসেবে পরীক্ষা করা যাবে।' }}
              </p>
            </div>
          </div>

          <!-- Settings Form -->
          <form @submit.prevent="saveFacebookSettings" class="space-y-4 text-xs">
            <div>
              <label class="block font-bold text-gray-700 mb-1">ফেসবুক পেজ আইডি (Page ID) <span class="text-red-500">*</span></label>
              <input
                type="text"
                v-model="fbSettingsForm.page_id"
                placeholder="যেমন: 104829104829102"
                class="w-full text-xs border border-gray-200 rounded-xl p-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono"
                required
              />
            </div>

            <div>
              <label class="block font-bold text-gray-700 mb-1">ফেসবুক পেজের নাম (Page Name)</label>
              <input
                type="text"
                v-model="fbSettingsForm.page_name"
                placeholder="যেমন: দৈনিক নিউজপোর্টাল অফিসিয়াল"
                class="w-full text-xs border border-gray-200 rounded-xl p-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="font-bold text-gray-700">পেজ অ্যাক্সেস টোকেন (Page Access Token)</label>
                <span v-if="facebookSettings.has_token" class="text-emerald-600 font-semibold text-[10px]">✓ টোকেন সেভ করা আছে</span>
              </div>
              <input
                type="password"
                v-model="fbSettingsForm.access_token"
                :placeholder="facebookSettings.has_token ? 'টোকেন অপরিবর্তিত রাখতে খালি রাখুন' : 'EAAxxxxxxxxxxxx...'"
                class="w-full text-xs border border-gray-200 rounded-xl p-2.5 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 font-mono"
              />
              <p class="text-[10px] text-gray-400 mt-1">
                মেটা ফর ডেভেলপার্স থেকে আপনার পেজের দীর্ঘমেয়াদী (Never-expiring) অ্যাক্সেস টোকেন দিন।
              </p>
            </div>

            <!-- Test Connection Result Box -->
            <div v-if="testResult" :class="['p-3 rounded-xl border text-xs', testResult.success ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800']">
              <p class="font-bold">{{ testResult.success ? '✓ কানেকশন সফল!' : '✕ কানেকশন ব্যর্থ' }}</p>
              <p class="text-[11px] mt-0.5">{{ testResult.message }}</p>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
              <button
                type="button"
                @click="testFacebookConnection"
                :disabled="isTestingConnection"
                class="px-4 py-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs transition-colors flex items-center gap-1.5 disabled:opacity-50"
              >
                <span v-if="isTestingConnection" class="animate-spin">⚙️</span>
                <span>সংযোগ পরীক্ষা করুন</span>
              </button>

              <button
                type="submit"
                :disabled="isSavingSettings"
                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition-all disabled:opacity-50"
              >
                {{ isSavingSettings ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- MODAL: IMAGE PREVIEW -->
      <div
        v-if="previewImageUrl"
        @click="previewImageUrl = null"
        class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4 cursor-pointer animate-fadeIn"
      >
        <div class="max-w-2xl w-full bg-transparent p-2 text-center" @click.stop>
          <img :src="previewImageUrl" class="w-full rounded-2xl shadow-2xl border border-white/20" />
          <button
            @click="previewImageUrl = null"
            class="mt-4 px-4 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-full text-xs font-bold transition-colors"
          >
            বন্ধ করুন (✕)
          </button>
        </div>
      </div>

      <!-- Media Picker Modal -->
      <MediaPickerModal
        :show="showMediaPickerModal"
        title="ফটাকার্ডের জন্য ছবি নির্বাচন করুন"
        @close="showMediaPickerModal = false"
        @select="handleMediaSelected"
      />

      <!-- MODAL: CARD HISTORY & STUDIO TEMPLATES -->
      <div
        v-if="isPreviousCardsModalOpen"
        class="fixed inset-0 z-50 bg-black/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-6 animate-fadeIn"
        @click.self="isPreviousCardsModalOpen = false"
      >
        <div class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100">
          <!-- Modal Header -->
          <div class="p-5 sm:p-6 bg-gradient-to-r from-gray-900 via-slate-900 to-red-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-800">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="p-1.5 bg-red-600/90 rounded-lg text-white">
                  <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                  </svg>
                </span>
                <h3 class="text-lg sm:text-xl font-bold">📁 পূর্বের ফটাকার্ড ও রেডিমেড টেমপ্লেট</h3>
              </div>
              <p class="text-xs text-gray-300">পূর্বে তৈরিকৃত সংবাদ কার্ড বা রেডিমেড ডিজাইন থেকে যেকোনো কার্ড বেছে নিন</p>
            </div>

            <div class="flex items-center gap-3">
              <!-- Live Search Filter -->
              <div class="relative w-full sm:w-64">
                <input
                  type="text"
                  v-model="cardSearchQuery"
                  placeholder="শিরোনাম বা ক্যাটাগরি খুঁজুন..."
                  class="w-full text-xs pl-8 pr-7 py-2 bg-gray-800/90 text-white placeholder-gray-400 border border-gray-700 rounded-xl focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                />
                <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <button
                  v-if="cardSearchQuery"
                  @click="cardSearchQuery = ''"
                  class="absolute right-2.5 top-2 text-gray-400 hover:text-white text-xs"
                >✕</button>
              </div>

              <!-- Close Button -->
              <button
                @click="isPreviousCardsModalOpen = false"
                class="w-8 h-8 rounded-full bg-gray-800 hover:bg-gray-700 text-gray-300 hover:text-white flex items-center justify-center transition-colors text-sm font-bold flex-shrink-0 cursor-pointer"
                title="বন্ধ করুন"
              >
                ✕
              </button>
            </div>
          </div>

          <!-- Navigation Tabs -->
          <div class="flex border-b border-gray-200 bg-gray-50/70 px-4 sm:px-6 gap-2 sm:gap-4 overflow-x-auto text-xs font-bold">
            <button
              type="button"
              @click="modalActiveTab = 'previous'"
              :class="[
                'py-3.5 px-3 border-b-2 flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap',
                modalActiveTab === 'previous'
                  ? 'border-red-600 text-red-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              ]"
            >
              <span>🗂️ পূর্বে প্রকাশিত ও তৈরিকৃত কার্ড</span>
              <span class="px-2 py-0.5 rounded-full text-[10px]" :class="modalActiveTab === 'previous' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700'">
                {{ combinedPreviousCards.length }}
              </span>
            </button>

            <button
              type="button"
              @click="modalActiveTab = 'readyStyles'"
              :class="[
                'py-3.5 px-3 border-b-2 flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap',
                modalActiveTab === 'readyStyles'
                  ? 'border-red-600 text-red-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              ]"
            >
              <span>🎨 রেডিমেড ফটাকার্ড স্টাইল</span>
              <span class="px-2 py-0.5 rounded-full text-[10px]" :class="modalActiveTab === 'readyStyles' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700'">
                {{ readyStyles.length }}
              </span>
            </button>

            <button
              type="button"
              @click="modalActiveTab = 'savedCards'"
              :class="[
                'py-3.5 px-3 border-b-2 flex items-center gap-2 transition-all cursor-pointer whitespace-nowrap',
                modalActiveTab === 'savedCards'
                  ? 'border-red-600 text-red-600'
                  : 'border-transparent text-gray-600 hover:text-gray-900'
              ]"
            >
              <span>💾 সেভ করা ফেভারিট কার্ড</span>
              <span class="px-2 py-0.5 rounded-full text-[10px]" :class="modalActiveTab === 'savedCards' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700'">
                {{ savedCardHistory.length }}
              </span>
            </button>
          </div>

          <!-- Body Content Area -->
          <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50/50">
            <!-- TAB 1: PREVIOUS CARDS -->
            <div v-show="modalActiveTab === 'previous'" class="space-y-4">
              <div v-if="filteredPreviousCards.length === 0" class="py-12 text-center text-gray-400 space-y-2">
                <p class="text-4xl">📭</p>
                <p class="text-sm font-semibold text-gray-700">কোনো পূর্ববর্তী কার্ড পাওয়া যায়নি</p>
                <p class="text-xs text-gray-500">
                  {{ cardSearchQuery ? 'আপনার সার্চের সাথে মিলছে এমন কোনো কার্ড নেই।' : 'পূর্বে তৈরিকৃত সংবাদ প্রকাশ বা ডাউনলোড করলে কার্ডগুলো এখানে প্রদর্শিত হবে।' }}
                </p>
              </div>

              <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="card in filteredPreviousCards"
                  :key="card.id"
                  class="bg-white rounded-2xl border border-gray-200 hover:border-red-400 hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col justify-between group cursor-pointer"
                  @click="loadPreviousCard(card)"
                >
                  <!-- Card Image / Thumbnail Header -->
                  <div class="relative aspect-video bg-gray-900 overflow-hidden">
                    <img
                      v-if="getCardImage(card)"
                      :src="getCardImage(card)"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      loading="lazy"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 text-gray-400">
                      <span class="text-2xl">📰</span>
                    </div>

                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                    <!-- Top Badges -->
                    <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between gap-1">
                      <span class="px-2 py-0.5 rounded-lg bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold">
                        {{ card.category || 'সংবাদ' }}
                      </span>
                      <span
                        v-if="card.isSavedCustom"
                        class="px-2 py-0.5 rounded-lg bg-emerald-500/90 text-white text-[10px] font-bold"
                      >
                        ✓ কাস্টম সেভড
                      </span>
                      <span
                        v-else
                        class="px-2 py-0.5 rounded-lg bg-blue-600/90 text-white text-[10px] font-bold"
                      >
                        পূর্বে প্রকাশিত
                      </span>
                    </div>

                    <!-- Date & Breaking tag at bottom of image -->
                    <div class="absolute bottom-2 left-2.5 right-2.5 flex items-center justify-between text-[11px] text-gray-300 font-medium">
                      <span>📅 {{ card.published_at || card.saved_at || 'সাম্প্রতিক' }}</span>
                      <span v-if="card.is_breaking" class="text-red-400 font-bold">🔴 ব্রেকিং</span>
                    </div>
                  </div>

                  <!-- Card Content -->
                  <div class="p-3.5 space-y-2 flex-1 flex flex-col justify-between">
                    <div>
                      <h4 class="font-bold text-gray-900 text-xs sm:text-sm line-clamp-2 group-hover:text-red-600 transition-colors leading-snug">
                        {{ card.title }}
                      </h4>
                      <p v-if="card.excerpt" class="text-[11px] text-gray-500 line-clamp-2 mt-1 leading-relaxed">
                        {{ card.excerpt }}
                      </p>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2">
                      <button
                        type="button"
                        @click.stop="loadPreviousCard(card)"
                        class="w-full py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 active:scale-[0.98] rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                      >
                        <span>⚡ এই কার্ডটি নির্বাচন করুন</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 2: READY-MADE CARD STYLES -->
            <div v-show="modalActiveTab === 'readyStyles'" class="space-y-4">
              <div class="p-4 bg-gradient-to-r from-rose-50 to-red-50 border border-red-100 rounded-2xl flex items-center justify-between">
                <div>
                  <h4 class="font-bold text-gray-800 text-xs sm:text-sm">রেডিমেড ডিজাইন প্রিসেট</h4>
                  <p class="text-[11px] text-gray-600 mt-0.5">যেকোনো একটি স্টাইলে ক্লিক করলেই সরাসরি স্টুডিওতে গ্রেডিয়েন্ট ও ফন্ট কনফিগারেশন প্রয়োগ হবে।</p>
                </div>
                <span class="text-xl">🎨</span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="style in readyStyles"
                  :key="style.id"
                  class="bg-white rounded-2xl border border-gray-200 hover:border-red-400 hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col justify-between group cursor-pointer"
                  @click="applyReadyStyle(style)"
                >
                  <!-- Style Visual Preview Box -->
                  <div
                    class="p-4 rounded-t-2xl bg-gradient-to-b relative h-40 flex flex-col justify-between text-white overflow-hidden shadow-inner"
                    :class="style.bgGradientClass"
                  >
                    <!-- Header inside preview -->
                    <div class="flex items-center justify-between z-10">
                      <div class="flex items-center gap-1.5">
                        <span class="text-lg">{{ style.icon }}</span>
                        <span class="text-xs font-bold">{{ style.name }}</span>
                      </div>
                      <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/20 backdrop-blur-xs">
                        {{ style.badge }}
                      </span>
                    </div>

                    <!-- Fake Title Mock inside preview -->
                    <div class="z-10 space-y-1">
                      <div v-if="style.is_breaking" class="inline-block px-2 py-0.5 rounded bg-red-600 text-[10px] font-bold text-white mb-0.5">
                        🔴 ব্রেকিং নিউজ
                      </div>
                      <p
                        class="text-xs sm:text-sm font-extrabold leading-tight line-clamp-2"
                        :style="{ color: style.textColor }"
                      >
                        {{ form.title || 'সংবাদের শিরোনামের নমুনা স্টাইল প্রাকদর্শন' }}
                      </p>
                    </div>

                    <!-- Footer inside preview -->
                    <div class="z-10 flex items-center justify-between text-[9px] pt-1 border-t border-white/20" :style="{ color: style.dateColor }">
                      <span>📅 {{ bengaliTodayDate }}</span>
                      <span :style="{ color: style.watermarkColor }">🌐 newsportal.com</span>
                    </div>

                    <!-- Decorative background glow -->
                    <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                  </div>

                  <!-- Details & Apply Button -->
                  <div class="p-3.5 space-y-2 flex-1 flex flex-col justify-between">
                    <div>
                      <h4 class="font-bold text-gray-900 text-xs sm:text-sm group-hover:text-red-600 transition-colors">
                        {{ style.name }}
                      </h4>
                      <p class="text-[11px] text-gray-500 mt-0.5 leading-relaxed">
                        {{ style.description }}
                      </p>
                    </div>

                    <button
                      type="button"
                      @click.stop="applyReadyStyle(style)"
                      class="w-full py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 active:scale-[0.98] rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                      <span>🎨 এই স্টাইলটি স্টুডিওতে প্রয়োগ করুন</span>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 3: SAVED FAVORITE CARDS -->
            <div v-show="modalActiveTab === 'savedCards'" class="space-y-4">
              <!-- Top Action Bar -->
              <div class="p-4 bg-white border border-gray-200 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                <div>
                  <h4 class="font-bold text-gray-800 text-xs sm:text-sm">💾 আপনার ব্যক্তিগত সংরক্ষিত কার্ড তালিকা</h4>
                  <p class="text-[11px] text-gray-500 mt-0.5">বর্তমান লাইভ স্টুডিও কার্ডটি ভবিষ্যতে ব্যবহারের জন্য সেভ করে রাখতে পারেন।</p>
                </div>
                <button
                  type="button"
                  @click="saveCurrentCardAsTemplate"
                  class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 rounded-xl transition-all shadow-xs flex items-center justify-center gap-1.5 flex-shrink-0 cursor-pointer"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                  </svg>
                  <span>➕ বর্তমান লাইভ কার্ডটি সেভ করুন</span>
                </button>
              </div>

              <!-- List of Saved Cards -->
              <div v-if="filteredSavedCards.length === 0" class="py-12 text-center text-gray-400 space-y-2">
                <p class="text-4xl">📂</p>
                <p class="text-sm font-semibold text-gray-700">কোনো সেভ করা কার্ড নেই</p>
                <p class="text-xs text-gray-500">
                  উপরের "বর্তমান লাইভ কার্ডটি সেভ করুন" বাটনে ক্লিক করে বর্তমান কার্ডটি সেভ করতে পারেন অথবা ফটাকার্ড ডাউনলোড করলে তা স্বয়ংক্রিয়ভাবে এখানে যুক্ত হবে।
                </p>
              </div>

              <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div
                  v-for="card in filteredSavedCards"
                  :key="card.id"
                  class="bg-white rounded-2xl border border-gray-200 hover:border-red-400 hover:shadow-lg transition-all duration-200 overflow-hidden flex flex-col justify-between group"
                >
                  <!-- Card Thumbnail -->
                  <div class="relative aspect-video bg-gray-900 overflow-hidden">
                    <img
                      v-if="card.image"
                      :src="card.image"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                      loading="lazy"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900 text-gray-400">
                      <span class="text-2xl">📷</span>
                    </div>

                    <!-- Overlay Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/20 to-transparent"></div>

                    <!-- Category badge -->
                    <div class="absolute top-2.5 left-2.5">
                      <span class="px-2 py-0.5 rounded-lg bg-black/60 backdrop-blur-xs text-white text-[10px] font-bold">
                        {{ card.category || 'ক্যাটাগরি' }}
                      </span>
                    </div>

                    <!-- Delete button -->
                    <button
                      type="button"
                      @click="deleteSavedCard(card.id)"
                      class="absolute top-2.5 right-2.5 w-6 h-6 rounded-lg bg-black/60 hover:bg-red-600 text-white flex items-center justify-center transition-colors text-xs cursor-pointer"
                      title="মুছে ফেলুন"
                    >
                      🗑️
                    </button>

                    <div class="absolute bottom-2 left-2.5 right-2.5 text-[11px] text-gray-300 font-medium">
                      <span>🕒 সেভ: {{ card.saved_at || 'কিছুক্ষণ আগে' }}</span>
                    </div>
                  </div>

                  <!-- Details -->
                  <div class="p-3.5 space-y-2 flex-1 flex flex-col justify-between">
                    <div>
                      <h4 class="font-bold text-gray-900 text-xs sm:text-sm line-clamp-2 group-hover:text-red-600 transition-colors leading-snug">
                        {{ card.title }}
                      </h4>
                      <p v-if="card.excerpt" class="text-[11px] text-gray-500 line-clamp-2 mt-1 leading-relaxed">
                        {{ card.excerpt }}
                      </p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-2 flex items-center gap-2">
                      <button
                        type="button"
                        @click="loadPreviousCard(card)"
                        class="flex-1 py-2 px-3 text-xs font-bold text-white bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 active:scale-[0.98] rounded-xl shadow-xs transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                      >
                        <span>⚡ স্টুডিওতে লোড করুন</span>
                      </button>
                      <button
                        type="button"
                        @click="deleteSavedCard(card.id)"
                        class="p-2 text-gray-400 hover:text-red-600 rounded-xl hover:bg-red-50 border border-gray-200 transition-colors text-xs cursor-pointer"
                        title="কার্ডটি মুছুন"
                      >
                        🗑️
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="p-3.5 sm:p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
            <span class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
              পছন্দের কার্ডে ক্লিক করলেই তা সাথে সাথে স্টুডিও ক্যানভাসে লোড হবে
            </span>
            <button
              type="button"
              @click="isPreviousCardsModalOpen = false"
              class="px-4 py-1.5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold transition-colors cursor-pointer"
            >
              বন্ধ করুন
            </button>
          </div>
        </div>
      </div>

      <!-- Floating Success Toast Notification -->
      <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="cardSuccessToast"
          class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white px-5 py-3 rounded-2xl shadow-2xl border border-gray-700 flex items-center gap-3"
        >
          <span class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center font-bold text-xs">✓</span>
          <span class="text-xs font-semibold">{{ cardSuccessToast }}</span>
          <button @click="cardSuccessToast = ''" class="text-gray-400 hover:text-white ml-2 text-xs cursor-pointer">✕</button>
        </div>
      </transition>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MediaPickerModal from '@/Components/UI/MediaPickerModal.vue'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  divisions: { type: Array, default: () => [] },
  history: { type: Object, default: () => ({ data: [] }) },
  facebookSettings: { type: Object, default: () => ({}) },
  portal: { type: Object, default: () => ({}) },
  stats: { type: Object, default: () => ({ total_photocards: 0, fb_published: 0, today_count: 0 }) },
})

const page = usePage()

// UI state
const activeTab = ref('studio')
const showSettingsModal = ref(false)
const showMediaPickerModal = ref(false)
const previewImageUrl = ref(null)
const isCanvasRendering = ref(false)
const isSubmitting = ref(false)
const reSharingId = ref(null)
const isTestingConnection = ref(false)
const isSavingSettings = ref(false)
const testResult = ref(null)

// Previous Cards & Ready-Made Templates Modal state
const isPreviousCardsModalOpen = ref(false)
const modalActiveTab = ref('previous')
const cardSearchQuery = ref('')
const savedCardHistory = ref([])
const cardSuccessToast = ref('')
let toastTimeout = null

// Recent Uploads / Gallery Selections
const recentUploads = ref([])

// Canvas ref
const photocardCanvas = ref(null)

// Background presets
const backgroundPresets = [
  {
    name: 'জাতীয় সংসদ / সংবাদ',
    url: 'https://images.unsplash.com/photo-1541872703-74c5e44368f9?auto=format&fit=crop&w=1200&q=80',
  },
  {
    name: 'প্রেস মিট / রাজনীতি',
    url: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&w=1200&q=80',
  },
  {
    name: 'স্টেডিয়াম / খেলাধুলা',
    url: 'https://images.unsplash.com/photo-1579952363873-27f3bade9f55?auto=format&fit=crop&w=1200&q=80',
  },
  {
    name: 'অর্থনীতি / বাণিজ্য',
    url: 'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=1200&q=80',
  },
  {
    name: 'প্রযুক্তি / বিজ্ঞান',
    url: 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
  },
]
const selectedPresetUrl = ref(backgroundPresets[0].url)
const loadedImage = ref(null)

const activeBrandingTab = ref('date')

// Studio Config
const studioConfig = reactive({
  gradientPreset: 'cinematic_dark',

  // Headline Typography & Color
  fontSize: 48,
  textColor: '#FFFFFF',

  // Bengali Date Configuration (Manual Edit, Size, Color)
  showDate: true,
  customDateText: '', // Defaults to bengaliTodayDate if blank
  dateFontSize: 22,
  dateColor: '#94A3B8',

  // Portal Link / Watermark Configuration (Manual Edit, Size, Color)
  showWatermark: true,
  customWatermarkText: '', // Defaults to props.portal?.domain || 'www.newsportal.com'
  watermarkFontSize: 22,
  watermarkColor: '#E2E8F0',

  // Header Branding Configuration (Manual Portal Name, Tagline, Size, Color)
  showHeaderBranding: true,
  customPortalName: '', // Defaults to props.portal?.name || 'নিউজ পোর্টাল'
  customTagline: '', // Defaults to props.portal?.tagline || 'সত্যের সন্ধানে প্রতিদিন'
  headerFontSize: 36,
  headerColor: '#FFFFFF',
})

// Publishing Form
const form = reactive({
  title: 'রাজধানীতে মেট্রোরেলের নতুন রুট চালু, যাত্রীদের উপচে পড়া ভিড়',
  category_id: props.categories.length ? props.categories[0].id : '',
  division_id: null,
  district_id: null,
  excerpt: 'নগরবাসীর দীর্ঘ প্রতীক্ষিত নতুন মেট্রোরেল রুটে আজ সকাল থেকেই উৎসাহী মানুষের উপচে পড়া ভিড় দেখা গেছে।',
  body: 'রাজধানীতে বহুল প্রতীক্ষিত মেট্রোরেলের সম্প্রসারিত নতুন রুটে আজ আনুষ্ঠানিকভাবে যাত্রী চলাচল শুরু হয়েছে। সকাল থেকেই বিভিন্ন স্টেশনে মানুষের উপচে পড়া ভিড় লক্ষ্য করা গেছে। কর্তৃপক্ষ জানিয়েছে, প্রথম দিনেই প্রায় এক লক্ষ যাত্রী ভ্রমণ করেছেন।',
  photocard_image: '',
  post_to_facebook: true,
  custom_caption: '',
  is_breaking: false,
  is_featured: false,
})

// Facebook Settings Form
const fbSettingsForm = reactive({
  page_id: props.facebookSettings.page_id || '',
  page_name: props.facebookSettings.page_name || '',
  access_token: '',
})

// Current Category Name
const currentCategoryName = computed(() => {
  if (!form.category_id) return 'জাতীয়'
  const found = props.categories.find(c => c.id === form.category_id)
  return found ? found.name : 'জাতীয়'
})

// Bengali Date formatted
const bengaliTodayDate = computed(() => {
  const date = new Date()
  const banglaDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
  const months = ['জানুয়ারি', 'ফেব্রুয়ারি', 'মার্চ', 'এপ্রিল', 'মে', 'জুন', 'জুলাই', 'আগস্ট', 'সেপ্টেম্বর', 'অক্টোবর', 'নভেম্বর', 'ডিসেম্বর']

  const day = String(date.getDate()).split('').map(d => banglaDigits[d] || d).join('')
  const month = months[date.getMonth()]
  const year = String(date.getFullYear()).split('').map(d => banglaDigits[d] || d).join('')

  return `${day} ${month} ${year}`
})

// Live Article Preview URL
const liveArticlePreviewUrl = computed(() => {
  const domain = props.portal?.domain || window.location.host
  const base = domain.startsWith('http') ? domain : `https://${domain}`
  return `${base}/news/...`
})

// On Category Change
const onCategoryChange = () => {
  renderCanvas()
}

// Recent uploads helpers
const addToRecentUploads = (url) => {
  if (!url) return
  const list = [url, ...recentUploads.value.filter(u => u !== url)].slice(0, 8)
  recentUploads.value = list
  try {
    localStorage.setItem('photocard_recent_images', JSON.stringify(list))
  } catch (e) {
    console.error('Failed to save recent images to localStorage', e)
  }
}

const clearRecentUploads = () => {
  recentUploads.value = []
  try {
    localStorage.removeItem('photocard_recent_images')
  } catch (e) {
    console.error(e)
  }
}

// Media Selection from MediaPickerModal
const handleMediaSelected = (item) => {
  const url = item?.url || item
  if (!url) return
  selectedPresetUrl.value = url
  addToRecentUploads(url)
  loadCanvasImage(url)
}

// Preset Selection
const selectPreset = (preset) => {
  selectedPresetUrl.value = preset.url
  loadCanvasImage(preset.url)
}

// Custom Image Upload
const handleCustomImageUpload = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = (e) => {
    selectedPresetUrl.value = null
    addToRecentUploads(e.target.result)
    loadCanvasImage(e.target.result)
  }
  reader.readAsDataURL(file)
  event.target.value = ''
}

// Load Image Helper
const loadCanvasImage = (src) => {
  isCanvasRendering.value = true
  const img = new Image()
  img.crossOrigin = 'anonymous'
  img.onload = () => {
    loadedImage.value = img
    isCanvasRendering.value = false
    renderCanvas()
  }
  img.onerror = () => {
    isCanvasRendering.value = false
    renderCanvas()
  }
  img.src = src
}

// RENDER CANVAS (Live Photocard Studio Core)
const renderCanvas = () => {
  const canvas = photocardCanvas.value
  if (!canvas) return
  const ctx = canvas.getContext('2d')
  const width = canvas.width
  const height = canvas.height

  ctx.clearRect(0, 0, width, height)

  // 1. Draw Background Image
  if (loadedImage.value) {
    // Aspect-fill image onto square canvas
    const img = loadedImage.value
    const scale = Math.max(width / img.width, height / img.height)
    const x = (width / 2) - (img.width / 2) * scale
    const y = (height / 2) - (img.height / 2) * scale
    ctx.drawImage(img, x, y, img.width * scale, img.height * scale)
  } else {
    // Fallback dark gradient background
    const bgGrad = ctx.createLinearGradient(0, 0, width, height)
    bgGrad.addColorStop(0, '#1E293B')
    bgGrad.addColorStop(1, '#0F172A')
    ctx.fillStyle = bgGrad
    ctx.fillRect(0, 0, width, height)
  }

  // 2. Draw Overlay Gradient
  const grad = ctx.createLinearGradient(0, height * 0.25, 0, height)
  if (studioConfig.gradientPreset === 'breaking_red') {
    grad.addColorStop(0, 'rgba(15, 23, 42, 0.0)')
    grad.addColorStop(0.5, 'rgba(153, 27, 27, 0.65)')
    grad.addColorStop(1, 'rgba(69, 10, 10, 0.98)')
  } else if (studioConfig.gradientPreset === 'royal_navy') {
    grad.addColorStop(0, 'rgba(15, 23, 42, 0.0)')
    grad.addColorStop(0.5, 'rgba(30, 58, 138, 0.7)')
    grad.addColorStop(1, 'rgba(15, 23, 42, 0.98)')
  } else if (studioConfig.gradientPreset === 'emerald_green') {
    grad.addColorStop(0, 'rgba(6, 78, 59, 0.0)')
    grad.addColorStop(0.5, 'rgba(6, 95, 70, 0.7)')
    grad.addColorStop(1, 'rgba(2, 44, 34, 0.98)')
  } else if (studioConfig.gradientPreset === 'sunset_amber') {
    grad.addColorStop(0, 'rgba(120, 53, 15, 0.0)')
    grad.addColorStop(0.5, 'rgba(180, 83, 9, 0.65)')
    grad.addColorStop(1, 'rgba(24, 24, 27, 0.98)')
  } else if (studioConfig.gradientPreset === 'purple_rose') {
    grad.addColorStop(0, 'rgba(88, 28, 135, 0.0)')
    grad.addColorStop(0.5, 'rgba(112, 26, 117, 0.7)')
    grad.addColorStop(1, 'rgba(30, 10, 40, 0.98)')
  } else {
    // Default cinematic dark
    grad.addColorStop(0, 'rgba(0, 0, 0, 0.0)')
    grad.addColorStop(0.45, 'rgba(0, 0, 0, 0.45)')
    grad.addColorStop(0.8, 'rgba(0, 0, 0, 0.92)')
    grad.addColorStop(1, 'rgba(0, 0, 0, 0.98)')
  }
  ctx.fillStyle = grad
  ctx.fillRect(0, 0, width, height)

  // Top Subtle Gradient for Header Branding
  const topGrad = ctx.createLinearGradient(0, 0, 0, 220)
  topGrad.addColorStop(0, 'rgba(0, 0, 0, 0.85)')
  topGrad.addColorStop(1, 'rgba(0, 0, 0, 0.0)')
  ctx.fillStyle = topGrad
  ctx.fillRect(0, 0, width, 220)

  // 3. Draw Header Branding (Portal Name, Logo Badge, Breaking Tag)
  const padX = 75

  if (studioConfig.showHeaderBranding !== false) {
    const portalName = (studioConfig.customPortalName && studioConfig.customPortalName.trim())
      ? studioConfig.customPortalName.trim()
      : (props.portal?.name || 'নিউজ পোর্টাল')
    const initialLetter = portalName.charAt(0) || 'N'

    // Portal Logo / Circle
    ctx.save()
    ctx.fillStyle = '#DC2626'
    ctx.beginPath()
    ctx.arc(padX + 28, 80, 28, 0, Math.PI * 2)
    ctx.fill()
    ctx.fillStyle = '#FFFFFF'
    ctx.font = 'bold 28px "Hind Siliguri", "Noto Sans Bengali", sans-serif'
    ctx.textAlign = 'center'
    ctx.textBaseline = 'middle'
    ctx.fillText(initialLetter, padX + 28, 81)
    ctx.restore()

    // Portal Name & Tagline
    const headerFontSize = Number(studioConfig.headerFontSize) || 36
    ctx.fillStyle = studioConfig.headerColor || '#FFFFFF'
    ctx.font = `bold ${headerFontSize}px "Hind Siliguri", "Noto Sans Bengali", sans-serif`
    ctx.textAlign = 'left'
    ctx.fillText(portalName, padX + 70, 75)

    const tagline = (studioConfig.customTagline && studioConfig.customTagline.trim())
      ? studioConfig.customTagline.trim()
      : (props.portal?.tagline || 'সত্যের সন্ধানে প্রতিদিন')
    if (tagline) {
      ctx.fillStyle = '#CBD5E1'
      ctx.font = '500 20px "Hind Siliguri", "Noto Sans Bengali", sans-serif'
      ctx.fillText(tagline, padX + 70, 102)
    }
  }

  // Breaking News Banner (if checked)
  if (form.is_breaking) {
    const bW = 240
    const bH = 50
    const bX = width - padX - bW
    const bY = 55

    ctx.fillStyle = '#DC2626'
    drawRoundedRect(ctx, bX, bY, bW, bH, 12)
    ctx.fill()

    ctx.fillStyle = '#FFFFFF'
    ctx.font = 'bold 22px "Hind Siliguri", "Noto Sans Bengali", sans-serif'
    ctx.textAlign = 'center'
    ctx.textBaseline = 'middle'
    ctx.fillText('🔴 ব্রেকিং নিউজ', bX + (bW / 2), bY + (bH / 2))
  }

  // 4. Draw Category Badge
  const catText = currentCategoryName.value
  ctx.font = 'bold 24px "Hind Siliguri", "Noto Sans Bengali", sans-serif'
  const catMetrics = ctx.measureText(catText)
  const catBadgeWidth = catMetrics.width + 42
  const catBadgeHeight = 44
  const catBadgeY = height - 420

  ctx.fillStyle = form.is_breaking ? '#DC2626' : '#2563EB'
  drawRoundedRect(ctx, padX, catBadgeY, catBadgeWidth, catBadgeHeight, 10)
  ctx.fill()

  ctx.fillStyle = '#FFFFFF'
  ctx.textAlign = 'center'
  ctx.textBaseline = 'middle'
  ctx.fillText(catText, padX + (catBadgeWidth / 2), catBadgeY + (catBadgeHeight / 2) + 1)

  // 5. Draw Headline Text (Word-wrapped)
  const headline = form.title || 'শিরোনাম এখানে প্রদর্শিত হবে'
  ctx.fillStyle = studioConfig.textColor || '#FFFFFF'
  ctx.font = `bold ${Number(studioConfig.fontSize) || 48}px "Hind Siliguri", "Noto Sans Bengali", sans-serif`
  ctx.textAlign = 'left'
  ctx.textBaseline = 'top'

  const maxLineWidth = width - (padX * 2)
  const lineHeight = (Number(studioConfig.fontSize) || 48) * 1.35
  const headlineY = height - 350

  wrapAndDrawText(ctx, headline, padX, headlineY, maxLineWidth, lineHeight, 4)

  // 6. Draw Footer Branding & Date
  const footerY = height - 70

  // Divider line
  if (studioConfig.showDate || studioConfig.showWatermark) {
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.2)'
    ctx.lineWidth = 1.5
    ctx.beginPath()
    ctx.moveTo(padX, footerY - 20)
    ctx.lineTo(width - padX, footerY - 20)
    ctx.stroke()
  }

  // Bengali Date
  if (studioConfig.showDate) {
    const dateText = (studioConfig.customDateText && studioConfig.customDateText.trim())
      ? studioConfig.customDateText.trim()
      : `📅 ${bengaliTodayDate.value}`
    ctx.fillStyle = studioConfig.dateColor || '#94A3B8'
    ctx.font = `bold ${Number(studioConfig.dateFontSize) || 22}px "Hind Siliguri", "Noto Sans Bengali", sans-serif`
    ctx.textAlign = 'left'
    ctx.textBaseline = 'top'
    ctx.fillText(dateText, padX, footerY)
  }

  // Watermark Domain
  if (studioConfig.showWatermark) {
    const rawLink = (studioConfig.customWatermarkText && studioConfig.customWatermarkText.trim())
      ? studioConfig.customWatermarkText.trim()
      : (props.portal?.domain || 'www.newsportal.com')
    const domainText = rawLink.startsWith('🌐') ? rawLink : `🌐 ${rawLink}`
    ctx.fillStyle = studioConfig.watermarkColor || '#E2E8F0'
    ctx.font = `bold ${Number(studioConfig.watermarkFontSize) || 22}px "Hind Siliguri", "Noto Sans Bengali", monospace`
    ctx.textAlign = 'right'
    ctx.textBaseline = 'top'
    ctx.fillText(domainText, width - padX, footerY)
  }
}

// Canvas Helpers
const drawRoundedRect = (ctx, x, y, width, height, radius) => {
  ctx.beginPath()
  ctx.moveTo(x + radius, y)
  ctx.lineTo(x + width - radius, y)
  ctx.quadraticCurveTo(x + width, y, x + width, y + radius)
  ctx.lineTo(x + width, y + height - radius)
  ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height)
  ctx.lineTo(x + radius, y + height)
  ctx.quadraticCurveTo(x, y + height, x, y + height - radius)
  ctx.lineTo(x, y + radius)
  ctx.quadraticCurveTo(x, y, x + radius, y)
  ctx.closePath()
}

const wrapAndDrawText = (ctx, text, x, y, maxWidth, lineHeight, maxLines = 4) => {
  const words = text.split(' ')
  let line = ''
  let currentY = y
  let lineCount = 0

  for (let n = 0; n < words.length; n++) {
    const testLine = line + words[n] + ' '
    const metrics = ctx.measureText(testLine)
    const testWidth = metrics.width

    if (testWidth > maxWidth && n > 0) {
      ctx.fillText(line.trim(), x, currentY)
      line = words[n] + ' '
      currentY += lineHeight
      lineCount++
      if (lineCount >= maxLines - 1 && n < words.length - 1) {
        // Truncate remaining
        line += '...'
        break
      }
    } else {
      line = testLine
    }
  }
  ctx.fillText(line.trim(), x, currentY)
}

// Toast message helper
const showToast = (message) => {
  cardSuccessToast.value = message
  if (toastTimeout) clearTimeout(toastTimeout)
  toastTimeout = setTimeout(() => {
    cardSuccessToast.value = ''
  }, 3500)
}

// Card image extraction helper
const getCardImage = (card) => {
  if (!card) return ''
  if (card.image) return card.image
  if (typeof card.featured_image === 'object' && card.featured_image !== null) {
    return card.featured_image.original || card.featured_image.sm || card.featured_image.md || ''
  }
  return typeof card.featured_image === 'string' ? card.featured_image : ''
}

// Auto-save card to localStorage history
const saveCurrentCardToStorage = () => {
  try {
    const canvas = photocardCanvas.value
    let thumbnail = selectedPresetUrl.value || ''
    if (!thumbnail && canvas) {
      try {
        thumbnail = canvas.toDataURL('image/jpeg', 0.5)
      } catch (e) {
        // Tainted canvas fallback
      }
    }

    const newCard = {
      id: 'saved_' + Date.now(),
      title: form.title || 'শিরোনামবিহীন কার্ড',
      category: currentCategoryName.value,
      category_id: form.category_id,
      excerpt: form.excerpt,
      body: form.body,
      is_breaking: form.is_breaking,
      image: thumbnail,
      published_at: bengaliTodayDate.value,
      saved_at: new Date().toLocaleString('bn-BD', { dateStyle: 'medium', timeStyle: 'short' }),
      timestamp: Date.now(),
      studioConfig: JSON.parse(JSON.stringify(studioConfig)),
      isSavedCustom: true,
    }

    // Filter out if duplicate title exists recently
    const filtered = savedCardHistory.value.filter(c => c.title !== newCard.title)
    savedCardHistory.value = [newCard, ...filtered].slice(0, 25)

    localStorage.setItem('photocard_saved_cards_history', JSON.stringify(savedCardHistory.value))
  } catch (err) {
    console.error('Failed to save card history to localStorage:', err)
  }
}

// Manual Save Current Live Card
const saveCurrentCardAsTemplate = () => {
  saveCurrentCardToStorage()
  showToast('বর্তমান কার্ডটি আপনার সেভ করা তালিকায় সংরক্ষণ করা হয়েছে!')
}

// Delete Saved Card
const deleteSavedCard = (id) => {
  if (!confirm('আপনি কি এই সেভ করা কার্ডটি মুছে ফেলতে চান?')) return
  savedCardHistory.value = savedCardHistory.value.filter(c => c.id !== id)
  try {
    localStorage.setItem('photocard_saved_cards_history', JSON.stringify(savedCardHistory.value))
  } catch (e) {
    console.error(e)
  }
  showToast('কার্ডটি তালিকা থেকে মুছে ফেলা হয়েছে!')
}

// Ready-Made Card Styles
const readyStyles = [
  {
    id: 'breaking',
    name: 'ব্রেকিং নিউজ স্পেশাল',
    badge: 'জরুরি ঘটনা',
    description: 'লাল ডার্ক ভাইব্রেট গ্রেডিয়েন্ট ও বোল্ড ব্রেকিং ট্যাগ',
    gradientPreset: 'breaking_red',
    is_breaking: true,
    textColor: '#FFFFFF',
    fontSize: 50,
    dateColor: '#FCA5A5',
    watermarkColor: '#FEE2E2',
    headerColor: '#FFFFFF',
    bgGradientClass: 'from-red-900 via-rose-900 to-black',
    accentColor: 'text-red-400',
    icon: '🔴',
  },
  {
    id: 'cinematic',
    name: 'জাতীয় ও রাজনৈতিক বুলেটিন',
    badge: 'জাতীয় সংবাদ',
    description: 'সিনেমেটিক ডার্ক ব্যাকগ্রাউন্ড ও ক্লাসিক রিড্যাবিলিটি',
    gradientPreset: 'cinematic_dark',
    is_breaking: false,
    textColor: '#FFFFFF',
    fontSize: 48,
    dateColor: '#94A3B8',
    watermarkColor: '#E2E8F0',
    headerColor: '#FFFFFF',
    bgGradientClass: 'from-slate-900 via-gray-900 to-black',
    accentColor: 'text-blue-400',
    icon: '🏛️',
  },
  {
    id: 'international',
    name: 'আন্তর্জাতিক ও ভূরাজনীতি',
    badge: 'বিশ্ব সংবাদ',
    description: 'রয়্যাল নেভি ব্লু ও নজরকাড়া গোল্ডেন ইয়েলো শিরোনাম',
    gradientPreset: 'royal_navy',
    is_breaking: false,
    textColor: '#FACC15',
    fontSize: 48,
    dateColor: '#93C5FD',
    watermarkColor: '#DBEAFE',
    headerColor: '#FFFFFF',
    bgGradientClass: 'from-blue-950 via-indigo-950 to-slate-950',
    accentColor: 'text-yellow-400',
    icon: '🌍',
  },
  {
    id: 'business',
    name: 'বাণিজ্য ও শেয়ারবাজার',
    badge: 'অর্থ ও বাণিজ্য',
    description: 'এমারেল্ড গ্রিন আভা ও পরিচ্ছন্ন প্রফেশনাল লেআউট',
    gradientPreset: 'emerald_green',
    is_breaking: false,
    textColor: '#FFFFFF',
    fontSize: 48,
    dateColor: '#6EE7B7',
    watermarkColor: '#D1FAE5',
    headerColor: '#FFFFFF',
    bgGradientClass: 'from-emerald-950 via-teal-950 to-slate-950',
    accentColor: 'text-emerald-400',
    icon: '📈',
  },
  {
    id: 'sports',
    name: 'খেলাধুলা ও স্পোর্টস লাইভ',
    badge: 'খেলাধুলা',
    description: 'সানসেট অ্যাম্বার এনার্জি গ্রেডিয়েন্ট ও বোল্ড ক্যাপশন',
    gradientPreset: 'sunset_amber',
    is_breaking: false,
    textColor: '#FFFFFF',
    fontSize: 50,
    dateColor: '#FCD34D',
    watermarkColor: '#FEF3C7',
    headerColor: '#FFFFFF',
    bgGradientClass: 'from-amber-950 via-orange-950 to-stone-950',
    accentColor: 'text-amber-400',
    icon: '⚽',
  },
  {
    id: 'entertainment',
    name: 'বিনোদন ও বিশেষ কলাম',
    badge: 'বিনোদন / বিশেষ',
    description: 'পার্পল-রোজ নান্দনিক আভা ও আকর্ষণীয় ফন্ট স্টাইল',
    gradientPreset: 'purple_rose',
    is_breaking: false,
    textColor: '#FDE047',
    fontSize: 46,
    dateColor: '#F472B6',
    watermarkColor: '#FBCFE8',
    headerColor: '#FDF2F8',
    bgGradientClass: 'from-purple-950 via-rose-950 to-slate-950',
    accentColor: 'text-rose-400',
    icon: '🎭',
  },
]

// Apply Ready Style
const applyReadyStyle = (style) => {
  studioConfig.gradientPreset = style.gradientPreset
  studioConfig.textColor = style.textColor
  studioConfig.fontSize = style.fontSize
  studioConfig.dateColor = style.dateColor
  studioConfig.watermarkColor = style.watermarkColor
  studioConfig.headerColor = style.headerColor
  form.is_breaking = style.is_breaking

  renderCanvas()
  isPreviousCardsModalOpen.value = false
  showToast(`"${style.name}" স্টাইলটি সফলভাবে প্রয়োগ করা হয়েছে!`)
}

// Combined Previous Cards (localStorage saved cards + published history)
const combinedPreviousCards = computed(() => {
  const list = []
  const seenTitles = new Set()

  // 1. From savedCardHistory (localStorage)
  if (Array.isArray(savedCardHistory.value)) {
    for (const card of savedCardHistory.value) {
      if (card && card.title) {
        list.push({
          ...card,
          isSavedCustom: true,
          sourceBadge: 'তৈরিকৃত কার্ড',
        })
        seenTitles.add(card.title.trim())
      }
    }
  }

  // 2. From props.history.data (Recent Published News)
  if (props.history && Array.isArray(props.history.data)) {
    for (const item of props.history.data) {
      if (item && item.title && !seenTitles.has(item.title.trim())) {
        list.push({
          id: 'hist_' + item.id,
          title: item.title,
          category: item.category || 'সংবাদ',
          category_id: item.category_id || null,
          excerpt: item.excerpt || '',
          body: item.body || '',
          featured_image: item.featured_image,
          image: getCardImage(item),
          published_at: item.published_at || '',
          isSavedCustom: false,
          sourceBadge: 'পূর্বে প্রকাশিত',
        })
        seenTitles.add(item.title.trim())
      }
    }
  }

  return list
})

// Filtered Previous Cards
const filteredPreviousCards = computed(() => {
  const query = cardSearchQuery.value.trim().toLowerCase()
  if (!query) return combinedPreviousCards.value

  return combinedPreviousCards.value.filter(c => {
    const titleMatch = (c.title || '').toLowerCase().includes(query)
    const catMatch = (c.category || '').toLowerCase().includes(query)
    return titleMatch || catMatch
  })
})

// Filtered Saved Cards
const filteredSavedCards = computed(() => {
  const query = cardSearchQuery.value.trim().toLowerCase()
  if (!query) return savedCardHistory.value

  return savedCardHistory.value.filter(c => {
    const titleMatch = (c.title || '').toLowerCase().includes(query)
    const catMatch = (c.category || '').toLowerCase().includes(query)
    return titleMatch || catMatch
  })
})

// Load Previous Card into Live Studio
const loadPreviousCard = (card) => {
  if (!card) return

  if (card.title) {
    form.title = card.title
  }

  // Match category
  if (card.category_id) {
    form.category_id = card.category_id
  } else if (card.category) {
    const matched = props.categories.find(c =>
      c.name.trim() === card.category.trim() ||
      c.slug === card.category ||
      c.name.toLowerCase() === card.category.toLowerCase()
    )
    if (matched) form.category_id = matched.id
  }

  if (card.excerpt) form.excerpt = card.excerpt
  if (card.body) form.body = card.body
  if (typeof card.is_breaking !== 'undefined') form.is_breaking = !!card.is_breaking

  // Apply custom styling if available from saved card
  if (card.studioConfig) {
    Object.assign(studioConfig, card.studioConfig)
  }

  // Load background image
  const imgUrl = getCardImage(card)
  if (imgUrl) {
    selectedPresetUrl.value = imgUrl
    loadCanvasImage(imgUrl)
  } else {
    renderCanvas()
  }

  isPreviousCardsModalOpen.value = false
  showToast('কার্ডটি সফলভাবে স্টুডিওতে লোড করা হয়েছে!')
}

// Download Photocard
const downloadPhotocard = () => {
  const canvas = photocardCanvas.value
  if (!canvas) return

  // Automatically save current card state to history
  saveCurrentCardToStorage()

  const now = new Date()
  const pad = (n) => String(n).padStart(2, '0')
  const dateStr = `${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}`
  const timeStr = `${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}`
  const filename = `photocard_${dateStr}_${timeStr}.png`

  try {
    const dataUrl = canvas.toDataURL('image/png')
    const a = document.createElement('a')
    a.href = dataUrl
    a.download = filename
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
  } catch (e) {
    // Fallback to toBlob if toDataURL fails (e.g. cross-origin taint)
    canvas.toBlob((blob) => {
      if (!blob) return
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename
      document.body.appendChild(a)
      a.click()
      setTimeout(() => {
        document.body.removeChild(a)
        URL.revokeObjectURL(url)
      }, 1000)
    }, 'image/png')
  }
}

// Preview Modal
const previewImage = (url) => {
  if (url) previewImageUrl.value = url
}

// Submit Dual Publishing Form
const submitPublishForm = () => {
  if (!form.title.trim()) {
    alert('দয়া করে সংবাদের শিরোনাম লিখুন।')
    return
  }
  if (!form.category_id) {
    alert('দয়া করে ক্যাটাগরি নির্বাচন করুন।')
    return
  }

  const canvas = photocardCanvas.value
  if (!canvas) return

  isSubmitting.value = true

  // Grab photocard base64 png from canvas
  form.photocard_image = canvas.toDataURL('image/png')
  saveCurrentCardToStorage()

  router.post(route('dashboard.social-publisher.publish'), form, {
    onSuccess: () => {
      isSubmitting.value = false
      // Reset form title and body
      form.title = ''
      form.excerpt = ''
      form.body = ''
      renderCanvas()
    },
    onError: () => {
      isSubmitting.value = false
    },
  })
}

// Re-share post
const reSharePost = (item) => {
  if (!confirm(`"${item.title}" সংবাদটি কি ফেসবুকে পুনরায় শেয়ার করতে চান?`)) return

  reSharingId.value = item.id
  router.post(route('dashboard.social-publisher.re-share', { post: item.id }), {}, {
    onFinish: () => {
      reSharingId.value = null
    },
  })
}

// Test Facebook Connection
const testFacebookConnection = async () => {
  isTestingConnection.value = true
  testResult.value = null
  try {
    const res = await window.axios.post(route('dashboard.social-publisher.test-facebook'), {
      page_id: fbSettingsForm.page_id,
      access_token: fbSettingsForm.access_token,
    })
    testResult.value = res.data
  } catch (err) {
    testResult.value = {
      success: false,
      message: err.response?.data?.message || err.message,
    }
  } finally {
    isTestingConnection.value = false
  }
}

// Save Facebook Settings
const saveFacebookSettings = () => {
  isSavingSettings.value = true
  router.post(route('dashboard.social-publisher.facebook-settings'), fbSettingsForm, {
    onSuccess: () => {
      isSavingSettings.value = false
      showSettingsModal.value = false
    },
    onError: () => {
      isSavingSettings.value = false
    },
  })
}

onMounted(() => {
  try {
    const saved = localStorage.getItem('photocard_recent_images')
    if (saved) {
      const parsed = JSON.parse(saved)
      if (Array.isArray(parsed)) {
        recentUploads.value = parsed
      }
    }
  } catch (e) {
    console.error('Failed to load recent images from localStorage', e)
  }

  try {
    const savedCards = localStorage.getItem('photocard_saved_cards_history')
    if (savedCards) {
      const parsed = JSON.parse(savedCards)
      if (Array.isArray(parsed)) {
        savedCardHistory.value = parsed
      }
    }
  } catch (e) {
    console.error('Failed to load saved card history from localStorage', e)
  }

  nextTick(() => {
    loadCanvasImage(selectedPresetUrl.value)
  })
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.25s ease-out forwards;
}
</style>
