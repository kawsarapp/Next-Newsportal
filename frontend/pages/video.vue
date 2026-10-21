<template>
  <div class="space-y-8">
    <!-- Breadcrumb & Header -->
    <div class="border-b pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
          <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
          <span>/</span>
          <span>ভিডিও ও বুলেটিন</span>
        </div>
        <div class="flex items-center gap-2.5">
          <span class="w-3.5 h-3.5 rounded-full bg-red-600 animate-ping"></span>
          <h2 class="text-2xl font-black text-gray-900">
            {{ ytConfig.live_title || 'ভিডিও বুলেটিন ও মাল্টিমিডিয়া' }}
          </h2>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <NuxtLink
          to="/live"
          class="text-xs bg-red-600 text-white font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider flex items-center gap-1.5 shadow-xs hover:bg-red-700 transition-colors"
        >
          <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
          সরাসরি সম্প্রচার পেজ →
        </NuxtLink>
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

    <!-- Active Video Player -->
    <div class="bg-black rounded-3xl overflow-hidden shadow-xl border border-gray-800">
      <div class="relative w-full aspect-video bg-black flex items-center justify-center">
        <iframe
          v-if="currentVideoId"
          :src="`https://www.youtube-nocookie.com/embed/${currentVideoId}?autoplay=1&rel=0`"
          title="YouTube Video Player"
          class="w-full h-full border-0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen
        ></iframe>
        <div v-else class="text-gray-500 text-sm">
          ভিডিও লোড হচ্ছে...
        </div>
      </div>
      <div class="p-4 sm:p-5 bg-gray-900 text-white flex items-center justify-between flex-wrap gap-3 text-xs">
        <div class="flex items-center gap-2.5">
          <span class="px-2.5 py-0.5 bg-red-600 text-white font-bold rounded text-[10px] uppercase">
            {{ currentVideoIsLive ? 'LIVE STREAM' : 'NOW PLAYING' }}
          </span>
          <span class="font-semibold text-sm text-gray-100">
            {{ currentVideoTitle || ytConfig.live_title || 'দৈনিক বাংলা ডিজিটাল ভিডিও বুলেটিন' }}
          </span>
        </div>
        <div class="flex items-center gap-2 text-gray-400">
          <span>দৈনিক বাংলা ডিজিটাল ডেস্ক</span>
        </div>
      </div>
    </div>

    <!-- Video Bulletins Grid -->
    <div class="space-y-4">
      <div class="flex items-center justify-between border-b pb-2">
        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
          <span>🎬</span> সকল ভিডিও ও বুলেটিন তালিকা
        </h3>
        <span class="text-xs text-gray-400 font-medium">
          {{ displayVideos.length }} টি ভিডিও পাওয়া গেছে
        </span>
      </div>

      <div v-if="displayVideos.length === 0" class="bg-white rounded-2xl p-10 text-center text-xs text-gray-400 border border-gray-100">
        বর্তমানে কোনো ভিডিও তালিকা পাওয়া যায়নি
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="(vid, i) in displayVideos"
          :key="i"
          @click="selectVideo(vid)"
          class="bg-white rounded-2xl overflow-hidden border border-gray-200/80 shadow-xs group hover:border-red-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
        >
          <div class="relative aspect-video bg-gray-100 overflow-hidden">
            <img
              v-if="getYoutubeThumb(vid.video_url || vid.url)"
              :src="getYoutubeThumb(vid.video_url || vid.url)"
              :alt="vid.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              loading="lazy"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gray-900 text-white text-3xl">
              ▶
            </div>
            <div class="absolute inset-0 bg-black/25 flex items-center justify-center group-hover:bg-black/10 transition-colors">
              <span class="w-11 h-11 rounded-full bg-red-600 text-white flex items-center justify-center text-sm shadow-lg group-hover:scale-110 transition-transform">
                ▶
              </span>
            </div>
            <span v-if="vid.duration" class="absolute bottom-2 right-2 px-1.5 py-0.5 bg-black/80 text-white font-mono text-[10px] rounded">
              {{ vid.duration }}
            </span>
          </div>
          <div class="p-3.5 space-y-1.5 flex-1 flex flex-col justify-between">
            <h4 class="text-xs sm:text-sm font-bold text-gray-900 line-clamp-2 leading-relaxed group-hover:text-red-600 transition-colors">
              {{ vid.title }}
            </h4>
            <div class="flex items-center justify-between text-[11px] text-gray-400 pt-1">
              <span>দৈনিক বাংলা ডিজিটাল</span>
              <span class="text-red-600 font-semibold group-hover:underline">দেখুন ▶</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  key: route => route.fullPath,
})

const config = useRuntimeConfig()

// 1. Fetch YouTube section config
const { data: ytConfigResponse } = useFetch(`${config.public.apiBase}/youtube-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'video-page-yt-config',
  lazy: true,
  default: () => ({ success: true, data: {} }),
})

const ytConfig = computed(() => ytConfigResponse.value?.data || {})
const ytLiveId = computed(() => {
  return ytConfig.value.live_video_id || extractYoutubeId(ytConfig.value.live_video_url) || ''
})
const ytVideos = computed(() => ytConfig.value.videos || [])

function extractYoutubeId(url) {
  if (!url) return null
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

// Default sample videos if none returned
const defaultVideos = [
  {
    title: 'দৈনিক বাংলা বিশেষ বুলেটিন: আজকের গুরুত্বপূর্ণ শীর্ষ খবর',
    video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    duration: '০৫:৪০'
  },
  {
    title: 'রাজনীতি ও নির্বাচন ভাবনা: সমকালীন বিশ্লেষণ',
    video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    duration: '০৮:১২'
  },
  {
    title: 'বিশ্ব অর্থনীতির গতিপ্রকৃতি ও প্রযুক্তি বিপ্লব',
    video_url: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    duration: '০৬:২৫'
  }
]

const displayVideos = computed(() => {
  return ytVideos.value && ytVideos.value.length > 0 ? ytVideos.value : defaultVideos
})

const currentVideoId = ref('')
const currentVideoTitle = ref('')
const currentVideoIsLive = ref(false)

const getYoutubeId = (url) => {
  if (!url) return null
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

const getYoutubeThumb = (url) => {
  const id = getYoutubeId(url)
  return id ? `https://img.youtube.com/vi/${id}/mqdefault.jpg` : null
}

const selectVideo = (vid) => {
  const id = getYoutubeId(vid.video_url || vid.url)
  if (id) {
    currentVideoId.value = id
    currentVideoTitle.value = vid.title
    currentVideoIsLive.value = false
    if (process.client) {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }
}

// Initial active video
watchEffect(() => {
  if (!currentVideoId.value) {
    if (ytLiveId.value) {
      currentVideoId.value = ytLiveId.value
      currentVideoTitle.value = ytConfig.value.live_title || 'সরাসরি সম্প্রচার'
      currentVideoIsLive.value = true
    } else if (displayVideos.value.length > 0) {
      const first = displayVideos.value[0]
      const id = getYoutubeId(first.video_url || first.url)
      if (id) {
        currentVideoId.value = id
        currentVideoTitle.value = first.title
      }
    }
  }
})

useHead({
  title: 'ভিডিও ও বুলেটিন — দৈনিক বাংলা',
  meta: [
    { name: 'description', content: 'দৈনিক বাংলা অনলাইন ভিডিও বুলেটিন ও বিশেষ সরাসরি সংবাদ প্রতিবেদন।' }
  ]
})
</script>
