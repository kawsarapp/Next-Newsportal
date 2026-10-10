<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">ইউজার, রোল ও পারমিশন কন্ট্রোল</h2>
          <p class="text-sm text-gray-500 mt-0.5">অ্যাডমিন, সম্পাদক, সহ-সম্পাদক ও সাংবাদিকদের অ্যাক্সেস লেভেল পরিচালনা</p>
        </div>

        <button
          @click="openModal"
          class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-1.5"
        >
          <span>+</span> নতুন ইউজার ইনভাইট করুন
        </button>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
              <th class="py-3.5 px-4">ইউজার</th>
              <th class="py-3.5 px-4">ইমেইল</th>
              <th class="py-3.5 px-4">নির্ধারিত রোল</th>
              <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
              <th class="py-3.5 px-4 text-right">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50 transition-colors">
              <td class="py-3.5 px-4 font-semibold text-gray-800 text-sm flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 font-bold text-xs flex items-center justify-center">
                  {{ user.name.charAt(0) }}
                </div>
                {{ user.name }}
              </td>
              <td class="py-3.5 px-4 text-sm text-gray-600 font-mono text-xs">{{ user.email }}</td>
              <td class="py-3.5 px-4 text-sm">
                <span class="px-2.5 py-1 bg-red-50 text-red-700 font-semibold rounded-lg text-xs capitalize">
                  {{ user.role }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">সক্রিয়</span>
              </td>
              <td class="py-3.5 px-4 text-right">
                <button class="text-blue-600 hover:underline text-xs font-medium mr-3">পারমিশন</button>
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
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  users: {
    type: Array,
    default: () => [
      { id: 1, name: 'সুপার অ্যাডমিন', email: 'admin@newsportal.dev', role: 'Super Admin' },
      { id: 2, name: 'চিফ এডিটর', email: 'editor@newsportal.dev', role: 'Chief Editor' },
      { id: 3, name: 'স্টাফ রিপোর্টার', email: 'reporter@newsportal.dev', role: 'Reporter' },
    ]
  }
})

const openModal = () => {
  alert('নতুন ইউজার যুক্ত বা আমন্ত্রণ জানানোর উইন্ডো ওপেন হচ্ছে...')
}
</script>
