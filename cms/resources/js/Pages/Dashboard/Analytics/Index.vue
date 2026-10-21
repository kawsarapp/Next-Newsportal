<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Flash Messages -->
      <div v-if="$page.props.flash?.success" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between">
        <span>✓ {{ $page.props.flash.success }}</span>
      </div>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">অ্যানালিটিক্স ড্যাশবোর্ড</span>
            <h2 class="text-xl font-bold text-gray-800">অ্যানালিটিক্স ও পাঠক রিপোর্টস</h2>
          </div>
          <p class="text-sm text-gray-500 mt-0.5">রিয়েলটাইম পাঠক সংখ্যা, পেজভিউ ও ট্রাফিক পারফরম্যান্স বিশ্লেষণ</p>
        </div>

        <a
          href="/dashboard/analytics/export"
          class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
        >
          <span>📥</span> এক্সেল / CSV রিপোর্ট ডাউনলোড
        </a>
      </div>

      <!-- Live Counters -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">লাইভ ভিজিটর (রিয়েলটাইম)</span>
          <p class="text-3xl font-extrabold text-emerald-600 mt-1 flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
            {{ toBanglaNum(live_visitors) }} জন
          </p>
          <span class="text-[11px] text-gray-400 mt-1 block">গত ৫ মিনিটে সক্রিয় পাঠক</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">আজকের মোট পেজভিউ</span>
          <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ toBanglaNum(today_views) }}</p>
          <span class="text-[11px] text-gray-400 mt-1 block">আজ প্রকাশিত সংবাদের ভিউ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">সর্বমোট পাঠক পেজভিউ</span>
          <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ toBanglaNum(total_views) }}</p>
          <span class="text-[11px] text-gray-400 mt-1 block">পোর্টালের মোট অর্জিত ভিউ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <span class="text-xs text-gray-400 block font-medium">আজকের প্রকাশিত নিউজ</span>
          <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ toBanglaNum(today_posts) }} টি</p>
          <span class="text-[11px] text-gray-400 mt-1 block">মোট নিবন্ধ: {{ toBanglaNum(total_posts) }} টি</span>
        </div>
      </div>

      <!-- Traffic Sources & Top Categories -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Traffic Sources -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-sm border-b pb-3 flex items-center justify-between">
            <span>শীর্ষ ট্রাফিক সোর্স</span>
            <span class="text-xs text-gray-400 font-normal">অটো-অ্যানালাইসিস</span>
          </h3>
          <div class="space-y-4">
            <div v-for="source in traffic_sources" :key="source.name">
              <div class="flex justify-between text-xs font-semibold mb-1">
                <span class="text-gray-700">{{ source.name }}</span>
                <span class="text-gray-900 font-bold">{{ toBanglaNum(source.percentage) }}%</span>
              </div>
              <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div :class="['h-full rounded-full', source.color]" :style="{ width: `${source.percentage}%` }"></div>
              </div>
            </div>
          </div>

          <!-- Device Breakdown -->
          <div class="pt-4 border-t border-gray-100">
            <h4 class="font-bold text-gray-800 text-xs mb-3">ডিভাইস ব্যবহারের অনুপাত</h4>
            <div class="grid grid-cols-3 gap-2 text-center">
              <div v-for="dev in device_stats" :key="dev.name" class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                <p class="text-xs text-gray-500 truncate">{{ dev.name.split(' ')[0] }}</p>
                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ toBanglaNum(dev.percentage) }}%</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Top Performing Categories -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-sm border-b pb-3 flex items-center justify-between">
            <span>সর্বাধিক পঠিত ক্যাটাগরি</span>
            <span class="text-xs text-gray-400 font-normal">রিয়েল ভিউ পরিসংখ্যান</span>
          </h3>

          <div v-if="top_categories.length === 0" class="py-6 text-center text-xs text-gray-400">
            কোনো ক্যাটাগরি ডেটা পাওয়া যায়নি।
          </div>

          <div v-else class="space-y-2.5">
            <div
              v-for="(cat, idx) in top_categories"
              :key="cat.id"
              class="flex items-center justify-between text-xs p-3 bg-gray-50 hover:bg-gray-100/70 rounded-xl border border-gray-100 transition-colors"
            >
              <div class="flex items-center gap-2.5">
                <span class="w-5 h-5 rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center text-[10px]">
                  {{ toBanglaNum(idx + 1) }}
                </span>
                <span class="font-semibold text-gray-800">{{ cat.name }}</span>
              </div>
              <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-md border border-red-100">
                {{ toBanglaNum(cat.posts_sum_view_count || 0) }} ভিউ
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Read Posts -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="font-bold text-gray-800 text-base">সর্বাধিক পঠিত শীর্ষ সংবাদ (Top Stories)</h3>
            <p class="text-xs text-gray-400 mt-0.5">সবচেয়ে বেশি ভিউ পাওয়া সংবাদের রিয়েল পরিসংখ্যান</p>
          </div>
          <span class="text-xs text-gray-500 font-medium">র‍্যাঙ্কিং শীর্ষ ৫</span>
        </div>

        <div v-if="top_posts.length === 0" class="p-8 text-center text-xs text-gray-400">
          এখনো কোনো প্রকাশিত সংবাদ পাওয়া যায়নি।
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 bg-gray-50/70 uppercase border-b border-gray-100">
              <tr>
                <th class="px-5 py-3 w-16 text-center">ক্রম</th>
                <th class="px-5 py-3">সংবাদের শিরোনাম</th>
                <th class="px-5 py-3">প্রকাশের তারিখ</th>
                <th class="px-5 py-3 text-right">মোট ভিউয়ার সংখ্যা</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr v-for="(post, index) in top_posts" :key="post.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-3 text-center">
                  <span
                    :class="[
                      'w-6 h-6 inline-flex items-center justify-center rounded-full text-xs font-bold',
                      index === 0 ? 'bg-amber-100 text-amber-800' :
                      index === 1 ? 'bg-gray-200 text-gray-800' :
                      index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-600'
                    ]"
                  >
                    {{ toBanglaNum(index + 1) }}
                  </span>
                </td>
                <td class="px-5 py-3">
                  <span class="font-medium text-gray-800 hover:text-red-600 cursor-pointer transition-colors line-clamp-1">
                    {{ post.title }}
                  </span>
                </td>
                <td class="px-5 py-3 text-xs text-gray-400 whitespace-nowrap">
                  {{ formatDate(post.published_at) }}
                </td>
                <td class="px-5 py-3 text-right whitespace-nowrap">
                  <span class="font-bold text-red-600 bg-red-50 px-2.5 py-1 rounded-lg border border-red-100 text-xs">
                    👁️ {{ toBanglaNum(post.view_count) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Top Reporters -->
      <div v-if="top_reporters && top_reporters.length > 0" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-gray-800 text-base border-b pb-3">শীর্ষ পারফর্মিং সাংবাদিক / প্রতিবেদক</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
          <div
            v-for="rep in top_reporters"
            :key="rep.id"
            class="p-4 rounded-xl border border-gray-100 bg-gray-50/60 text-center space-y-2 hover:border-gray-200 transition-colors"
          >
            <div class="w-12 h-12 mx-auto rounded-full bg-red-100 text-red-700 font-bold flex items-center justify-center text-base">
              {{ rep.name.charAt(0) }}
            </div>
            <div>
              <h4 class="font-bold text-xs text-gray-800 truncate">{{ rep.name }}</h4>
              <p class="text-[11px] text-gray-400 capitalize">{{ rep.reporter_type || 'প্রতিবেদক' }}</p>
            </div>
            <div class="pt-2 border-t border-gray-200/60 flex justify-around text-xs">
              <div>
                <span class="text-gray-400 block text-[10px]">নিবন্ধ</span>
                <span class="font-bold text-gray-700">{{ toBanglaNum(rep.posts_count || 0) }}</span>
              </div>
              <div>
                <span class="text-gray-400 block text-[10px]">ভিউ</span>
                <span class="font-bold text-red-600">{{ toBanglaNum(rep.posts_sum_view_count || 0) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  today_views: {
    type: Number,
    default: 0
  },
  total_views: {
    type: Number,
    default: 0
  },
  today_posts: {
    type: Number,
    default: 0
  },
  total_posts: {
    type: Number,
    default: 0
  },
  live_visitors: {
    type: Number,
    default: 0
  },
  top_categories: {
    type: Array,
    default: () => []
  },
  top_posts: {
    type: Array,
    default: () => []
  },
  top_reporters: {
    type: Array,
    default: () => []
  },
  device_stats: {
    type: Array,
    default: () => []
  },
  traffic_sources: {
    type: Array,
    default: () => []
  }
})

const toBanglaNum = (num) => {
  if (num === null || num === undefined) return '০'
  const bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯']
  return Number(num).toLocaleString('en-US').replace(/\d/g, d => bn[d])
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('bn-BD', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (e) {
    return dateStr
  }
}
</script>
