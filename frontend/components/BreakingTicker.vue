<template>
  <div v-if="breakingNews.length > 0" class="bg-red-600 text-white text-xs font-semibold py-2 px-4 sm:px-8">
    <div class="max-w-7xl mx-auto flex items-center gap-3">
      <div class="flex items-center gap-1.5 flex-shrink-0 bg-white/20 px-2 py-0.5 rounded-full uppercase tracking-wider text-[10px]">
        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
        ব্রেকিং
      </div>

      <div class="flex-1 overflow-hidden">
        <NuxtLink :to="`/news/${currentBreaking.slug}`" class="hover:underline truncate block">
          {{ currentBreaking.title }}
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  breakingNews: {
    type: Array,
    default: () => []
  }
})

const currentIndex = ref(0)
const currentBreaking = computed(() => {
  if (props.breakingNews.length === 0) return { title: '', slug: '' }
  return props.breakingNews[currentIndex.value] || props.breakingNews[0]
})

// Auto rotate breaking items every 5 seconds
onMounted(() => {
  if (props.breakingNews.length > 1) {
    setInterval(() => {
      currentIndex.value = (currentIndex.value + 1) % props.breakingNews.length
    }, 5000)
  }
})
</script>
