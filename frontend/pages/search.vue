<template>
  <div class="space-y-6">
    <div class="border-b pb-4">
      <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
        <NuxtLink to="/" class="hover:text-red-600">প্রচ্ছদ</NuxtLink>
        <span>/</span>
        <span>অনুসন্ধান</span>
      </div>
      <h2 class="text-2xl font-bold text-gray-900">
        অনুসন্ধান ফলাফল: <span class="text-red-600">"{{ query }}"</span>
      </h2>
    </div>

    <!-- Search Input Bar -->
    <div class="max-w-xl">
      <form @submit.prevent="doSearch" class="flex gap-2">
        <input
          v-model="searchInput"
          type="text"
          placeholder="অন্য কোনো শব্দ দিয়ে খুঁজুন..."
          class="flex-1 px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-sm outline-none focus:border-red-600"
        />
        <button type="submit" class="px-5 py-2.5 bg-red-600 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition-colors">
          খুঁজুন
        </button>
      </form>
    </div>

    <!-- Results Grid -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-pulse">
      <div v-for="i in 3" :key="i" class="h-64 bg-gray-200 rounded-2xl"></div>
    </div>

    <div v-else-if="posts.length === 0" class="text-center py-20 bg-white rounded-2xl border p-8 space-y-2">
      <p class="text-base font-bold text-gray-700">"{{ query }}" সম্পর্কিত কোনো সংবাদ পাওয়া যায়নি</p>
      <p class="text-xs text-gray-400">অন্য কোনো প্রাসঙ্গিক শব্দ দিয়ে চেষ্টা করুন।</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-xs hover:shadow-md transition-shadow group flex flex-col justify-between"
      >
        <NuxtLink :to="`/news/${post.slug}`" class="space-y-3 block">
          <div class="h-44 w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
            <img
              :src="post.featured_image?.original || 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600&auto=format&fit=crop'"
              :alt="post.title"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform"
            />
          </div>
          <span class="text-xs font-semibold text-red-600">{{ post.category?.name }}</span>
          <h4 class="font-bold text-sm text-gray-800 line-clamp-2 leading-snug group-hover:text-red-600 transition-colors">
            {{ post.title }}
          </h4>
          <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
            {{ post.excerpt }}
          </p>
        </NuxtLink>

        <div class="pt-3 border-t border-gray-100 text-[11px] text-gray-400 mt-3 flex items-center justify-between">
          <span>{{ post.author?.name || 'অনলাইন ডেস্ক' }}</span>
          <span>👁️ {{ post.view_count || 0 }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()

const query = computed(() => String(route.query.q || '').trim())
const searchInput = ref(query.value)

watch(query, (newVal) => {
  searchInput.value = newVal
})

const { data: searchResponse, pending, error } = await useFetch(() => `${config.public.apiBase}/search?q=${encodeURIComponent(query.value)}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: () => `search-query-${query.value}`,
  immediate: true,
  default: () => ({ data: [] }),
})

const posts = computed(() => searchResponse.value?.data || [])

const doSearch = () => {
  if (searchInput.value.trim()) {
    router.push({ path: '/search', query: { q: searchInput.value.trim() } })
  }
}
</script>
