<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">মিডিয়া লাইব্রেরি</h2>
          <p class="text-sm text-gray-500 mt-0.5">ছবি, ভিডিও ও অন্যান্য ডিজিটাল মিডিয়া পরিচালনা করুন</p>
        </div>
        <label class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2 cursor-pointer">
          <span>📤</span> নতুন ছবি আপলোড
          <input type="file" @change="uploadMedia" accept="image/*" class="hidden" />
        </label>
      </div>

      <!-- Upload Progress / Notification -->
      <div v-if="uploading" class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm flex items-center gap-3">
        <span class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></span>
        ছবি আপলোড এবং প্রসেস করা হচ্ছে...
      </div>

      <!-- Search & Filter -->
      <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between gap-4">
        <input
          v-model="search"
          type="text"
          placeholder="ফাইলের নাম দিয়ে খুঁজুন..."
          class="text-sm px-3 py-2 border rounded-lg outline-none w-72 focus:border-red-500"
        />
        <span class="text-xs text-gray-500 font-medium">মোট ফাইল: {{ filteredMedia.length }}</span>
      </div>

      <!-- Media Grid -->
      <div v-if="filteredMedia.length === 0" class="bg-white rounded-2xl p-16 text-center text-gray-400 border border-gray-100 shadow-sm">
        <div class="text-4xl mb-3">🖼️</div>
        <p class="text-base font-semibold text-gray-700">কোনো মিডিয়া ফাইল পাওয়া যায়নি</p>
        <p class="text-xs text-gray-400 mt-1">উপরের বাটন দিয়ে নতুন ছবি আপলোড করুন</p>
      </div>

      <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div
          v-for="item in filteredMedia"
          :key="item.id"
          class="group bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col relative"
        >
          <div class="h-32 bg-gray-100 overflow-hidden flex items-center justify-center relative">
            <img :src="item.url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
              <button @click="copyUrl(item.url)" class="p-1.5 bg-white/90 rounded-lg text-gray-800 text-xs font-semibold hover:bg-white" title="লিংক কপি">
                🔗
              </button>
              <button @click="deleteItem(item.id)" class="p-1.5 bg-red-600/90 rounded-lg text-white text-xs hover:bg-red-600" title="মুছুন">
                🗑️
              </button>
            </div>
          </div>
          <div class="p-2.5 flex-1 flex flex-col justify-between">
            <p class="text-xs font-medium text-gray-800 truncate" :title="item.name">{{ item.name }}</p>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
              <span>{{ item.size || 'WebP' }}</span>
              <span>{{ item.date }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  media: {
    type: Array,
    default: () => [],
  }
})

const search = ref('')
const uploading = ref(false)

const filteredMedia = computed(() => {
  if (!search.value) return props.media
  return props.media.filter(m => m.name.toLowerCase().includes(search.value.toLowerCase()))
})

const uploadMedia = (e) => {
  const file = e.target.files[0]
  if (!file) return

  uploading.value = true
  const formData = new FormData()
  formData.append('file', file)

  router.post(route('dashboard.media.upload'), formData, {
    forceFormData: true,
    onFinish: () => {
      uploading.value = false
    }
  })
}

const copyUrl = (url) => {
  navigator.clipboard.writeText(url)
  alert('ছবির সরাসরি লিংক ক্লিপবোর্ডে কপি করা হয়েছে!')
}

const deleteItem = (id) => {
  if (confirm('এই মিডিয়া ফাইলটি কি মুছে ফেলতে চান?')) {
    router.delete(route('dashboard.media.destroy', id))
  }
}
</script>
