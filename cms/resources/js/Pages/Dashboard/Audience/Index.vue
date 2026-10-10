<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">প্লাগইন মডিউল</span>
            <h2 class="text-xl font-bold text-gray-800">পাঠক ও এনগেজমেন্ট (Audience Desk)</h2>
          </div>
          <p class="text-sm text-gray-500 mt-0.5">মন্তব্য যাচাই, মডারেশন, স্প্যাম ফিল্টারিং ও সাবস্ক্রাইবার তালিকা</p>
        </div>
      </div>

      <!-- Overview Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">অনুমোদনের অপেক্ষায় মন্তব্য</span>
          <p class="text-2xl font-bold text-amber-600 mt-1">{{ comments.length }} টি</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">নিউজলেটার সাবস্ক্রাইবার</span>
          <p class="text-2xl font-bold text-blue-600 mt-1">১২৮ জন</p>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">ওয়েব পুশ নোটিফিকেশন</span>
          <p class="text-2xl font-bold text-emerald-600 mt-1">সক্রিয় ✓</p>
        </div>
      </div>

      <!-- Comments Moderation Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-gray-800 text-sm">সাম্প্রতিক পাঠকদের মন্তব্য</h3>
        </div>

        <div class="divide-y divide-gray-50">
          <div v-for="c in comments" :key="c.id" class="p-4 hover:bg-gray-50 transition-colors flex items-start justify-between gap-4">
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="font-bold text-sm text-gray-800">{{ c.name }}</span>
                <span class="text-xs text-gray-400 font-mono">{{ c.email }}</span>
                <span class="text-[11px] text-gray-400">· {{ c.time }}</span>
              </div>
              <p class="text-xs text-gray-700 bg-gray-50 p-2.5 rounded-lg border border-gray-100/80 leading-relaxed">{{ c.comment }}</p>
              <span class="text-[11px] text-blue-600 font-medium">সংবাদ: {{ c.post_title }}</span>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
              <button @click="approve(c)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">অনুমোদন</button>
              <button @click="spam(c)" class="px-3 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-lg text-xs font-semibold">স্প্যাম</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const comments = ref([
  {
    id: 1,
    name: 'তানভীর আহমেদ',
    email: 'tanvir@gmail.com',
    comment: 'খুবই সময়োপযোগী ও বস্তুনিষ্ঠ সংবাদ। ধন্যবাদ আপনাদের পুরো টিমকে।',
    post_title: 'দেশজুড়ে ডিজিটাল রূপান্তরের নতুন দিগন্ত উন্মোচিত',
    time: '১০ মিনিট আগে',
  },
  {
    id: 2,
    name: 'ফারজানা হক',
    email: 'farzana@yahoo.com',
    comment: 'এই বিষয়ে স্থানীয় কর্তৃপক্ষের আরও জোরদার ভূমিকা রাখা উচিত ছিল।',
    post_title: 'উচ্চশিক্ষা ও উদ্ভাবনী গবেষণায় নতুন বৃত্তির ঘোষণা',
    time: '১ ঘণ্টা আগে',
  }
])

const approve = (c) => {
  alert(`'${c.name}' এর মন্তব্য অনুমোদিত হয়েছে!`)
  comments.value = comments.value.filter(item => item.id !== c.id)
}

const spam = (c) => {
  alert(`'${c.name}' এর মন্তব্য স্প্যাম হিসেবে চিহ্নিত হয়েছে!`)
  comments.value = comments.value.filter(item => item.id !== c.id)
}
</script>
