<template>
  <AppLayout>
    <div class="p-6 space-y-5">

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">নিউজ ম্যানেজমেন্ট</h2>
          <p class="text-sm text-gray-500 mt-0.5">সব নিউজ পরিচালনা করুন</p>
        </div>
        <Link :href="route('dashboard.posts.create')" class="btn-primary">
          + নতুন নিউজ লিখুন
        </Link>
      </div>

      <!-- Status Filter Tabs -->
      <div class="flex gap-2 flex-wrap bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm w-fit">
        <button
          v-for="tab in statusTabs"
          :key="tab.value"
          @click="setFilter('status', tab.value)"
          :class="[
            'px-3 py-1.5 rounded-lg text-sm font-medium transition-all',
            filters.status === tab.value
              ? 'bg-red-600 text-white shadow-sm'
              : 'text-gray-600 hover:bg-gray-100'
          ]"
        >
          {{ tab.label }}
          <span :class="['ml-1 text-xs px-1.5 py-0.5 rounded-full', filters.status === tab.value ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-500']">
            {{ counts[tab.value] ?? 0 }}
          </span>
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
        <input
          v-model="filters.search"
          type="text"
          placeholder="নিউজ খুঁজুন..."
          class="input-field flex-1 min-w-48"
          @input="debouncedSearch"
        >
        <select v-model="filters.category_id" class="input-field w-40" @change="reload">
          <option value="">সব ক্যাটাগরি</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select v-model="filters.type" class="input-field w-36" @change="reload">
          <option value="">সব ধরন</option>
          <option value="article">আর্টিকেল</option>
          <option value="video">ভিডিও</option>
          <option value="gallery">গ্যালারি</option>
          <option value="live">লাইভ</option>
          <option value="opinion">মতামত</option>
        </select>
        <select v-model="filters.sort" class="input-field w-36" @change="reload">
          <option value="latest">সর্বশেষ</option>
          <option value="oldest">পুরনো আগে</option>
          <option value="most_viewed">সর্বাধিক ভিউ</option>
        </select>
      </div>

      <!-- Bulk Action Bar -->
      <div v-if="selected.length > 0" class="bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center gap-3">
        <span class="text-sm font-medium text-blue-700">{{ selected.length }}টি নিউজ নির্বাচিত</span>
        <div class="flex gap-2 ml-auto">
          <button @click="bulkAction('publish')"  class="btn-sm bg-green-600 text-white">প্রকাশ করুন</button>
          <button @click="bulkAction('archive')"  class="btn-sm bg-gray-600 text-white">সংরক্ষণ করুন</button>
          <button @click="bulkAction('trash')"    class="btn-sm bg-red-600 text-white">ট্র্যাশ করুন</button>
          <button @click="selected = []"           class="btn-sm bg-white text-gray-600 border">বাতিল</button>
        </div>
      </div>

      <!-- Posts Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50">
              <th class="py-3 px-4 text-left w-8">
                <input type="checkbox" @change="toggleAll" :checked="allSelected" class="rounded">
              </th>
              <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">নিউজ</th>
              <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden md:table-cell">ক্যাটাগরি</th>
              <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden lg:table-cell">লেখক</th>
              <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide hidden xl:table-cell">ভিউ</th>
              <th class="py-3 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">অবস্থা</th>
              <th class="py-3 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="posts.data.length === 0">
              <td colspan="7" class="py-16 text-center text-gray-400 text-sm">
                কোনো নিউজ পাওয়া যায়নি
              </td>
            </tr>
            <tr
              v-for="post in posts.data"
              :key="post.id"
              class="hover:bg-gray-50 transition-colors group"
            >
              <td class="py-3 px-4">
                <input type="checkbox" :value="post.id" v-model="selected" class="rounded">
              </td>
              <td class="py-3 px-4">
                <div class="flex items-start gap-3">
                  <div class="w-14 h-10 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden hidden sm:block">
                    <img v-if="post.featured_image?.sm" :src="post.featured_image.sm" class="w-full h-full object-cover">
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-300 text-xs">📰</div>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug">{{ post.title }}</p>
                    <div class="flex items-center gap-2 mt-1">
                      <span v-if="post.is_breaking" class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-medium">ব্রেকিং</span>
                      <span v-if="post.is_featured" class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded font-medium">ফিচার্ড</span>
                      <span class="text-xs text-gray-400">{{ post.created_at_ago }}</span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="py-3 px-4 hidden md:table-cell">
                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">
                  {{ post.category?.name || post.category || 'অবর্গীকৃত' }}
                </span>
              </td>
              <td class="py-3 px-4 hidden lg:table-cell">
                <span class="text-xs text-gray-600">{{ post.author?.name || post.author || 'অনলাইন ডেস্ক' }}</span>
              </td>

              <td class="py-3 px-4 hidden xl:table-cell">
                <span class="text-xs font-medium text-gray-700">{{ post.view_count?.toLocaleString('bn-BD') ?? 0 }}</span>
              </td>
              <td class="py-3 px-4">
                <StatusBadge :status="post.status" />
              </td>
              <td class="py-3 px-4 text-right">
                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <template v-if="filters.status === 'trashed' || post.deleted_at">
                    <button @click="restorePost(post)" class="btn-icon text-emerald-600" title="পুনরুদ্ধার করুন">♻️</button>
                    <button @click="confirmDelete(post)" class="btn-icon text-red-600 font-bold" title="স্থায়ীভাবে মুছুন">❌</button>
                  </template>
                  <template v-else>
                    <Link :href="route('dashboard.posts.edit', post.id)" class="btn-icon" title="সম্পাদনা">✏️</Link>
                    <button @click="quickAction(post, 'publish')" v-if="post.status === 'approved' || post.status === 'draft'" class="btn-icon text-green-600" title="প্রকাশ করুন">📢</button>
                    <button @click="quickAction(post, 'approve')" v-if="post.status === 'pending_review'" class="btn-icon text-blue-600" title="অনুমোদন দিন">✅</button>
                    <button @click="quickAction(post, 'breaking')" class="btn-icon" :class="post.is_breaking ? 'text-red-600' : 'text-gray-400'" title="ব্রেকিং">🔴</button>
                    <button @click="confirmDelete(post)" class="btn-icon text-red-500" title="ট্র্যাশে পাঠান">🗑️</button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between">
          <p class="text-sm text-gray-500">
            মোট {{ posts.total }} টি নিউজের মধ্যে {{ posts.from }}-{{ posts.to }} দেখাচ্ছে
          </p>
          <div class="flex gap-1">
            <Link
              v-for="link in posts.links"
              :key="link.label"
              :href="link.url ?? '#'"
              :class="[
                'px-3 py-1.5 text-sm rounded-lg transition-colors',
                link.active ? 'bg-red-600 text-white' : 'text-gray-600 hover:bg-gray-100',
                !link.url ? 'opacity-40 pointer-events-none' : ''
              ]"
              v-html="link.label"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <ConfirmModal
      v-if="deleteTarget"
      :title="deleteTarget.deleted_at || filters.status === 'trashed' ? 'স্থায়ীভাবে মুছে ফেলবেন?' : 'নিউজ ট্র্যাশে পাঠাবেন?'"
      :message="deleteTarget.deleted_at || filters.status === 'trashed' ? `'${deleteTarget?.title}' স্থায়ীভাবে ডাটাবেস থেকে মুছে ফেলা হবে। এই প্রক্রিয়াটি আর পুনরুদ্ধার করা যাবে না!` : `'${deleteTarget?.title}' ট্র্যাশে পাঠানো হবে। পরবর্তীতে চাইলে ট্র্যাশ থেকে পুনরুদ্ধার করতে পারবেন।`"
      :confirm-label="deleteTarget.deleted_at || filters.status === 'trashed' ? 'হ্যাঁ, সম্পূর্ণ মুছুন' : 'হ্যাঁ, ট্র্যাশ করুন'"
      @confirm="doDelete"
      @cancel="deleteTarget = null"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatusBadge from '@/Components/UI/StatusBadge.vue'
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'

