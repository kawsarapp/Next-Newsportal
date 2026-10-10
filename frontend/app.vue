<template>
  <div class="min-h-screen flex flex-col bg-gray-50/50">
    <!-- Header with Utility, Brand & Nav -->
    <AppHeader />

    <!-- Live Breaking News Bar -->
    <BreakingTicker :breakingNews="breakingNews" />

    <!-- Main Dynamic View (SPA Page Content) -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-8 py-6">
      <NuxtPage :page-key="route => route.fullPath" />
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<script setup>
const config = useRuntimeConfig()

const { data: breakingResponse } = await useFetch(`${config.public.apiBase}/posts/breaking`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'app-breaking-news',
  default: () => ({ success: true, data: [] }),
})

const breakingNews = computed(() => breakingResponse.value?.data || [])
</script>
