<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
      <!-- Modal Header -->
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white shrink-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg font-bold">
            🗂️
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900">{{ title }}</h3>
            <p class="text-xs text-gray-400">পূর্বে আপলোড করা যেকোনো ছবি পুনরায় ব্যবহার করুন</p>
          </div>
        </div>

        <button
          type="button"
          @click="close"
          class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
        >
          ✕
        </button>
      </div>

      <!-- Action Toolbar (Search + Direct Upload) -->
      <div class="p-4 bg-gray-50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3 shrink-0">
        <div class="relative flex-1 min-w-[200px] max-w-md">
          <span class="absolute left-3 top-2.5 text-gray-400 text-sm">🔍</span>
          <input
            v-model="search"
            type="text"
            placeholder="ফাইলের নাম দিয়ে অনুসন্ধান..."
            class="w-full text-xs pl-8 pr-3 py-2 bg-white border border-gray-200 rounded-xl focus:border-red-500 outline-none"
          />
        </div>

        <div class="flex items-center gap-2">
          <!-- Refresh button -->
          <button
            type="button"
            @click="fetchMedia"
            :disabled="loading"
            class="p-2 border border-gray-200 hover:bg-white bg-gray-50 text-gray-600 rounded-xl text-xs transition-colors"
            title="রিফ্রেশ"
          >
            <span :class="['inline-block', { 'animate-spin': loading }]">🔄</span>
          </button>

          <!-- Modal Direct Upload Button -->
          <label class="px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 cursor-pointer shadow-xs transition-colors">
            <span v-if="uploading" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span v-else>📤</span>
            <span>নতুন আপলোড</span>
            <input
              type="file"
              :multiple="multiple"
              accept="image/*"
              @change="handleUpload"
              :disabled="uploading"
              class="hidden"
            />
          </label>
        </div>
      </div>

      <!-- Media Items Grid -->
      <div class="flex-1 p-6 overflow-y-auto min-h-0 bg-gray-50/50">
        <!-- Loading State -->
        <div v-if="loading && mediaList.length === 0" class="h-full flex flex-col items-center justify-center text-gray-400 gap-2">
          <span class="w-8 h-8 border-3 border-red-500/20 border-t-red-600 rounded-full animate-spin"></span>
          <p class="text-xs">ছবি লোড হচ্ছে...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredMedia.length === 0" class="h-full flex flex-col items-center justify-center text-center p-8 text-gray-400">
          <span class="text-4xl mb-2">🖼️</span>
          <p class="text-sm font-semibold text-gray-700">কোনো ছবি পাওয়া যায়নি</p>
          <p class="text-xs text-gray-400 mt-1">নতুন ছবি আপলোড করুন</p>
        </div>

        <!-- Image Gallery Grid -->
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
          <div
            v-for="item in filteredMedia"
            :key="item.id"
            @click="toggleSelect(item)"
            :class="[
              'group relative rounded-xl overflow-hidden border-2 bg-white cursor-pointer transition-all flex flex-col shadow-2xs hover:shadow-md',
              isSelected(item) ? 'border-red-600 ring-2 ring-red-100 shadow-md scale-[0.98]' : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <!-- Checkmark badge -->
            <div
              v-if="isSelected(item)"
              class="absolute top-2 right-2 z-10 w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold shadow-md"
            >
              ✓
            </div>

            <!-- Thumbnail -->
            <div class="h-28 bg-gray-100 overflow-hidden relative">
              <img :src="item.url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy" />
            </div>

            <!-- Meta details -->
            <div class="p-2 text-[11px] bg-white border-t border-gray-100">
              <p class="font-medium text-gray-800 truncate" :title="item.name">{{ item.name }}</p>
              <div class="flex items-center justify-between text-gray-400 mt-0.5 text-[10px]">
                <span>{{ item.size }}</span>
                <span>{{ item.date }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-6 py-3.5 border-t border-gray-100 bg-white flex items-center justify-between shrink-0">
        <div class="text-xs text-gray-500">
          <span v-if="selectedItems.length > 0" class="font-semibold text-red-600">
            {{ selectedItems.length }} টি ছবি নির্বাচন করা হয়েছে
          </span>
          <span v-else>
            যেকোনো ছবি নির্বাচন করতে ক্লিক করুন
          </span>
        </div>

        <div class="flex items-center gap-2.5">
          <button
            type="button"
            @click="close"
            class="px-4 py-2 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-xs font-medium transition-colors"
          >
            বাতিল
          </button>

          <button
            type="button"
            @click="confirmSelection"
            :disabled="selectedItems.length === 0"
            class="px-5 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-40 disabled:hover:bg-red-600 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors flex items-center gap-1.5"
          >
            <span>যুক্ত করুন</span>
            <span v-if="selectedItems.length > 0">({{ selectedItems.length }})</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'মিডিয়া লাইব্রেরি থেকে ছবি নির্বাচন',
  }
})

