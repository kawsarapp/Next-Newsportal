<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-800">রিপোর্টার ও নিউজরুম নেটওয়ার্ক</h2>
          <p class="text-sm text-gray-500 mt-0.5">দেশজুড়ে নিজস্ব প্রতিবেদক ও জেলা/উপজেলা প্রতিনিধিদের তালিকা</p>
        </div>
        <div class="flex items-center gap-3">
          <a :href="route('dashboard.reporters.payments')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            সম্মানী ও বিলিং (Billing)
          </a>
        </div>
      </div>

      <!-- Reporter Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div v-for="reporter in reporters" :key="reporter.id" class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 font-bold text-lg flex items-center justify-center">
              {{ reporter.name.charAt(0) }}
            </div>
            <div>
              <h3 class="font-bold text-gray-800">{{ reporter.name }}</h3>
              <p class="text-xs text-gray-500">{{ reporter.email }}</p>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2 text-xs py-2 border-t border-b border-gray-50">
            <div>
              <span class="text-gray-400 block">দায়িত্ব:</span>
              <span class="font-medium text-gray-700 capitalize">{{ reporter.reporter_type || 'স্টাফ রিপোর্টার' }}</span>
            </div>
            <div>
              <span class="text-gray-400 block">মোট সংবাদ:</span>
              <span class="font-bold text-red-600">{{ reporter.posts_count || 0 }} টি</span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-1">
            <span :class="['px-2.5 py-0.5 rounded-full text-xs font-semibold', reporter.status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500']">
              {{ reporter.status === 'active' ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
            </span>
            <span class="text-xs text-gray-400">যোগদান: {{ reporter.joined_date }}</span>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  reporters: {
    type: Array,
    default: () => [],
  }
})
</script>
