<template>
  <div class="space-y-8">
    <!-- Breadcrumb & Status Header -->
    <div class="border-b pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
          <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
          <span>/</span>
          <span>সরাসরি সম্প্রচার</span>
        </div>
        <div class="flex items-center gap-2.5">
          <span class="w-3.5 h-3.5 rounded-full bg-red-600 animate-ping"></span>
          <h2 class="text-2xl font-black text-gray-900">
            {{ ytConfig.live_title || liveData?.event_title || 'সরাসরি সম্প্রচার ও লাইভ কাভারেজ' }}
          </h2>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <span class="text-xs bg-red-600 text-white font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider flex items-center gap-1.5 shadow-xs">
          <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
          {{ ytConfig.is_live ? 'YOUTUBE LIVE' : 'LIVE UPDATES' }}
        </span>
        <a
          v-if="ytConfig.channel_url"
          :href="ytConfig.channel_url"
          target="_blank"
          class="text-xs bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-full transition-colors flex items-center gap-1"
        >
          <span>▶</span> ইউটিউব চ্যানেল
        </a>
      </div>
    </div>

    <!-- YouTube Live Player (When Active or configured) -->
    <div v-if="ytLiveId" class="bg-black rounded-3xl overflow-hidden shadow-xl border border-gray-800">
      <div class="relative w-full aspect-video bg-black flex items-center justify-center">
        <iframe
          :src="`https://www.youtube-nocookie.com/embed/${ytLiveId}?autoplay=1&rel=0`"
          title="YouTube Live Stream"
          class="w-full h-full border-0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen
        ></iframe>
      </div>
      <div class="p-4 bg-gray-900 text-white flex items-center justify-between flex-wrap gap-2 text-xs">
        <div class="flex items-center gap-2">
          <span class="px-2 py-0.5 bg-red-600 text-white font-bold rounded text-[10px]">LIVE</span>
          <span class="font-semibold">{{ ytConfig.live_title || 'দৈনিক বাংলা ডিজিটাল লাইভ সম্প্রচার' }}</span>
        </div>
        <span class="text-gray-400">অবিরাম সরাসরি সম্প্রচার</span>
      </div>
    </div>

    <!-- 2-Column Grid: Timeline Updates Left & Recent Videos Right -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Column: Live Timeline Coverage Updates -->
      <div class="lg:col-span-8 space-y-4">
        <div class="flex items-center justify-between border-b pb-2">
          <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
            <span>⏱️</span> মুহূর্তের লাইভ আপডেট
          </h3>
          <span class="text-xs text-gray-400">সর্বশেষ তথ্য</span>
        </div>

        <div class="space-y-4">
          <div
            v-for="(update, idx) in liveData?.updates"
            :key="idx"
            class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-xs relative pl-8 border-l-4 border-l-red-600 space-y-1.5"
          >
            <span class="text-xs font-mono font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded">
              ⏱️ {{ update.time }}
            </span>
            <h4 class="text-base font-bold text-gray-900 pt-1">{{ update.headline }}</h4>
            <p class="text-sm text-gray-700 leading-relaxed">{{ update.body }}</p>
          </div>
        </div>
      </div>

      <!-- Right Column: Recent YouTube Videos Sidebar -->
      <div class="lg:col-span-4 space-y-4">
        <div class="flex items-center justify-between border-b pb-2">
          <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
            <span>🎬</span> সর্বশেষ ভিডিও বুলেটিন
          </h3>
          <a
            v-if="ytConfig.channel_url"
            :href="ytConfig.channel_url"
            target="_blank"
            class="text-xs text-red-600 font-semibold hover:underline"
          >
            চ্যানেল →
          </a>
        </div>

        <div v-if="ytVideos.length === 0" class="bg-white rounded-2xl p-6 text-center text-xs text-gray-400 border">
          বর্তমানে কোনো ভিডিও তালিকা নেই
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="(vid, i) in ytVideos"
            :key="i"
            class="bg-white rounded-2xl overflow-hidden border border-gray-200/80 shadow-xs group hover:border-red-300 transition-colors"
          >
            <a
              :href="vid.video_url"
              target="_blank"
              class="block relative aspect-video bg-gray-100 overflow-hidden"
            >
              <img
                v-if="getYoutubeThumb(vid.video_url)"
                :src="getYoutubeThumb(vid.video_url)"
                :alt="vid.title"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-900 text-white text-3xl">
                ▶
              </div>
              <div class="absolute inset-0 bg-black/25 flex items-center justify-center group-hover:bg-black/10 transition-colors">
                <span class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center text-sm shadow-lg group-hover:scale-110 transition-transform">
                  ▶
                </span>
              </div>
              <span v-if="vid.duration" class="absolute bottom-2 right-2 px-1.5 py-0.5 bg-black/80 text-white font-mono text-[10px] rounded">
                {{ vid.duration }}
              </span>
            </a>
            <div class="p-3">
              <a
                :href="vid.video_url"
                target="_blank"
                class="text-xs font-bold text-gray-900 line-clamp-2 leading-relaxed group-hover:text-red-600 transition-colors"
              >
                {{ vid.title }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

// 1. Live blog timeline data
const { data: liveResponse } = await useFetch(`${config.public.apiBase}/m/LiveBlogCoverage`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'page-live-blog',
  default: () => ({
    success: true,
    event_title: 'চলমান ঘটনা: লাইভ কাভারেজ',
    updates: [
      {
        time: 'এখনই',
        headline: 'লাইভ সম্প্রচার চলমান রয়েছে',
        body: 'সর্বশেষ খবরের জন্য কিছুক্ষণ পর পর এই পেজটি রিফ্রেশ করুন।'
      }
    ]
  }),
})

const liveData = computed(() => liveResponse.value?.success ? liveResponse.value : null)

// 2. YouTube Live & Videos config from CMS
const { data: ytResponse } = await useFetch(`${config.public.apiBase}/youtube-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'page-youtube-live-config',
  default: () => ({ success: true, data: {} }),
})

const ytConfig = computed(() => ytResponse.value?.data || {})
const ytVideos = computed(() => ytConfig.value?.videos || [])

// Extract YouTube ID from standard URL / youtu.be / shorts
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
</script>
