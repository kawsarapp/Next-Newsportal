<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-800">সার্চ ইঞ্জিন অপ্টিমাইজেশন (SEO) ড্যাশবোর্ড</h2>
          <p class="text-sm text-gray-500 mt-0.5">গুগল নিউজ ইন্ডেক্সিং, সাইটম্যাপ ও মেটাট্যাগ মনিটরিং</p>
        </div>
      </div>

      <!-- SEO Score Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
            🗺️
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-700">XML নিউজ সাইটম্যাপ</h3>
            <p class="text-xs text-emerald-600 font-medium mt-0.5">স্বয়ংক্রিয় জেনারেটেড ও সক্রিয়</p>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
            🏷️
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-700">NewsArticle স্কিমা</h3>
            <p class="text-xs text-blue-600 font-medium mt-0.5">Schema.org JSON-LD সমর্থিত</p>
          </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
            🤖
          </div>
          <div>
            <h3 class="text-sm font-semibold text-gray-700">Robots.txt কনফিগ</h3>
            <p class="text-xs text-purple-600 font-medium mt-0.5">ক্রলিং নিয়মাবলী অপ্টিমাইজড</p>
          </div>
        </div>
      </div>

      <!-- SEO Config Form -->
      <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4 max-w-2xl">
        <h3 class="font-bold text-gray-800 text-base border-b pb-3">গ্লোবাল মেটাট্যাগ কনফিগারেশন</h3>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ডিফল্ট মেটা টাইটেল</label>
          <input type="text" value="সর্বশেষ খবর, রাজনীতি, অর্থনীতি ও খেলাধুলা" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">ডিফল্ট মেটা ডেসক্রিপশন</label>
          <textarea rows="3" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500">বাংলাদেশের অন্যতম জনপ্রিয় হেডলেস ডিজিটাল নিউজপেপার পোর্টাল।</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Google Search Console Verification Tag</label>
          <input type="text" placeholder="google-site-verification=..." class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
        </div>

        <button type="button" @click="save" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold shadow-sm">
          সংরক্ষণ করুন
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const save = () => {
  alert('SEO সেটিংস সফলভাবে আপডেট হয়েছে!')
}
</script>
