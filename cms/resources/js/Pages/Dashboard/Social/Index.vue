<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">প্লাগইন মডিউল</span>
            <h2 class="text-xl font-bold text-gray-800">সোশ্যাল মিডিয়া ডিস্ট্রিবিউশন</h2>
          </div>
          <p class="text-sm text-gray-500 mt-0.5">ফেসবুক পেজ ও টেলিগ্রাম চ্যানেলে স্বয়ংক্রিয় নিউজ সম্প্রচার</p>
        </div>

        <button
          @click="openModal"
          class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-1.5"
        >
          <span>+</span> নতুন চ্যানেল যুক্ত করুন
        </button>
      </div>

      <!-- Connected Channels -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div
          v-for="acc in accounts"
          :key="acc.id"
          class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between"
        >
          <div class="flex items-center gap-3">
            <div :class="['w-11 h-11 rounded-xl flex items-center justify-center text-xl font-bold', platformStyle(acc.platform)]">
              {{ platformIcon(acc.platform) }}
            </div>
            <div>
              <h3 class="font-bold text-gray-800 text-sm">{{ acc.account_name }}</h3>
              <p class="text-xs text-gray-400 capitalize">{{ acc.platform }}</p>
            </div>
          </div>

          <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-semibold">
            সংযুক্ত ✓
          </span>
        </div>
      </div>

      <!-- Quick Publish Simulator -->
      <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4 max-w-3xl">
        <h3 class="font-bold text-gray-800 text-base border-b pb-3 flex items-center gap-2">
          <span>📢</span> সরাসরি সোশ্যাল পোস্ট তৈরি করুন
        </h3>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাপশন / পোস্ট বার্তা</label>
          <textarea
            v-model="caption"
            rows="3"
            placeholder="সোশ্যাল মিডিয়ায় প্রচারের জন্য আকর্ষণীয় ক্যাপশন লিখুন..."
            class="w-full text-sm p-3 border rounded-xl outline-none focus:border-red-500 bg-gray-50/50"
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">সংবাদের লিংক (URL)</label>
          <input
            v-model="newsUrl"
            type="url"
            placeholder="https://newsportal.com/news/example-article"
            class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
          />
        </div>

        <div class="flex items-center justify-between pt-2">
          <div class="flex items-center gap-4 text-xs text-gray-600">
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" checked class="rounded text-red-600 focus:ring-red-500" />
              ফেসবুক পেজ
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="checkbox" checked class="rounded text-red-600 focus:ring-red-500" />
              টেলিগ্রাম চ্যানেল
            </label>
          </div>

          <button
            type="button"
            @click="publish"
            :disabled="publishing || !caption"
            class="px-5 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-40 text-white rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2"
          >
            <span v-if="publishing" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ publishing ? 'শেয়ার হচ্ছে...' : 'তাত্ক্ষণিক শেয়ার করুন' }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  accounts: {
    type: Array,
    default: () => [
      { id: 1, platform: 'facebook', account_name: 'মূল ফেসবুক পেজ' },
      { id: 2, platform: 'telegram', account_name: 'অফিসিয়াল টেলিগ্রাম চ্যানেল' },
      { id: 3, platform: 'x', account_name: 'নিউজপোর্টাল এক্স হ্যান্ডল' },
    ]
  }
})

const caption = ref('')
const newsUrl = ref('')
const publishing = ref(false)

const platformIcon = (p) => {
  if (p === 'facebook') return '📘'
  if (p === 'telegram') return '✈️'
  if (p === 'x') return '✖️'
  return '🌐'
}

const platformStyle = (p) => {
  if (p === 'facebook') return 'bg-blue-50 text-blue-600'
  if (p === 'telegram') return 'bg-sky-50 text-sky-600'
  return 'bg-gray-100 text-gray-800'
}

const openModal = () => {
  alert('নতুন সামাজিক যোগাযোগ মাধ্যম যুক্ত করার API কনফিগারেশন উইন্ডো ওপেন হচ্ছে...')
}

const publish = () => {
  publishing.value = true
  setTimeout(() => {
    publishing.value = false
    alert('সংবাদটি সফলভাবে ফেসবুক এবং টেলিগ্রাম চ্যানেলে সম্প্রচারিত হয়েছে!')
    caption.value = ''
    newsUrl.value = ''
  }, 1000)
}
</script>
