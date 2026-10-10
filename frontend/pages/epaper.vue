<template>
  <div class="space-y-6">
    <div class="border-b pb-4 flex items-center justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
          <NuxtLink to="/" class="hover:text-red-600">প্রচ্ছদ</NuxtLink>
          <span>/</span>
          <span>ই-পেপার</span>
        </div>
        <h2 class="text-2xl font-bold text-gray-900">ডিজিটাল ই-পেপার সংস্করণ</h2>
      </div>

      <div class="text-xs text-gray-500 bg-white px-3 py-1.5 rounded-xl border border-gray-200">
        তারিখ: <strong class="text-gray-800">{{ epaperData?.edition_date || 'আজ' }}</strong>
      </div>
    </div>

    <!-- E-Paper Page Viewer -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-6">
      <!-- Page Navigation Switcher -->
      <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-100">
        <div class="flex items-center gap-2">
          <button
            v-for="(page, idx) in epaperData?.pages"
            :key="page.page_no"
            @click="activePage = idx"
            :class="[
              'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all',
              activePage === idx ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            পৃষ্ঠা {{ page.page_no }}
          </button>
        </div>

        <span class="text-xs font-medium text-gray-500">
          {{ currentPage?.title }}
        </span>
      </div>

      <!-- Page Sheet Canvas -->
      <div class="min-h-[500px] bg-gray-50 rounded-xl border border-dashed border-gray-300 flex flex-col items-center justify-center p-8 text-center relative overflow-hidden">
        <div class="max-w-md space-y-3">
          <div class="text-5xl mb-2">📰</div>
          <h3 class="text-lg font-bold text-gray-800">{{ currentPage?.title }} (পৃষ্ঠা {{ currentPage?.page_no }})</h3>
          <p class="text-xs text-gray-500 leading-relaxed">
            মুদ্রিত পত্রিকার ই-পেপার সংস্করণ অনলাইনে ব্রাউজ করছেন। উচ্চ রেজোলিউশনের পৃষ্ঠা ডাউনলোড বা কাটিং করতে পারেন।
          </p>
          <div class="pt-2 flex justify-center gap-3">
            <button class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg shadow-xs hover:bg-red-700">
              📥 পৃষ্ঠা ডাউনলোড (PDF)
            </button>
            <button class="px-4 py-2 bg-gray-200 text-gray-800 text-xs font-semibold rounded-lg hover:bg-gray-300">
              ✂️ খবর ক্রপ / কাটিং
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()
const activePage = ref(0)

const { data: epaperResponse } = await useFetch(`${config.public.apiBase}/m/EPaperViewer`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'page-epaper-viewer',
  default: () => ({
    success: true,
    edition_date: new Date().toLocaleDateString('bn-BD', { year: 'numeric', month: 'long', day: 'numeric' }),
    pages: [
      { page_no: 1, title: 'প্রথম পাতা (প্রধান শিরোনাম)' },
      { page_no: 2, title: 'জাতীয় ও রাজনীতি' },
      { page_no: 3, title: 'অর্থ ও বাণিজ্য' },
      { page_no: 4, title: 'খেলাধুলা ও বিনোদন' },
    ]
  }),
})

const epaperData = computed(() => epaperResponse.value?.success ? epaperResponse.value : null)

const currentPage = computed(() => {
  return epaperData.value?.pages?.[activePage.value] || null
})
</script>
