<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-100 animate-in fade-in zoom-in-95 duration-150">
      <!-- Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-gray-900 to-gray-800 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">📹</span>
          <div>
            <h3 class="font-bold text-base">সোশ্যাল মিডিয়া ও ভিডিও এম্বেড</h3>
            <p class="text-xs text-gray-300">ইউটিউব, ফেসবুক, টুইটার/X বা যেকোনো ভিডিও সংবাদে যুক্ত করুন</p>
          </div>
        </div>
        <button
          type="button"
          @click="close"
          class="text-gray-400 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors"
        >
          ✕
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex border-b border-gray-200 bg-gray-50 px-6 pt-3 gap-2 overflow-x-auto text-sm">
        <button
          type="button"
          @click="activeTab = 'youtube'"
          :class="[
            'px-4 py-2.5 font-medium rounded-t-lg transition-all flex items-center gap-2 border-b-2',
            activeTab === 'youtube'
              ? 'border-red-600 text-red-600 bg-white shadow-xs font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          ]"
        >
          <span class="text-red-600">🔴</span> YouTube
        </button>
        <button
          type="button"
          @click="activeTab = 'facebook'"
          :class="[
            'px-4 py-2.5 font-medium rounded-t-lg transition-all flex items-center gap-2 border-b-2',
            activeTab === 'facebook'
              ? 'border-blue-600 text-blue-600 bg-white shadow-xs font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          ]"
        >
          <span class="text-blue-600">🔵</span> Facebook
        </button>
        <button
          type="button"
          @click="activeTab = 'twitter'"
          :class="[
            'px-4 py-2.5 font-medium rounded-t-lg transition-all flex items-center gap-2 border-b-2',
            activeTab === 'twitter'
              ? 'border-black text-black bg-white shadow-xs font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          ]"
        >
          <span class="font-black">𝕏</span> Twitter / X
        </button>
        <button
          type="button"
          @click="activeTab = 'custom'"
          :class="[
            'px-4 py-2.5 font-medium rounded-t-lg transition-all flex items-center gap-2 border-b-2',
            activeTab === 'custom'
              ? 'border-purple-600 text-purple-600 bg-white shadow-xs font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          ]"
        >
          <span>🌐</span> কাস্টম / iframe
        </button>
      </div>

      <!-- Tab Contents -->
      <div class="p-6 space-y-4">
        <!-- YouTube Tab -->
        <div v-if="activeTab === 'youtube'" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              ইউটিউব ভিডিও বা শর্টস-এর লিঙ্ক (URL):
            </label>
            <input
              v-model="youtubeUrl"
              type="url"
              placeholder="https://www.youtube.com/watch?v=... অথবা https://youtu.be/..."
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
            />
            <p class="text-xs text-gray-500 mt-1">
              সমর্থিত: সাধারণ ভিডিও, লাইভ স্ট্রিম, শর্টস লিঙ্ক (যেমন: https://youtu.be/xxx বা https://youtube.com/shorts/xxx)
            </p>
          </div>

          <!-- YouTube Live Preview -->
          <div v-if="parsedYoutubeEmbedUrl" class="rounded-xl overflow-hidden border border-gray-200 bg-black aspect-video max-h-64 mx-auto">
            <iframe
              :src="parsedYoutubeEmbedUrl"
              class="w-full h-full"
              frameborder="0"
              allowfullscreen
            ></iframe>
          </div>
        </div>

        <!-- Facebook Tab -->
        <div v-if="activeTab === 'facebook'" class="space-y-4">
          <div class="flex items-center gap-4 text-xs font-medium text-gray-700 pb-2 border-b border-gray-100">
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input type="radio" v-model="facebookMode" value="url" class="text-blue-600 focus:ring-blue-500" />
              <span>লিঙ্ক (URL) দিয়ে</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer">
              <input type="radio" v-model="facebookMode" value="code" class="text-blue-600 focus:ring-blue-500" />
              <span>এমবেড কোড (Iframe Code) দিয়ে</span>
            </label>
          </div>

          <div v-if="facebookMode === 'url'">
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              ফেসবুক পোস্ট বা ভিডিওর লিঙ্ক (URL):
            </label>
            <input
              v-model="facebookUrl"
              type="url"
              placeholder="https://www.facebook.com/.../posts/... বা https://www.facebook.com/watch?v=..."
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
            />
            <div class="flex items-center gap-4 mt-2">
              <label class="flex items-center gap-1.5 text-xs text-gray-600 cursor-pointer">
                <input type="checkbox" v-model="facebookIsVideo" class="rounded text-blue-600 focus:ring-blue-500" />
                <span>এটি একটি ফেসবুক ভিডিও (Video / Reel)</span>
              </label>
            </div>
            <p class="text-xs text-gray-500 mt-1">
              ফেসবুকের যেকোনো পাবলিক পোস্ট, ফটো অথবা ভিডিওর লিঙ্ক পেস্ট করুন।
            </p>
          </div>

          <div v-else>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              ফেসবুক থেকে কপি করা এম্বেড কোড (iframe):
            </label>
            <textarea
              v-model="facebookCode"
              rows="4"
              placeholder='<iframe src="https://www.facebook.com/plugins/post.php?..." width="500" height="600" ...></iframe>'
              class="w-full px-3.5 py-2.5 text-xs font-mono border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
              ফেসবুক পোস্টের ডান পাশের থ্রি-ডট (•••) থেকে "Embed" ক্লিক করে কোডটি কপি করে এখানে পেস্ট করুন।
            </p>
          </div>

          <!-- Facebook Preview -->
          <div v-if="parsedFacebookEmbedUrl" class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50 max-h-72 flex justify-center">
            <iframe
              :src="parsedFacebookEmbedUrl"
              class="w-full max-w-[500px] h-72 border-0"
              scrolling="no"
              frameborder="0"
              allowfullscreen="true"
            ></iframe>
          </div>
        </div>

        <!-- Twitter / X Tab -->
        <div v-if="activeTab === 'twitter'" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              টুইটার / X পোস্টের লিঙ্ক (URL):
            </label>
            <input
              v-model="twitterUrl"
              type="url"
              placeholder="https://x.com/username/status/1234567890 বা https://twitter.com/..."
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-black focus:border-black outline-none transition-all"
            />
            <p class="text-xs text-gray-500 mt-1">
              যেকোনো টুইট / X পোস্টের শেয়ার লিঙ্ক কপি করে দিন।
            </p>
          </div>

          <!-- Twitter Preview -->
          <div v-if="parsedTwitterEmbedUrl" class="rounded-xl overflow-hidden border border-gray-200 bg-white max-h-72 flex justify-center p-2">
            <iframe
              :src="parsedTwitterEmbedUrl"
              class="w-full max-w-[550px] h-64 border-0 rounded-lg"
              frameborder="0"
              scrolling="no"
            ></iframe>
          </div>
        </div>

        <!-- Custom / Iframe Tab -->
        <div v-if="activeTab === 'custom'" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              যেকোনো প্ল্যাটফর্মের এম্বেড কোড বা iframe লিঙ্ক:
            </label>
            <textarea
              v-model="customEmbedCode"
              rows="4"
              placeholder='<iframe src="https://..." width="100%" height="450" ...></iframe> অথবা সরাসরি Embed URL'
              class="w-full px-3.5 py-2.5 text-xs font-mono border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all"
            ></textarea>
            <p class="text-xs text-gray-500 mt-1">
              Instagram, Vimeo, DailyMotion, Google Maps, Spotify বা যেকোনো মিডিয়ার &lt;iframe&gt; কোড পেস্ট করতে পারেন।
            </p>
          </div>

          <!-- Custom Preview -->
          <div v-if="parsedCustomEmbedUrl" class="rounded-xl overflow-hidden border border-gray-200 bg-gray-50 max-h-72 flex justify-center">
            <iframe
              :src="parsedCustomEmbedUrl"
              class="w-full h-64 border-0"
              frameborder="0"
              allowfullscreen
            ></iframe>
          </div>
        </div>

        <!-- Error feedback -->
        <div v-if="errorMessage" class="p-3 bg-red-50 text-red-600 rounded-xl text-xs font-medium flex items-center gap-2">
          <span>⚠️</span> {{ errorMessage }}
        </div>
      </div>

      <!-- Footer Buttons -->
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
        <button
          type="button"
          @click="close"
          class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 rounded-xl transition-colors"
        >
          বাতিল
        </button>
        <button
          type="button"
          @click="handleInsert"
          class="px-5 py-2 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow-xs transition-all flex items-center gap-2"
        >
          <span>✓</span> এম্বেড যোগ করুন
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  }
})

