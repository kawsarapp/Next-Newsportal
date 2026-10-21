<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden border border-gray-100 animate-in fade-in zoom-in-95 duration-150">
      <!-- Header -->
      <div class="px-6 py-4 bg-gradient-to-r from-gray-900 to-gray-800 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="text-2xl">🔗</span>
          <div>
            <h3 class="font-bold text-base">লিঙ্ক যুক্ত করুন (Insert Link)</h3>
            <p class="text-xs text-gray-300">সংবাদের অভ্যন্তরীণ লিঙ্ক বা যেকোনো ওয়েব লিঙ্ক যুক্ত করুন</p>
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
          @click="activeTab = 'internal'"
          :class="[
            'px-4 py-2.5 font-medium rounded-t-lg transition-all flex items-center gap-2 border-b-2',
            activeTab === 'internal'
              ? 'border-red-600 text-red-600 bg-white shadow-xs font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          ]"
        >
          <span>📰</span> সংবাদের লিঙ্ক বাছুন
        </button>
        <button
          type="button"
          @click="activeTab = 'external'"
          :class="[
            'px-4 py-2.5 font-medium rounded-t-lg transition-all flex items-center gap-2 border-b-2',
            activeTab === 'external'
              ? 'border-red-600 text-red-600 bg-white shadow-xs font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-100'
          ]"
        >
          <span>🌐</span> কাস্টম ওয়েব লিঙ্ক
        </button>
      </div>

      <!-- Tab Contents -->
      <div class="p-6">
        <!-- Tab 1: Internal News Search -->
        <div v-if="activeTab === 'internal'" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              সংবাদ খুঁজুন:
            </label>
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="শিরোনাম বা কীওয়ার্ড লিখুন..."
                class="w-full pl-9 pr-8 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
                autofocus
              />
              <span class="absolute left-3 top-3 text-gray-400 text-sm">🔍</span>
              <button
                v-if="searchQuery"
                type="button"
                @click="searchQuery = ''"
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-sm"
              >
                ✕
              </button>
            </div>
            <p class="text-[11px] text-gray-400 mt-1">
              সংবাদের শিরোনামে ক্লিক করলেই আর্টিকেলে লিংকটি তৈরি হয়ে যাবে।
            </p>
          </div>

          <!-- Selected Text Notice -->
          <div v-if="selectedText" class="px-3 py-2 bg-amber-50 border border-amber-200/80 rounded-xl text-xs text-amber-800 flex items-center gap-2">
            <span>✏️</span>
            <span>নির্বাচিত লেখা: <strong class="font-bold underline">{{ selectedText }}</strong> (এর সাথে লিংক যুক্ত হবে)</span>
          </div>

          <!-- News Results List -->
          <div class="border border-gray-200 rounded-xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-gray-100 bg-gray-50/50">
            <div v-if="isLoading" class="p-8 text-center text-gray-500 space-y-2">
              <span class="inline-block w-5 h-5 border-2 border-red-500/30 border-t-red-600 rounded-full animate-spin"></span>
              <p class="text-xs">সংবাদ খোঁজা হচ্ছে...</p>
            </div>

            <div v-else-if="posts.length === 0" class="p-8 text-center text-gray-400 text-xs">
              কোনো সংবাদ পাওয়া যায়নি। অন্য কিছু লিখে খুঁজুন।
            </div>

            <div
              v-else
              v-for="post in posts"
              :key="post.id"
              @click="selectPost(post)"
              class="p-3 bg-white hover:bg-red-50/50 cursor-pointer transition-colors group flex items-start justify-between gap-3"
            >
              <div class="space-y-1 min-w-0 flex-1">
                <h4 class="text-xs sm:text-sm font-semibold text-gray-800 group-hover:text-red-600 transition-colors line-clamp-2 leading-snug">
                  {{ post.title }}
                </h4>
                <div class="flex items-center gap-2 text-[11px] text-gray-400">
                  <span v-if="post.category?.name" class="bg-gray-100 group-hover:bg-red-100 text-gray-600 group-hover:text-red-700 px-2 py-0.5 rounded-md font-medium text-[10px]">
                    {{ post.category.name }}
                  </span>
                  <span>·</span>
                  <span>{{ formatDate(post.published_at) }}</span>
                  <span class="text-gray-300">#{{ post.slug }}</span>
                </div>
              </div>
              <button
                type="button"
                class="shrink-0 px-3 py-1.5 bg-red-50 group-hover:bg-red-600 text-red-600 group-hover:text-white rounded-lg text-xs font-semibold transition-colors flex items-center gap-1"
              >
                <span>যুক্ত করুন</span>
                <span>→</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Tab 2: Custom External URL -->
        <div v-else-if="activeTab === 'external'" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              লিঙ্কের লেখা (Anchor Text):
            </label>
            <input
              v-model="customText"
              type="text"
              placeholder="যেমন: এখানে ক্লিক করুন বা বিস্তারিত দেখুন"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">
              ওয়েব লিঙ্ক (URL):
            </label>
            <input
              v-model="customUrl"
              type="text"
              placeholder="https://example.com বা facebook.com"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all"
              @keydown.enter="insertCustomLink"
            />
            <p class="text-[11px] text-gray-400 mt-1">
              প্রোটোকল না দিলে স্বয়ংক্রিয়ভাবে https:// যুক্ত হবে।
            </p>
          </div>

          <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
              <input
                v-model="openInNewTab"
                type="checkbox"
                class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500"
              />
              <span class="text-xs font-medium text-gray-700">নতুন ট্যাবে খুলুন (Open in new tab)</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
        <div>
          <button
            v-if="isLinked"
            type="button"
            @click="handleUnlink"
            class="px-3.5 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors flex items-center gap-1.5"
            title="বর্তমান লিঙ্কটি মুছে ফেলুন"
          >
            <span>🗑️</span>
            <span>লিঙ্ক মুছে ফেলুন</span>
          </button>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors"
          >
            বাতিল
          </button>

          <button
            v-if="activeTab === 'external'"
            type="button"
            @click="insertCustomLink"
            :disabled="!customUrl.trim()"
            class="px-5 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:cursor-not-allowed rounded-xl transition-colors shadow-sm"
          >
            লিঙ্ক যুক্ত করুন
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  selectedText: {
    type: String,
    default: '',
  },
  initialUrl: {
    type: String,
    default: '',
  },
  isLinked: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['close', 'insert', 'unlink'])

