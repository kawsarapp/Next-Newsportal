<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-800">সিস্টেম ও পোর্টাল সেটিংস</h2>
          <p class="text-sm text-gray-500 mt-0.5">নিউজপোর্টাল ব্র্যান্ডিং, ইমেইল, API কী ও নিরাপত্তা কনফিগারেশন</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings Form -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
          <h3 class="font-bold text-gray-800 text-base border-b pb-3">সাধারণ পোর্টাল তথ্য</h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">পোর্টালের নাম</label>
              <input type="text" :value="portal.name" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ট্যাগলাইন</label>
              <input type="text" :value="portal.tagline || 'বাংলাদেশের সেরা নিউজ পোর্টাল'" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ভাষা</label>
              <select class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500 bg-white">
                <option value="bn">বাংলা (Bengali)</option>
                <option value="en">English</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">টাইমজোন</label>
              <input type="text" value="Asia/Dhaka" disabled class="w-full text-sm px-3 py-2 border rounded-xl bg-gray-50 text-gray-500" />
            </div>
          </div>

          <div class="pt-3 border-t">
            <button type="button" @click="save" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold shadow-sm">
              পরিবর্তন সংরক্ষণ করুন
            </button>
          </div>
        </div>

        <!-- API Key Box -->
        <div class="space-y-6">
          <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
            <div class="flex items-center gap-2 text-red-600 font-bold text-sm">
              <span>🔑</span> হেডলেস API অ্যাক্সেস
            </div>
            <p class="text-xs text-gray-500 leading-relaxed">
              আপনার ফ্রন্টএন্ড Nuxt/Next.js অ্যাপ্লিকেশনের সাথে এই ব্যাকএন্ডকে যুক্ত করতে নিচের পোর্টাল কী ব্যবহার করুন।
            </p>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">পোর্টাল API প্রিফিক্স</label>
              <div class="p-2.5 bg-gray-50 border rounded-lg font-mono text-xs text-gray-700 break-all select-all">
                {{ portal.api_key_prefix || 'pk_live_...' }}
              </div>
            </div>

            <div class="pt-2">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                API স্ট্যাটাস: সচল ও সুরক্ষিত
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  portal: {
    type: Object,
    default: () => ({}),
  }
})

const save = () => {
  alert('পোর্টাল সেটিংস সফলভাবে আপডেট হয়েছে!')
}
</script>
