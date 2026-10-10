<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">প্লাগইন মডিউল</span>
            <h2 class="text-xl font-bold text-gray-800">বিজ্ঞাপন ও আয় ব্যবস্থাপনা (Ads Manager)</h2>
          </div>
          <p class="text-sm text-gray-500 mt-0.5">গুগল অ্যাডসেন্স, কাস্টম ব্যানার ও স্পন্সরশিপ স্লট পরিচালনা</p>
        </div>

        <button
          @click="openModal()"
          class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-1.5"
        >
          <span>+</span> নতুন অ্যাড স্লট
        </button>
      </div>

      <!-- Quick Metrics -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">সক্রিয় অ্যাড স্লট</span>
          <p class="text-2xl font-bold text-gray-800 mt-1">{{ slots.length }} টি</p>
          <span class="text-[11px] text-emerald-600 font-medium">হোমপেজ ও আর্টিকেল পেজ লাইভ</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">মোট বিজ্ঞাপন ক্যাম্পেইন</span>
          <p class="text-2xl font-bold text-blue-600 mt-1">৩ টি</p>
          <span class="text-[11px] text-gray-400">ডাইরেক্ট বিজ্ঞাপনদাতা ও অ্যাডসেন্স</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">Ads.txt স্ট্যাটাস</span>
          <p class="text-2xl font-bold text-emerald-600 mt-1">ভেরিফাইড ✓</p>
          <span class="text-[11px] text-gray-400">গুগল বট রিডেবল</span>
        </div>
      </div>

      <!-- Ad Slots Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-gray-800 text-sm">বিজ্ঞাপন স্লট তালিকা</h3>
          <span class="text-xs text-gray-400">ফ্রন্টএন্ডে সরাসরি এই পজিশনগুলোতে বিজ্ঞাপন রেন্ডার হবে</span>
        </div>

        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
              <th class="py-3 px-4">স্লটের নাম</th>
              <th class="py-3 px-4">পজিশন / লোকেশন</th>
              <th class="py-3 px-4">ডিভাইস টার্গেট</th>
              <th class="py-3 px-4 text-center">স্ট্যাটাস</th>
              <th class="py-3 px-4 text-right">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="slot in slots" :key="slot.id" class="hover:bg-gray-50 transition-colors">
              <td class="py-3 px-4 font-semibold text-gray-800 text-sm">{{ slot.name }}</td>
              <td class="py-3 px-4 text-xs font-mono text-gray-500 bg-gray-50/50 rounded">{{ slot.location }}</td>
              <td class="py-3 px-4 text-xs text-gray-600">ডেস্কটপ ও মোবাইল</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">সক্রিয়</span>
              </td>
              <td class="py-3 px-4 text-right">
                <button @click="editSlot(slot)" class="text-blue-600 hover:underline text-xs font-medium mr-3">কোড বসান</button>
                <button class="text-red-500 hover:text-red-700 text-xs">মুছুন</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  slots: {
    type: Array,
    default: () => [
      { id: 1, name: 'হেডার ব্যানার অ্যাড', location: 'header_top' },
      { id: 2, name: 'নিউজ বিস্তারিত সাইডবার', location: 'article_sidebar' },
      { id: 3, name: 'নিউজ প্যারাগ্রাফের মাঝে', location: 'in_content_middle' },
      { id: 4, name: 'হোমপেজ ব্রেকিং এর নিচে', location: 'home_below_ticker' },
    ]
  }
})

const openModal = () => {
  alert('নতুন বিজ্ঞাপন স্লট তৈরি উইন্ডো ওপেন হচ্ছে...')
}

const editSlot = (slot) => {
  const code = prompt(`'${slot.name}' এর জন্য HTML বা Google AdSense স্ক্রিপ্ট কোড দিন:`, '<!-- AdSense Script Code -->')
  if (code) {
    alert('বিজ্ঞাপন কোড সফলভাবে সংরক্ষিত হয়েছে!')
  }
}

</script>
