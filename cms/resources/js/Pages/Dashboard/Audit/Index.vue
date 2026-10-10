<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">নিরাপত্তা ও অডিট লগ ট্রেইল</h2>
          <p class="text-sm text-gray-500 mt-0.5">কে কখন কোন সংবাদ তৈরি, প্রকাশ, পরিবর্তন বা মুছেছেন তার প্রামাণ্য রেকর্ড</p>
        </div>
      </div>

      <!-- Logs Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
              <th class="py-3 px-4">অ্যাকশন / ইভেন্ট</th>
              <th class="py-3 px-4">ব্যবহারকারী</th>
              <th class="py-3 px-4">আইপি অ্যাড্রেস</th>
              <th class="py-3 px-4">স্ট্যাটাস কোড</th>
              <th class="py-3 px-4 text-right">সময়</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50 transition-colors">
              <td class="py-3.5 px-4 font-mono text-xs font-semibold text-gray-800">
                <span class="px-2 py-0.5 bg-gray-100 rounded text-gray-700">{{ log.action }}</span>
              </td>
              <td class="py-3.5 px-4 text-sm font-medium text-gray-700">{{ log.user_name }}</td>
              <td class="py-3.5 px-4 text-xs font-mono text-gray-500">{{ log.ip }}</td>
              <td class="py-3.5 px-4">
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700">200 OK</span>
              </td>
              <td class="py-3.5 px-4 text-right text-xs text-gray-400">{{ log.time }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  logs: {
    type: Array,
    default: () => [
      { id: 1, action: 'dashboard.posts.store', user_name: 'Super Admin', ip: '127.0.0.1', time: '২ মিনিট আগে' },
      { id: 2, action: 'dashboard.categories.store', user_name: 'Chief Editor', ip: '127.0.0.1', time: '১৫ মিনিট আগে' },
      { id: 3, action: 'dashboard.media.upload', user_name: 'Staff Reporter', ip: '127.0.0.1', time: '১ ঘণ্টা আগে' },
    ]
  }
})
</script>
