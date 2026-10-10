<template>
  <AppLayout>
    <div class="p-6 flex items-center justify-center min-h-96">
      <div class="text-center">
        <div class="text-6xl mb-4">🚧</div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">শীঘ্রই আসছে</h2>
        <p class="text-gray-500 text-sm">এই মডিউলটি তৈরি হচ্ছে।</p>
        <Link :href="route('dashboard.overview')" class="mt-6 inline-block text-sm text-red-600 hover:underline">← ড্যাশবোর্ডে ফিরুন</Link>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
defineProps({ title: String })
</script>
