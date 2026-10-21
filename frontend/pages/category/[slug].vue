<template>
  <div class="space-y-6">
    <div class="border-b pb-4">
      <div class="flex items-center gap-2 text-xs text-gray-400 mb-1 flex-wrap">
        <NuxtLink to="/" class="hover:text-red-600 transition-colors">প্রচ্ছদ</NuxtLink>
        <span>/</span>
        <template v-if="parentCategorySlug !== slug">
          <NuxtLink :to="`/category/${parentCategorySlug}`" class="hover:text-red-600 transition-colors">
            {{ parentCategoryName }}
          </NuxtLink>
          <span>/</span>
          <span class="text-gray-700 font-semibold">{{ categoryTitle }}</span>
        </template>
        <template v-else>
          <NuxtLink :to="`/category/${slug}`" class="hover:text-red-600 transition-colors">
            {{ categoryTitle }}
          </NuxtLink>
        </template>
        <template v-if="slug === 'saradesh' && selectedDivision">
          <span>/</span>
          <NuxtLink
            :to="`/category/saradesh?division_id=${selectedDivision.id}`"
            class="text-red-600 font-medium hover:underline"
          >
            {{ selectedDivision.name }} বিভাগ
          </NuxtLink>
        </template>
        <template v-if="slug === 'saradesh' && selectedDistrict">
          <span>/</span>
          <span class="text-gray-600 font-semibold">{{ selectedDistrict.name }} জেলা</span>
        </template>
      </div>
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900">{{ categoryTitle }}</h2>
        <span v-if="!pending && posts.length > 0" class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full font-medium">
          {{ posts.length }} টি সংবাদ
        </span>
      </div>

      <!-- Horizontal Scrolling Subcategories Bar (Mobile & Desktop) -->
      <div v-if="subcategories.length > 0" class="mt-3.5 flex items-center gap-2 overflow-x-auto scrollbar-none touch-scroll-x py-1.5 -mx-4 px-4 sm:mx-0 sm:px-0">
        <NuxtLink
          :to="`/category/${parentCategorySlug}`"
          class="px-3.5 py-1.5 rounded-full text-xs font-bold whitespace-nowrap transition-colors shrink-0 flex items-center gap-1 shadow-2xs"
          :class="slug === parentCategorySlug ? 'bg-red-600 text-white shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
        >
          <span>✦</span>
          <span>সকল {{ parentCategoryName }}</span>
        </NuxtLink>
        <NuxtLink
          v-for="sub in subcategories"
          :key="sub.id"
          :to="`/category/${sub.slug}`"
          class="px-3.5 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-colors shrink-0 shadow-2xs"
          :class="slug === sub.slug ? 'bg-red-600 text-white font-bold shadow-xs' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
        >
          {{ sub.name }}
        </NuxtLink>
      </div>
    </div>

    <!-- Area Filter Card for 'সারা দেশ' -->
    <div v-if="slug === 'saradesh' && areas.length > 0" class="bg-gray-50/90 border border-gray-200/80 rounded-2xl p-4 sm:p-5 shadow-2xs">
      <div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200/60">
        <div class="flex items-center gap-2">
          <span class="text-base">📍</span>
          <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">এলাকা অনুযায়ী সংবাদ ফিল্টার</h3>
        </div>
        <NuxtLink
          v-if="selectedDivisionId || selectedDistrictId"
          to="/category/saradesh"
          class="text-xs font-semibold text-red-600 hover:text-red-700 hover:underline flex items-center gap-1"
        >
          <span>✕</span> ফিল্টার মুছুন
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Division Select -->
        <div>
          <label class="block text-[11px] font-semibold text-gray-500 mb-1">বিভাগ নির্বাচন করুন:</label>
          <select
            :value="selectedDivisionId || ''"
            @change="onDivisionChange($event.target.value)"
            class="w-full text-xs font-medium bg-white border border-gray-200 rounded-xl px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition"
          >
            <option value="">সকল বিভাগ</option>
            <option v-for="div in areas" :key="div.id" :value="div.id">
              {{ div.name }} বিভাগ
            </option>
          </select>
        </div>

        <!-- District Select -->
        <div>
          <label class="block text-[11px] font-semibold text-gray-500 mb-1">জেলা নির্বাচন করুন:</label>
          <select
            :value="selectedDistrictId || ''"
            @change="onDistrictChange($event.target.value)"
            :disabled="!selectedDivision || !selectedDivision.districts?.length"
            class="w-full text-xs font-medium bg-white border border-gray-200 rounded-xl px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition disabled:opacity-50 disabled:bg-gray-100"
          >
            <option value="">সকল জেলা</option>
            <option v-for="dist in (selectedDivision?.districts || [])" :key="dist.id" :value="dist.id">
              {{ dist.name }}
            </option>
          </select>
        </div>

        <!-- Quick Info / Status -->
        <div class="sm:col-span-2 lg:col-span-1 flex items-end">
          <div class="w-full bg-white rounded-xl px-3.5 py-2 border border-gray-200/80 text-xs text-gray-600 flex items-center justify-between">
            <span class="truncate">
              অবস্থান: <strong class="text-gray-900">{{ selectedDistrict?.name ? `${selectedDistrict.name}, ${selectedDivision?.name}` : (selectedDivision?.name ? `${selectedDivision.name} বিভাগ` : 'সমগ্র বাংলাদেশ') }}</strong>
            </span>
            <span class="text-[10px] text-gray-400 font-mono">{{ posts.length }}টি</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Posts Grid Loading Skeleton -->
    <div v-if="pending" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="bg-white rounded-2xl p-4 border border-gray-100 shadow-2xs space-y-3 animate-pulse">
        <div class="h-44 bg-gray-200 rounded-xl"></div>
        <div class="h-5 bg-gray-200 rounded-md w-3/4"></div>
        <div class="space-y-1.5">
          <div class="h-3 bg-gray-200 rounded w-full"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error && posts.length === 0" class="text-center py-16 bg-red-50 rounded-2xl border border-red-100 p-8 space-y-3">
      <div class="text-4xl mb-1">⚠️</div>
      <p class="text-base font-bold text-red-700">সংবাদ লোড করতে সমস্যা হয়েছে</p>
      <p class="text-xs text-red-500">দয়া করে কিছুক্ষণ পর আবার চেষ্টা করুন</p>
      <button @click="refresh()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition">
        পুনরায় চেষ্টা করুন
      </button>
    </div>

    <!-- Empty State -->
    <div v-else-if="posts.length === 0" class="text-center py-20 bg-white rounded-2xl border border-gray-100 p-8 space-y-3">
      <div class="text-4xl mb-1">📂</div>
      <p class="text-base font-bold text-gray-700">এই বিভাগে এখনো কোনো সংবাদ প্রকাশিত হয়নি</p>
      <p class="text-xs text-gray-400">খুব শীঘ্রই এই বিভাগে নতুন সংবাদ যুক্ত করা হবে</p>
      <NuxtLink to="/" class="inline-block mt-2 px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-colors">
        ← প্রচ্ছদে ফিরে যান
      </NuxtLink>
    </div>

    <!-- Loaded Posts Grid -->
    <!-- Loaded Posts Grid with In-Feed Ad Banner -->
    <div v-else class="space-y-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <template v-for="(post, index) in posts" :key="post.id">
          <!-- In-feed Category Sponsor Banner after 3 items -->
          <div
            v-if="index === 3"
            class="sm:col-span-2 md:col-span-3 rounded-2xl overflow-hidden bg-gradient-to-r from-gray-900 via-slate-800 to-gray-900 border border-gray-700/60 p-5 sm:p-6 text-white flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm"
          >
            <div class="flex items-center gap-3.5">
              <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-500 to-red-500 flex items-center justify-center text-xl shadow-xs shrink-0">
                ⭐
              </div>
              <div>
                <span class="text-[9px] uppercase tracking-wider text-amber-400 font-bold block mb-0.5">স্পন্সরড কনটেন্ট · Sponsored</span>
                <h4 class="font-bold text-sm sm:text-base text-white">দৈনিক বাংলা ডিজিটাল বিজ্ঞাপন নেটওয়ার্কে যোগ দিন</h4>
                <p class="text-xs text-gray-300 mt-0.5 line-clamp-1">দেশ ও প্রবাসের কোটি পাঠকের কাছে আপনার ব্র্যান্ডের প্রচারণা পৌঁছে দিন</p>
              </div>
            </div>
            <a
              href="mailto:ads@dainikbangla.com"
              class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs shrink-0"
            >
              বিজ্ঞাপন বুকিং করুন →
            </a>
          </div>

          <div
            class="bg-white rounded-2xl p-4 border border-gray-200/80 shadow-2xs hover:shadow-md transition-all duration-200 group flex flex-col justify-between"
          >
            <NuxtLink :to="`/news/${post.slug}`" class="space-y-3 block">
              <div class="h-44 w-full rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                <img
                  :src="post.featured_image?.original || (typeof post.featured_image === 'string' ? post.featured_image : '') || 'https://images.unsplash.com/photo-1495020689067-958852a7765e?w=600&auto=format&fit=crop'"
                  :alt="post.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                  loading="lazy"
                />
              </div>
              <div class="flex items-center justify-between text-xs pt-1">
                <div class="flex items-center gap-1.5 flex-wrap">
                  <span v-if="post.category?.name" class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md text-[11px]">
                    {{ post.category.name }}
                  </span>
                  <span v-if="post.district?.name || post.division?.name" class="text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.5 rounded-md text-[10px] font-medium flex items-center gap-0.5">
                    <span>📍</span> {{ post.district?.name ? `${post.district.name}, ${post.division?.name || ''}` : `${post.division?.name} বিভাগ` }}
                  </span>
                </div>
                <span v-if="post.published_at" class="text-gray-400 text-[11px]">
                  {{ formatRelativeTime(post.published_at) }}
                </span>
              </div>
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
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  key: route => route.fullPath,
})

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()