const activeTab = ref('internal')
const searchQuery = ref('')
const posts = ref([])
const isLoading = ref(false)

const customText = ref('')
const customUrl = ref('')
const openInNewTab = ref(true)

let searchTimeout = null

const close = () => {
  emit('close')
}

const handleUnlink = () => {
  emit('unlink')
  close()
}

// Format date helper
const formatDate = (dateStr) => {
  if (!dateStr) return ''
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('bn-BD', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch {
    return dateStr
  }
}

// Fetch posts from API
const fetchPosts = async (query = '') => {
  isLoading.value = true
  try {
    const endpoint = query.trim()
      ? `/api/v1/posts?search=${encodeURIComponent(query.trim())}&per_page=12`
      : `/api/v1/posts?per_page=10`

    const response = await fetch(endpoint, {
      headers: {
        Accept: 'application/json',
      },
    })
    const json = await response.json()
    posts.value = json.data || []
  } catch (err) {
    console.error('Failed to search posts:', err)
    posts.value = []
  } finally {
    isLoading.value = false
  }
}

// Watch search query with debounce
watch(searchQuery, (newVal) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchPosts(newVal)
  }, 300)
})

// Initialize fields when modal opens
watch(() => props.show, (newVal) => {
  if (newVal) {
    customText.value = props.selectedText || ''
    customUrl.value = props.initialUrl || ''
    openInNewTab.value = !props.initialUrl || !props.initialUrl.startsWith('/news/')
    searchQuery.value = ''
    fetchPosts('')

    // Default to internal tab if no existing URL or internal URL, else external
    if (props.initialUrl && !props.initialUrl.startsWith('/news/')) {
      activeTab.value = 'external'
    } else {
      activeTab.value = 'internal'
    }
  }
})

// Click on internal news
const selectPost = (post) => {
  const url = `/news/${post.slug}`
  const text = props.selectedText || post.title
  emit('insert', {
    url,
    text,
    target: null, // internal links open within the site
    isInternal: true,
  })
  close()
}

// Click on custom external link
const insertCustomLink = () => {
  let url = customUrl.value.trim()
  if (!url) return

  // Automatically prepend https:// if missing protocol and not a relative internal route
  if (!url.startsWith('http://') && !url.startsWith('https://') && !url.startsWith('/') && !url.startsWith('mailto:') && !url.startsWith('tel:')) {
    url = `https://${url}`
  }

  const text = customText.value.trim() || props.selectedText || url
  emit('insert', {
    url,
    text,
    target: openInNewTab.value ? '_blank' : null,
    isInternal: url.startsWith('/'),
  })
  close()
}
</script>