const emit = defineEmits(['close', 'insert'])

const activeTab = ref('youtube')
const errorMessage = ref('')

// YouTube
const youtubeUrl = ref('')

// Facebook
const facebookMode = ref('url')
const facebookUrl = ref('')
const facebookCode = ref('')
const facebookIsVideo = ref(false)

// Twitter
const twitterUrl = ref('')

// Custom
const customEmbedCode = ref('')

// Watch url to auto-detect video in Facebook
watch(facebookUrl, (val) => {
  if (val && (val.includes('/videos/') || val.includes('/watch') || val.includes('fb.watch') || val.includes('/reel/'))) {
    facebookIsVideo.value = true
  }
})

// YouTube Parser
const extractYoutubeId = (url) => {
  if (!url) return null
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=|shorts\/)([^#&?]*).*/
  const match = url.trim().match(regExp)
  return (match && match[2].length === 11) ? match[2] : null
}

const parsedYoutubeEmbedUrl = computed(() => {
  const videoId = extractYoutubeId(youtubeUrl.value)
  return videoId ? `https://www.youtube.com/embed/${videoId}` : null
})

// Facebook Parser
const parsedFacebookEmbedUrl = computed(() => {
  if (facebookMode.value === 'code') {
    if (!facebookCode.value) return null
    const srcMatch = facebookCode.value.match(/src=["'](.*?)["']/)
    return srcMatch ? srcMatch[1] : null
  } else {
    if (!facebookUrl.value) return null
    const trimmed = facebookUrl.value.trim()
    const encoded = encodeURIComponent(trimmed)
    if (facebookIsVideo.value) {
      return `https://www.facebook.com/plugins/video.php?href=${encoded}&show_text=false&width=500`
    } else {
      return `https://www.facebook.com/plugins/post.php?href=${encoded}&show_text=true&width=500`
    }
  }
})

// Twitter Parser
const extractTweetId = (url) => {
  if (!url) return null
  const match = url.trim().match(/status\/(\d+)/)
  return match ? match[1] : null
}

const parsedTwitterEmbedUrl = computed(() => {
  const tweetId = extractTweetId(twitterUrl.value)
  return tweetId ? `https://platform.twitter.com/embed/Tweet.html?id=${tweetId}` : null
})

// Custom / Iframe Parser
const parsedCustomEmbedUrl = computed(() => {
  if (!customEmbedCode.value) return null
  const code = customEmbedCode.value.trim()
  if (code.startsWith('http://') || code.startsWith('https://')) {
    return code
  }
  const srcMatch = code.match(/src=["'](.*?)["']/)
  return srcMatch ? srcMatch[1] : null
})

const close = () => {
  errorMessage.value = ''
  emit('close')
}

const handleInsert = () => {
  errorMessage.value = ''

  if (activeTab.value === 'youtube') {
    const embedUrl = parsedYoutubeEmbedUrl.value
    if (!embedUrl) {
      errorMessage.value = 'সঠিক ইউটিউব ভিডিও লিঙ্ক (URL) দিন।'
      return
    }
    emit('insert', {
      src: embedUrl,
      class: 'w-full aspect-video rounded-xl shadow-sm my-4',
      'data-embed-type': 'youtube',
      height: '450',
    })
    close()
  } else if (activeTab.value === 'facebook') {
    const embedUrl = parsedFacebookEmbedUrl.value
    if (!embedUrl) {
      errorMessage.value = 'সঠিক ফেসবুক লিঙ্ক অথবা সম্পূর্ণ iframe কোড দিন।'
      return
    }
    emit('insert', {
      src: embedUrl,
      class: 'w-full max-w-[550px] mx-auto rounded-xl shadow-sm my-4 border border-gray-200 block',
      height: facebookIsVideo.value ? '360' : '580',
      'data-embed-type': 'facebook',
      style: 'border:none;overflow:hidden',
      scrolling: 'no',
    })
    close()
  } else if (activeTab.value === 'twitter') {
    const embedUrl = parsedTwitterEmbedUrl.value
    if (!embedUrl) {
      errorMessage.value = 'সঠিক টুইটার / X পোস্ট লিঙ্ক দিন (যেমন: https://x.com/username/status/12345)'
      return
    }
    emit('insert', {
      src: embedUrl,
      class: 'w-full max-w-[550px] mx-auto rounded-xl shadow-sm my-4 border border-gray-100 block',
      height: '560',
      'data-embed-type': 'twitter',
      scrolling: 'no',
      style: 'border:none;overflow:hidden',
    })
    close()
  } else if (activeTab.value === 'custom') {
    const embedUrl = parsedCustomEmbedUrl.value
    if (!embedUrl) {
      errorMessage.value = 'সঠিক iframe কোড অথবা ওয়েব লিঙ্ক দিন।'
      return
    }
    emit('insert', {
      src: embedUrl,
      class: 'w-full rounded-xl shadow-sm my-4 border border-gray-200',
      height: '450',
      'data-embed-type': 'custom',
    })
    close()
  }
}
</script>
