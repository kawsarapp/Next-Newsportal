<template>
  <section v-if="ytConfig?.show_on_home !== false && (ytLiveId || (ytVideos && ytVideos.length > 0))" class="space-y-6 pt-4">
    <div class="flex items-center justify-between border-b border-gray-200 pb-2">
      <h3 class="text-xl font-bold text-gray-900 border-l-4 border-red-600 pl-3 flex items-center gap-2">
        <span class="w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
        {{ ytConfig?.live_title || 'ভিডিও ও লাইভ বুলেটিন' }}
      </h3>
      <div class="flex items-center gap-3">
        <NuxtLink to="/live" class="text-xs text-red-600 font-semibold hover:underline">
          সরাসরি সম্প্রচার পেজ →
        </NuxtLink>
        <a
          v-if="ytConfig?.channel_url"
          :href="ytConfig.channel_url"
          target="_blank"
          class="text-xs bg-red-600 text-white font-bold px-3 py-1 rounded-full hover:bg-red-700 transition-colors flex items-center gap-1 shadow-xs"
        >
          <span>▶</span> সাবস্ক্রাইব
        </a>
      </div>
    </div>

    <!-- If currently Live Streaming -->
    <div v-if="ytConfig?.is_live && ytLiveId" class="grid grid-cols-1 lg:grid-cols-12 gap-6 bg-black rounded-3xl overflow-hidden p-4 sm:p-6 text-white shadow-xl">
      <div class="lg:col-span-8 aspect-video rounded-2xl overflow-hidden bg-gray-900">
        <iframe
          :src="`https://www.youtube-nocookie.com/embed/${ytLiveId}?autoplay=0&rel=0`"
          title="Live Stream"
          class="w-full h-full border-0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
      </div>
      <div class="lg:col-span-4 flex flex-col justify-between py-2 space-y-4">
        <div class="space-y-3">
          <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-600 text-white font-bold rounded-full text-xs uppercase tracking-wider">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
            সরাসরি সম্প্রচার
          </span>
          <h4 class="text-lg sm:text-xl font-bold leading-snug">
            {{ ytConfig?.live_title || 'সরাসরি দেখুন বিশেষ সংবাদ ও বিশ্লেষণ' }}
          </h4>
          <p class="text-xs text-gray-400 leading-relaxed">
            দেশের শীর্ষ সংবাদ, ব্রেকিং আপডেট ও রাজনৈতিক বিশ্লেষণ সরাসরি দেখতে চোখ রাখুন আমাদের অফিশিয়াল চ্যানেলে।
          </p>
        </div>
        <a
          :href="ytConfig?.channel_url || 'https://youtube.com'"
          target="_blank"
          class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl text-center transition-colors shadow-sm"
        >
          ইউটিউবে সরাসরি দেখুন
        </a>
      </div>
    </div>

    <!-- Video Gallery Grid (Latest Videos) -->
    <div v-if="ytVideos && ytVideos.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
      <div
        v-for="(vid, i) in ytVideos"
        :key="i"
        class="bg-white rounded-2xl overflow-hidden border border-gray-200/80 shadow-xs hover:border-red-300 transition-colors group flex flex-col justify-between"
      >
        <a
          :href="vid.video_url"
          target="_blank"
          class="block relative aspect-video bg-gray-900 overflow-hidden"
        >
          <img
            v-if="getYoutubeThumb(vid.video_url)"
            :src="getYoutubeThumb(vid.video_url)"
            :alt="vid.title"
            loading="lazy"
            decoding="async"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          />
          <div v-else class="w-full h-full flex items-center justify-center text-white text-3xl">
            ▶
          </div>
          <div class="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/10 transition-colors">
            <span class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center text-sm shadow-md group-hover:scale-110 transition-transform">
              ▶
            </span>
          </div>
          <span v-if="vid.duration" class="absolute bottom-2 right-2 px-1.5 py-0.5 bg-black/80 text-white font-mono text-[10px] rounded">
            {{ vid.duration }}
          </span>
        </a>
        <div class="p-3.5 space-y-1">
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
  </section>
</template>

<script setup>
const props = defineProps({
  ytConfig: {
    type: Object,
    default: () => ({}),
  },
  ytLiveId: {
    type: String,
    default: null,
  },
  ytVideos: {
    type: Array,
    default: () => [],
  },
})

function extractYoutubeId(url) {
  if (!url) return null
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

function getYoutubeThumb(url) {
  const id = extractYoutubeId(url)
  return id ? `https://img.youtube.com/vi/${id}/mqdefault.jpg` : null
}
</script>
