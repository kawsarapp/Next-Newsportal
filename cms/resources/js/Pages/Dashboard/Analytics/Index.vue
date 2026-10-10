<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">প্লাগইন মডিউল</span>
            <h2 class="text-xl font-bold text-gray-800">অ্যানালিটিক্স ও পাঠক রিপোর্টস</h2>
          </div>
          <p class="text-sm text-gray-500 mt-0.5">রিয়েলটাইম পাঠক সংখ্যা, পেজভিউ ও ট্রাফিক পারফরম্যান্স বিশ্লেষণ</p>
        </div>
      </div>

      <!-- Live Counters -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">লাইভ ভিজিটর (রিয়েলটাইম)</span>
          <p class="text-3xl font-extrabold text-emerald-600 mt-1 flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
            ৪৮ জন
          </p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">আজকের মোট পেজভিউ</span>
          <p class="text-3xl font-extrabold text-gray-800 mt-1">৬,২৭০</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">গড় এনগেজমেন্ট সময়</span>
          <p class="text-3xl font-extrabold text-blue-600 mt-1">২মি ৪২সে</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">মোবাইল পাঠক অনুপাত</span>
          <p class="text-3xl font-extrabold text-purple-600 mt-1">৭৮.৪%</p>
        </div>
      </div>

      <!-- Traffic Sources & Devices -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Traffic Sources -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-sm border-b pb-3">শীর্ষ ট্রাফিক সোর্স</h3>
          <div class="space-y-3">
            <div>
              <div class="flex justify-between text-xs font-semibold mb-1">
                <span>গুগল সার্চ ও গুগল নিউজ</span>
                <span class="text-gray-600">৫২%</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-red-600 rounded-full" style="width: 52%"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-semibold mb-1">
                <span>ফেসবুক ও সোশ্যাল মিডিয়া</span>
                <span class="text-gray-600">৩৩%</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-600 rounded-full" style="width: 33%"></div>
              </div>
            </div>

            <div>
              <div class="flex justify-between text-xs font-semibold mb-1">
                <span>সরাসরি ভিজিট (Direct)</span>
                <span class="text-gray-600">১৫%</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-600 rounded-full" style="width: 15%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Performing Categories -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-sm border-b pb-3">সর্বাধিক পঠিত ক্যাটাগরি</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between text-xs p-2 bg-gray-50 rounded-lg">
              <span class="font-semibold text-gray-800">১. রাজনীতি ও নির্বাচন</span>
              <span class="font-bold text-red-600">২,৮২০ ভিউ</span>
            </div>
            <div class="flex items-center justify-between text-xs p-2 bg-gray-50 rounded-lg">
              <span class="font-semibold text-gray-800">২. ক্রিকেট ও খেলাধুলা</span>
              <span class="font-bold text-red-600">১,৯৪০ ভিউ</span>
            </div>
            <div class="flex items-center justify-between text-xs p-2 bg-gray-50 rounded-lg">
              <span class="font-semibold text-gray-800">৩. তথ্যপ্রযুক্তি ও চাকরি</span>
              <span class="font-bold text-red-600">১,১২০ ভিউ</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>