// Fetch Areas (8 Divisions & 64 Districts) from CMS for saradesh filtering
const { data: areasResponse } = await useFetch(`${config.public.apiBase}/areas`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'category-areas-list',
  default: () => ({ data: [] }),
})

const areas = computed(() => {
  return Array.isArray(areasResponse.value?.data) ? areasResponse.value.data : []
})

// Dynamic category tree from Headless CMS
const { data: treeResponse } = await useFetch(`${config.public.apiBase}/categories/tree?show_in_nav=1`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'header-categories-tree',
  default: () => ({ data: [] }),
})

const defaultCategories = [
  { id: 1, name: 'জাতীয়', slug: 'national', children: [] },
  { id: 2, name: 'রাজনীতি', slug: 'politics', children: [] },
  { id: 3, name: 'সারা দেশ', slug: 'saradesh', children: [] },
  {
    id: 4,
    name: 'আন্তর্জাতিক',
    slug: 'international',
    children: [
      { id: 41, name: 'মধ্যপ্রাচ্য', slug: 'modhyaprachyo' },
      { id: 42, name: 'যুক্তরাষ্ট্র', slug: 'juktorastro' },
      { id: 43, name: 'এশিয়া', slug: 'asia' },
      { id: 44, name: 'ইউরোপ', slug: 'europ' },
    ],
  },
  {
    id: 5,
    name: 'খেলা',
    slug: 'sports',
    children: [
      { id: 51, name: 'ক্রিকেট', slug: 'cricket' },
      { id: 52, name: 'ফুটবল', slug: 'football' },
      { id: 53, name: 'অন্যান্য', slug: 'sports-others' },
    ],
  },
  { id: 6, name: 'বিনোদন', slug: 'entertainment', children: [] },
  { id: 7, name: 'বাণিজ্য', slug: 'economy', children: [] },
  { id: 8, name: 'মতামত', slug: 'opinion', children: [] },
  { id: 9, name: 'লাইফস্টাইল', slug: 'lifestyle', children: [] },
  { id: 10, name: 'আইন ও আদালত', slug: 'law-and-court', children: [] },
  { id: 11, name: 'প্রযুক্তি', slug: 'technology', children: [] },
  { id: 12, name: 'ভিডিও', slug: 'video', children: [] },
  {
    id: 13,
    name: 'অন্যান্য',
    slug: 'others',
    children: [
      { id: 1301, name: 'শিক্ষা', slug: 'shikkha' },
      { id: 1302, name: 'ধর্ম', slug: 'dhormo' },
      { id: 1303, name: 'স্বাস্থ্য', slug: 'swasthyo' },
      { id: 1304, name: 'কৃষি', slug: 'krishi' },
      { id: 1305, name: 'প্রেস রিলিজ', slug: 'press-release' },
      { id: 1306, name: 'স্টার বিশেষ', slug: 'ster-bisesh' },
      { id: 1307, name: 'আবহাওয়া', slug: 'abhawa' },
      { id: 1308, name: 'চাকরি', slug: 'chakri' },
      { id: 1309, name: 'প্রবাস', slug: 'probas' },
      { id: 1310, name: 'ক্যাম্পাস', slug: 'campus' },
    ],
  },
]

