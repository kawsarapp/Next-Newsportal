<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-800">এডিটোরিয়াল ওয়ার্কফ্লো (Editorial Desk)</h2>
          <p class="text-sm text-gray-500 mt-0.5">নিউজ যাচাই, সংশোধন, ফ্যাক্ট-চেক ও অনুমোদন পাইপলাইন</p>
        </div>
      </div>

      <!-- Kanban Board Columns -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Drafts -->
        <div class="bg-gray-100/70 p-4 rounded-2xl flex flex-col min-h-[500px]">
          <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-gray-500"></span>
              <h3 class="font-bold text-gray-700 text-sm">খসড়া (Drafts)</h3>
            </div>
            <span class="text-xs bg-white px-2 py-0.5 rounded-full font-semibold text-gray-600 shadow-sm">{{ drafts.length }}</span>
          </div>

          <div class="space-y-3 flex-1 overflow-y-auto">
            <div v-for="post in drafts" :key="post.id" class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm hover:shadow transition-shadow">
              <span class="text-[11px] font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded">{{ post.category?.name || 'সাধারণ' }}</span>
              <h4 class="font-semibold text-gray-800 text-sm mt-2 line-clamp-2">{{ post.title }}</h4>
              <div class="flex items-center justify-between text-xs text-gray-400 mt-3 pt-2 border-t border-gray-100">
                <span>{{ post.author?.name }}</span>
                <Link :href="route('dashboard.posts.edit', post.id)" class="text-blue-600 hover:underline">সম্পাদনা →</Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Review -->
        <div class="bg-amber-50/60 p-4 rounded-2xl flex flex-col min-h-[500px] border border-amber-100">
          <div class="flex items-center justify-between pb-3 mb-3 border-b border-amber-200/60">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
              <h3 class="font-bold text-amber-900 text-sm">পর্যালোচনা বাকি (Review)</h3>
            </div>
            <span class="text-xs bg-white px-2 py-0.5 rounded-full font-semibold text-amber-700 shadow-sm">{{ pendingReviews.length }}</span>
          </div>

          <div class="space-y-3 flex-1 overflow-y-auto">
            <div v-for="post in pendingReviews" :key="post.id" class="bg-white p-4 rounded-xl border border-amber-200/60 shadow-sm hover:shadow transition-shadow">
              <span class="text-[11px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">{{ post.category?.name }}</span>
              <h4 class="font-semibold text-gray-800 text-sm mt-2 line-clamp-2">{{ post.title }}</h4>
              <div class="flex items-center justify-between text-xs text-gray-500 mt-3 pt-2 border-t border-gray-100">
                <span>{{ post.author?.name }}</span>
                <button @click="approve(post.id)" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-semibold">অনুমোদন</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Approved -->
        <div class="bg-blue-50/60 p-4 rounded-2xl flex flex-col min-h-[500px] border border-blue-100">
          <div class="flex items-center justify-between pb-3 mb-3 border-b border-blue-200/60">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
              <h3 class="font-bold text-blue-900 text-sm">অনুমোদিত (Ready to Publish)</h3>
            </div>
            <span class="text-xs bg-white px-2 py-0.5 rounded-full font-semibold text-blue-700 shadow-sm">{{ approved.length }}</span>
          </div>

          <div class="space-y-3 flex-1 overflow-y-auto">
            <div v-for="post in approved" :key="post.id" class="bg-white p-4 rounded-xl border border-blue-200/60 shadow-sm hover:shadow transition-shadow">
              <span class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ post.category?.name }}</span>
              <h4 class="font-semibold text-gray-800 text-sm mt-2 line-clamp-2">{{ post.title }}</h4>
              <div class="flex items-center justify-between text-xs text-gray-500 mt-3 pt-2 border-t border-gray-100">
                <span>{{ post.author?.name }}</span>
                <button @click="publish(post.id)" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold shadow-sm">প্রকাশ করুন</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Published -->
        <div class="bg-emerald-50/60 p-4 rounded-2xl flex flex-col min-h-[500px] border border-emerald-100">
          <div class="flex items-center justify-between pb-3 mb-3 border-b border-emerald-200/60">
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
              <h3 class="font-bold text-emerald-900 text-sm">প্রকাশিত (Live)</h3>
            </div>
            <span class="text-xs bg-white px-2 py-0.5 rounded-full font-semibold text-emerald-700 shadow-sm">{{ published.length }}</span>
          </div>

          <div class="space-y-3 flex-1 overflow-y-auto">
            <div v-for="post in published" :key="post.id" class="bg-white p-4 rounded-xl border border-emerald-200/60 shadow-sm">
              <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">{{ post.category?.name }}</span>
                <span class="text-[11px] text-gray-400">👁️ {{ post.view_count }}</span>
              </div>
              <h4 class="font-semibold text-gray-800 text-sm mt-2 line-clamp-2">{{ post.title }}</h4>
              <div class="flex items-center justify-between text-xs text-gray-400 mt-3 pt-2 border-t border-gray-100">
                <span>{{ post.author?.name }}</span>
                <span class="text-emerald-600 font-medium">লাইভ ✓</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  posts: {
    type: Array,
    default: () => [],
  }
})

const drafts = computed(() => props.posts.filter(p => p.status?.value === 'draft' || p.status === 'draft'))
const pendingReviews = computed(() => props.posts.filter(p => p.status?.value === 'pending_review' || p.status === 'pending_review'))
const approved = computed(() => props.posts.filter(p => p.status?.value === 'approved' || p.status === 'approved'))
const published = computed(() => props.posts.filter(p => p.status?.value === 'published' || p.status === 'published'))

const approve = (id) => {
  router.post(route('dashboard.posts.approve', id), {}, { preserveScroll: true })
}

const publish = (id) => {
  router.post(route('dashboard.posts.publish', id), {}, { preserveScroll: true })
}
</script>