const props = defineProps({
  posts:      { type: Object, required: true },
  categories: { type: Array,  default: () => [] },
  counts:     { type: Object, default: () => ({}) },
  filters:    { type: Object, default: () => ({}) },
})

const filters = ref({
  status:      props.filters.status ?? '',
  search:      props.filters.search ?? '',
  category_id: props.filters.category_id ?? '',
  type:        props.filters.type ?? '',
  sort:        props.filters.sort ?? 'latest',
})

const selected    = ref([])
const deleteTarget = ref(null)
const allSelected  = computed(() => props.posts.data.length > 0 && selected.value.length === props.posts.data.length)

const statusTabs = [
  { label: 'সব',           value: '' },
  { label: 'প্রকাশিত',    value: 'published' },
  { label: 'অনুমোদন বাকি', value: 'pending_review' },
  { label: 'ড্রাফট',      value: 'draft' },
  { label: 'শিডিউল',      value: 'scheduled' },
  { label: 'ট্র্যাশ',     value: 'trashed' },
]

let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => reload(), 400)
}

const setFilter = (key, val) => {
  filters.value[key] = val
  reload()
}

const reload = () => {
  router.get(route('dashboard.posts.index'), filters.value, {
    preserveState: true, preserveScroll: true, replace: true,
  })
}

const toggleAll = (e) => {
  selected.value = e.target.checked ? props.posts.data.map(p => p.id) : []
}

const quickAction = (post, action) => {
  router.post(route(`dashboard.posts.${action}`, post.id), {}, {
    preserveScroll: true,
    onSuccess: () => {},
  })
}

const confirmDelete = (post) => { deleteTarget.value = post }
const doDelete = () => {
  router.delete(route('dashboard.posts.destroy', deleteTarget.value.id), {
    preserveScroll: true,
    onSuccess: () => { deleteTarget.value = null },
  })
}

const restorePost = (post) => {
  router.post(route('dashboard.posts.restore', post.id), {}, {
    preserveScroll: true,
    onSuccess: () => {},
  })
}

const bulkAction = (action) => {
  router.post(route('dashboard.posts.bulk'), { ids: selected.value, action }, {
    preserveScroll: true,
    onSuccess: () => { selected.value = [] },
  })
}
</script>

<style>
.input-field { @reference "tailwindcss"; }
.input-field {
  padding: 0.5rem 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  outline: none;
  background: white;
}
.input-field:focus { border-color: #dc2626; box-shadow: 0 0 0 2px rgba(220,38,38,0.1); }
.btn-primary {
  background: #dc2626; color: white; padding: 0.5rem 1rem;
  border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500;
  display: inline-flex; align-items: center; gap: 0.25rem;
  transition: background 0.15s; text-decoration: none;
}
.btn-primary:hover { background: #b91c1c; }
.btn-sm { padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 500; cursor: pointer; }
.btn-icon { padding: 0.375rem; border-radius: 0.375rem; cursor: pointer; transition: background 0.1s; font-size: 0.875rem; }
.btn-icon:hover { background: #f3f4f6; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