const categoriesTree = computed(() => {
  const remote = treeResponse.value?.data
  return (Array.isArray(remote) && remote.length > 0) ? remote : defaultCategories
})

const selectedDivisionId = computed(() => {
  const val = route.query.division_id
  return val ? Number(val) : null
})

const selectedDistrictId = computed(() => {
  const val = route.query.district_id
  return val ? Number(val) : null
})

const selectedDivision = computed(() => {
  if (!selectedDivisionId.value || !areas.value.length) return null
  return areas.value.find(d => Number(d.id) === selectedDivisionId.value) || null
})

const selectedDistrict = computed(() => {
  if (!selectedDistrictId.value || !selectedDivision.value?.districts?.length) return null
  return selectedDivision.value.districts.find(d => Number(d.id) === selectedDistrictId.value) || null
})

const onDivisionChange = (divId) => {
  if (!divId) {
    router.push({ path: `/category/${slug.value}` })
  } else {
    router.push({
      path: `/category/${slug.value}`,
      query: { division_id: String(divId) },
    })
  }
}

const onDistrictChange = (distId) => {
  if (!distId) {
    if (selectedDivisionId.value) {
      router.push({
        path: `/category/${slug.value}`,
        query: { division_id: String(selectedDivisionId.value) },
      })
    } else {
      router.push({ path: `/category/${slug.value}` })
    }
  } else {
    router.push({
      path: `/category/${slug.value}`,
      query: {
        division_id: String(selectedDivisionId.value),
        district_id: String(distId),
      },
    })
  }
}