const emit = defineEmits(['close', 'select'])

const mediaList = ref([])
const loading = ref(false)
const uploading = ref(false)
const search = ref('')
const selectedItems = ref([])

const filteredMedia = computed(() => {
  if (!search.value.trim()) return mediaList.value
  const q = search.value.toLowerCase()
  return mediaList.value.filter(m => m.name.toLowerCase().includes(q))
})

const isSelected = (item) => {
  return selectedItems.value.some(i => i.id === item.id || i.url === item.url)
}

const toggleSelect = (item) => {
  if (props.multiple) {
    const idx = selectedItems.value.findIndex(i => i.id === item.id || i.url === item.url)
    if (idx > -1) {
      selectedItems.value.splice(idx, 1)
    } else {
      selectedItems.value.push(item)
    }
  } else {
    selectedItems.value = [item]
  }
}

const fetchMedia = async () => {
  loading.value = true
  try {
    const res = await window.axios.get(route('dashboard.media.index'), {
      headers: { Accept: 'application/json' }
    })
    if (res.data?.media) {
      mediaList.value = res.data.media
    }
  } catch (err) {
    console.error('Failed to load media list:', err)
  } finally {
    loading.value = false
  }
}

const handleUpload = async (e) => {
  const fileList = Array.from(e.target.files || [])
  if (fileList.length === 0) return

  uploading.value = true
  const formData = new FormData()

  if (fileList.length === 1) {
    formData.append('file', fileList[0])
  } else {
    fileList.forEach(file => formData.append('files[]', file))
  }

  try {
    const res = await window.axios.post(route('dashboard.media.upload'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
        Accept: 'application/json',
      },
    })
    // Refresh media list after upload
    await fetchMedia()

    // Auto-select uploaded files
    const uploadedUrls = []
    if (res.data?.files && Array.isArray(res.data.files)) {
      uploadedUrls.push(...res.data.files.map(f => f.url))
    } else if (res.data?.url) {
      uploadedUrls.push(res.data.url)
    }

    if (uploadedUrls.length > 0) {
      const newlyAdded = mediaList.value.filter(m => uploadedUrls.includes(m.url))
      if (props.multiple) {
        newlyAdded.forEach(item => {
          if (!selectedItems.value.some(i => i.id === item.id || i.url === item.url)) {
            selectedItems.value.push(item)
          }
        })
      } else if (newlyAdded.length > 0) {
        selectedItems.value = [newlyAdded[0]]
      }
    }
  } catch (err) {
    console.error('Media upload failed:', err)
    alert('ছবি আপলোড করতে ব্যর্থ হয়েছে। সর্বোচ্চ ফাইল সাইজ ১০MB।')
  } finally {
    uploading.value = false
    e.target.value = ''
  }
}

const confirmSelection = () => {
  if (selectedItems.value.length === 0) return

  if (props.multiple) {
    emit('select', selectedItems.value)
  } else {
    emit('select', selectedItems.value[0])
  }
  close()
}

const close = () => {
  selectedItems.value = []
  emit('close')
}

watch(() => props.show, (newVal) => {
  if (newVal) {
    selectedItems.value = []
    if (mediaList.value.length === 0) {
      fetchMedia()
    }
  }
})

onMounted(() => {
  if (props.show) {
    fetchMedia()
  }
})
</script>
