<template>
  <AppLayout title="অ্যাডভান্সড নিউজ আর্কাইভ">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cyan-100 text-cyan-800">মডিউল</span>
            <h2 class="text-2xl font-bold text-gray-800">অ্যাডভান্সড নিউজ আর্কাইভ ও ক্যালেন্ডার ফিল্টার</h2>
          </div>
          <p class="text-sm text-gray-500 mt-1">তারিখ, বছর ও ক্যাটাগরিভিত্তিক আর্কাইভ সংবাদ অনুসন্ধান ও পরিদর্শন</p>
        </div>
        <div class="text-right">
          <span class="text-xs text-gray-400">সর্বমোট আর্কাইভ খবর:</span>
          <p class="text-xl font-bold text-cyan-600">{{ totalArchived }} টি</p>
        </div>
      </div>

      <!-- Calendar & Search Filter Card -->
      <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-4">
        <form @submit.prevent="applySearch" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
          <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">📅 আর্কাইভ তারিখ নির্বাচন *</label>
            <input 
              v-model="filterDate" 
              type="date" 
              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-gray-800 focus:ring-cyan-500 focus:border-cyan-500"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">📁 ক্যাটাগরি ফিল্টার</label>
            <select 
              v-model="filterCategory" 
              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-700 focus:ring-cyan-500 focus:border-cyan-500">
              <option value="">সকল ক্যাটাগরি</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">🔍 শিরোনামে অনুসন্ধান</label>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="খবরের কি-ওয়ার্ড..." 
              class="w-full border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm text-gray-800 focus:ring-cyan-500 focus:border-cyan-500"
            />
          </div>

          <div class="flex items-center gap-2">
            <button 
              type="submit" 
              class="flex-1 py-2.5 px-4 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-sm font-semibold shadow-sm transition">
              খুঁজুন
            </button>
            <button 
              type="button" 
              @click="resetFilter" 
              class="py-2.5 px-3 border border-gray-200 hover:bg-gray-50 text-gray-500 rounded-xl text-sm">
              রিসেট
            </button>
          </div>
        </form>

        <!-- Current Date Summary Banner -->
        <div class="p-3.5 bg-cyan-50/70 border border-cyan-100 rounded-xl flex items-center justify-between text-xs">
          <div class="flex items-center gap-2 text-cyan-900">
            <span class="font-bold">নির্বাচিত তারিখ:</span>
            <span class="font-semibold">{{ filterDate }}</span>
            <span class="text-cyan-600">|</span>
            <span>প্রকাশিত খবর: <strong>{{ posts.total || posts.data?.length || 0 }} টি</strong></span>
          </div>
          <span class="text-cyan-700 text-[11px]">পাবলিক এপিআই: <code>/api/v1/m/NewsArchive?date={{ filterDate }}</code></span>
        </div>
      </div>

      <!-- Articles Grid / List -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-gray-800 text-sm">আর্কাইভকৃত সংবাদসমূহ ({{ filterDate }})</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-xs text-gray-400 font-semibold uppercase">
              <tr>
                <th class="px-5 py-3.5">ছবি ও শিরোনাম</th>
                <th class="px-5 py-3.5">ক্যাটাগরি</th>
                <th class="px-5 py-3.5">লেখক / রিপোর্টার</th>
                <th class="px-5 py-3.5">প্রকাশের সময়</th>
                <th class="px-5 py-3.5 text-right">লিঙ্ক</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50/50 transition">
                <td class="px-5 py-4 max-w-md">
                  <div class="flex items-center gap-3">
                    <img 
                      v-if="post.featured_image" 
                      :src="post.featured_image" 
                      class="w-12 h-12 rounded-lg object-cover border shrink-0" 
                    />
                    <div v-else class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 shrink-0 text-xs">
                      ছবি নেই
                    </div>
                    <div>
                      <p class="font-bold text-gray-800 text-sm line-clamp-1">{{ post.title }}</p>
                      <span class="text-xs text-gray-400">ID: #{{ post.id }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    {{ post.category?.name || 'সাধারণ' }}
                  </span>
                </td>
                <td class="px-5 py-4 text-xs font-medium text-gray-700">
                  {{ post.author?.name || 'নিজস্ব প্রতিবেদক' }}
                </td>
                <td class="px-5 py-4 text-xs text-gray-400">
                  {{ post.published_at ? new Date(post.published_at).toLocaleTimeString('bn-BD', { hour: '2-digit', minute: '2-digit' }) : 'N/A' }}
                </td>
                <td class="px-5 py-4 text-right">
                  <a 
                    :href="`/news/${post.slug || post.id}`" 
                    target="_blank" 
                    class="inline-flex items-center gap-1 text-xs text-cyan-600 hover:text-cyan-700 font-semibold hover:underline">
                    দেখুন
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                  </a>
                </td>
              </tr>
              <tr v-if="!posts.data || posts.data.length === 0">
                <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">
                  এই তারিখে ({{ filterDate }}) কোনো সংবাদ রেকর্ড পাওয়া যায়নি।
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  posts: Object,
  categories: Array,
  dailyCounts: Object,
  totalArchived: Number,
  filters: Object,
})

const filterDate = ref(props.filters?.date || new Date().toISOString().slice(0, 10))
const filterCategory = ref(props.filters?.category_id || '')
const searchQuery = ref(props.filters?.q || '')

function applySearch() {
  router.get(route('module.NewsArchive.index'), {
    date: filterDate.value,
    category_id: filterCategory.value,
    q: searchQuery.value,
  }, { preserveState: true })
}

function resetFilter() {
  filterDate.value = new Date().toISOString().slice(0, 10)
  filterCategory.value = ''
  searchQuery.value = ''
  router.get(route('module.NewsArchive.index'))
}
</script>