const banglaCategoryMap = {
  national: 'জাতীয়',
  politics: 'রাজনীতি',
  saradesh: 'সারা দেশ',
  sports: 'খেলা',
  cricket: 'ক্রিকেট',
  football: 'ফুটবল',
  'sports-others': 'অন্যান্য খেলা',
  international: 'আন্তর্জাতিক',
  antarjatik: 'আন্তর্জাতিক',
  biswa: 'আন্তর্জাতিক',
  economy: 'বাণিজ্য',
  entertainment: 'বিনোদন',
  technology: 'প্রযুক্তি',
  lifestyle: 'লাইফস্টাইল',
  opinion: 'মতামত',
  'law-and-court': 'আইন ও আদালত',
  video: 'ভিডিও',
  others: 'অন্যান্য',
  // World subcategories
  modhyaprachyo: 'মধ্যপ্রাচ্য',
  juktorastro: 'যুক্তরাষ্ট্র',
  asia: 'এশিয়া',
  europ: 'ইউরোপ',
  europe: 'ইউরোপ',
  // Others subcategories
  shikkha: 'শিক্ষা',
  education: 'শিক্ষা',
  dhormo: 'ধর্ম',
  dhorm: 'ধর্ম',
  religion: 'ধর্ম',
  swasthyo: 'স্বাস্থ্য',
  health: 'স্বাস্থ্য',
  krishi: 'কৃষি',
  agriculture: 'কৃষি',
  'press-release': 'প্রেস রিলিজ',
  'pres-reliz': 'প্রেস রিলিজ',
  'ster-bisesh': 'স্টার বিশেষ',
  abhawa: 'আবহাওয়া',
  chakri: 'চাকরি',
  jobs: 'চাকরি',
  probas: 'প্রবাস',
  probash: 'প্রবাস',
  campus: 'ক্যাম্পাস',
}

const slug = computed(() => String(route.params.slug || ''))
const isLatest = computed(() => slug.value === 'latest' || slug.value === 'sorbosesh')

function toBanglaNumber(num) {
  const bnNums = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
  return String(num).replace(/\d/g, d => bnNums[d])
}

function formatRelativeTime(dateStr) {
  if (!dateStr) return 'কিছুক্ষণ আগে'
  try {
    const diffMs = Date.now() - new Date(dateStr).getTime()
    const diffSec = Math.floor(diffMs / 1000)
    const diffMin = Math.floor(diffSec / 60)
    const diffHour = Math.floor(diffMin / 60)
    const diffDay = Math.floor(diffHour / 24)

    if (diffMin < 1) return 'এইমাত্র'
    if (diffMin < 60) return `${toBanglaNumber(diffMin)} মিনিট আগে`
    if (diffHour < 24) return `${toBanglaNumber(diffHour)} ঘণ্টা আগে`
    if (diffDay === 1) return 'গতকাল'
    if (diffDay < 7) return `${toBanglaNumber(diffDay)} দিন আগে`
    return new Date(dateStr).toLocaleDateString('bn-BD', { month: 'short', day: 'numeric' })
  } catch {
    return 'কিছুক্ষণ আগে'
  }
}

// Fetch Category detail from CMS so title is always available even if empty (skip if latest)
const { data: catResponse, refresh: refreshCat } = useFetch(() => `${config.public.apiBase}/categories/${slug.value}`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: `category-info-${slug.value}`,
  watch: [() => route.params.slug],
  lazy: true,
  default: () => null,
  immediate: !isLatest.value,
  onResponseError({ response }) {
    if (!isLatest.value) {
      console.warn(`[Category] CMS category info not found for "${slug.value}" (${response?.status || 'network error'})`)
    }
  },
})

const subcategories = computed(() => {
  const cat = catResponse.value?.data
  if (cat?.children && cat.children.length > 0) {
    return cat.children
  }
  if (cat?.parent?.children && cat.parent.children.length > 0) {
    return cat.parent.children
  }
  const currentSlug = slug.value
  const top = categoriesTree.value.find(c => c.slug === currentSlug)
  if (top?.children?.length > 0) {
    return top.children
  }
  for (const parent of categoriesTree.value) {
    if (parent.children?.some(c => c.slug === currentSlug)) {
      return parent.children
    }
  }
  return []
})

const parentCategorySlug = computed(() => {
  const cat = catResponse.value?.data
  if (cat?.parent?.slug) return cat.parent.slug
  const currentSlug = slug.value
  for (const parent of categoriesTree.value) {
    if (parent.slug === currentSlug) return parent.slug
    if (parent.children?.some(c => c.slug === currentSlug)) {
      return parent.slug
    }
  }
  return currentSlug
})

const parentCategoryName = computed(() => {
  const cat = catResponse.value?.data
  if (cat?.parent?.name) return cat.parent.name
  const currentSlug = slug.value
  for (const parent of categoriesTree.value) {
    if (parent.slug === currentSlug) return parent.name
    if (parent.children?.some(c => c.slug === currentSlug)) {
      return parent.name
    }
  }
  return banglaCategoryMap[parentCategorySlug.value] || 'ক্যাটাগরি'
})

// If slug is latest or sorbosesh, fetch all posts across all categories without category filter
const postsEndpoint = computed(() => {
  if (isLatest.value) {
    return `${config.public.apiBase}/posts?per_page=30`
  }
  const params = new URLSearchParams()
  params.set('category', slug.value)
  if (route.query.division_id) params.set('division_id', String(route.query.division_id))
  if (route.query.district_id) params.set('district_id', String(route.query.district_id))
  if (route.query.division) params.set('division', String(route.query.division))
  if (route.query.district) params.set('district', String(route.query.district))
  return `${config.public.apiBase}/posts?${params.toString()}`
})

const { data: postsResponse, pending, error, refresh: refreshPosts } = useFetch(postsEndpoint, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: () => `category-posts-${route.fullPath}`,
  watch: [
    () => route.params.slug,
    () => route.query.division_id,
    () => route.query.district_id,
    () => route.query.division,
    () => route.query.district,
  ],
  lazy: true,
  default: () => ({ data: [] }),
  onResponseError({ response, error: resError }) {
    console.error(`[Category] Failed to load posts for "${slug.value}":`, response?.status, resError)
  },
})

const refresh = () => {
  refreshCat()
  refreshPosts()
}

watch(() => route.fullPath, (newPath, oldPath) => {
  if (newPath && newPath !== oldPath && process.client) {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    refresh()
  }
})

const posts = computed(() => {
  const res = postsResponse.value
  if (Array.isArray(res?.data)) return res.data
  if (Array.isArray(res)) return res
  return []
})

const categoryTitle = computed(() => {
  if (isLatest.value) return 'সর্বশেষ সংবাদ'
  const baseTitle = catResponse.value?.data?.name || banglaCategoryMap[slug.value] || posts.value[0]?.category?.name || slug.value
  if (slug.value === 'saradesh') {
    if (selectedDistrict.value && selectedDivision.value) {
      return `${baseTitle} — ${selectedDivision.value.name} (${selectedDistrict.value.name} জেলা)`
    }
    if (selectedDivision.value) {
      return `${baseTitle} — ${selectedDivision.value.name} বিভাগ`
    }
  }
  return baseTitle
})

useHead({
  title: computed(() => `${categoryTitle.value} — ${config.public.siteName || 'দৈনিক বাংলা'}`),
})
</script>
