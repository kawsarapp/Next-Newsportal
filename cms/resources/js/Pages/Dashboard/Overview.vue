<template>
  <AppLayout>
    <div class="p-6 space-y-6">

      <!-- Welcome Bar -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-800">সুপ্রভাত, {{ $page.props.auth?.user?.name ?? 'ইউজার' }} 👋</h2>
          <p class="text-sm text-gray-500 mt-0.5">{{ today }} — নিউজরুম ওভারভিউ</p>
        </div>
        <div class="flex gap-2">
          <Link :href="route('dashboard.posts.create')" class="btn-primary">
            + নতুন নিউজ
          </Link>
        </div>
      </div>

      <!-- Stat Cards Row -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <StatCard
          v-for="stat in stats"
          :key="stat.label"
          :label="stat.label"
          :value="stat.value"
          :icon="stat.icon"
          :color="stat.color"
          :change="stat.change"
        />
      </div>

      <!-- Main Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left — Pending + Recent -->
        <div class="lg:col-span-2 space-y-6">

          <!-- Pending Approval -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                অনুমোদনের অপেক্ষায়
              </h3>
              <Link :href="route('dashboard.editorial.review-queue')" class="text-xs text-red-600 hover:underline">সব দেখুন</Link>
            </div>
            <div v-if="pendingPosts.length === 0" class="text-center py-8 text-gray-400 text-sm">
              কোনো নিউজ অপেক্ষায় নেই ✓
            </div>
            <div v-else class="space-y-3">
              <div
                v-for="post in pendingPosts"
                :key="post.id"
                class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors group"
              >
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0 overflow-hidden">
                  <img v-if="post.thumbnail" :src="post.thumbnail" class="w-full h-full object-cover" />
                  <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-xs">IMG</div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-800 truncate">{{ post.title }}</p>
                  <p class="text-xs text-gray-500 mt-0.5">{{ post.author }} · {{ post.submitted_ago }}</p>
                </div>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <Link :href="`/dashboard/posts/${post.id}/edit`" class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded hover:bg-blue-100">রিভিউ</Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Published -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-800">সম্প্রতি প্রকাশিত</h3>
              <Link :href="route('dashboard.posts.index', { status: 'published' })" class="text-xs text-red-600 hover:underline">সব দেখুন</Link>
            </div>
            <div class="space-y-3">
              <div v-for="post in recentPosts" :key="post.id" class="flex items-center gap-3 py-2 border-b border-gray-50 last:border-0">
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-800 truncate font-medium">{{ post.title }}</p>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-gray-400">{{ post.category }}</span>
                    <span class="text-xs text-gray-400">·</span>
                    <span class="text-xs text-gray-400">{{ post.published_ago }}</span>
                  </div>
                </div>
                <div class="text-right flex-shrink-0">
                  <p class="text-sm font-semibold text-gray-700">{{ post.views.toLocaleString('bn-BD') }}</p>
                  <p class="text-xs text-gray-400">ভিউ</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">

          <!-- Breaking News Status -->
          <div class="bg-red-600 rounded-2xl p-5 text-white">
            <h3 class="font-semibold mb-3 flex items-center gap-2">
              <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
              ব্রেকিং নিউজ
            </h3>
            <div v-if="breakingNews.length === 0" class="text-red-200 text-sm">কোনো ব্রেকিং নিউজ নেই</div>
            <div v-else class="space-y-2">
              <p v-for="b in breakingNews" :key="b.id" class="text-sm truncate">• {{ b.title }}</p>
            </div>
            <Link :href="route('dashboard.posts.index', { breaking: 1 })" class="mt-3 block text-xs text-red-200 hover:text-white">পরিচালনা করুন →</Link>
          </div>

          <!-- Post Counts by Status -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">নিউজের অবস্থা</h3>
            <div class="space-y-3">
              <StatusBar v-for="s in statusCounts" :key="s.label" :label="s.label" :count="s.count" :color="s.color" :total="totalPosts" />
            </div>
          </div>

          <!-- Reporter Activity -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-semibold text-gray-800">রিপোর্টার র‍্যাংকিং</h3>
              <span class="text-xs text-gray-400">এই মাস</span>
            </div>
            <div class="space-y-3">
              <div v-for="(r, i) in topReporters" :key="r.id" class="flex items-center gap-3">
                <span class="text-sm font-bold text-gray-400 w-5">{{ i + 1 }}</span>
                <div class="w-7 h-7 bg-gray-200 rounded-full flex-shrink-0 flex items-center justify-center text-xs font-semibold text-gray-600">
                  {{ r.name[0] }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-700 truncate">{{ r.name }}</p>
                </div>
                <span class="text-sm font-bold text-red-600">{{ r.posts }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <Link v-for="action in quickActions" :key="action.label" :href="route(action.route)" class="bg-white border border-gray-100 rounded-xl p-4 hover:border-red-200 hover:shadow-sm transition-all text-center group">
          <div class="text-2xl mb-2">{{ action.emoji }}</div>
          <p class="text-xs font-medium text-gray-700 group-hover:text-red-600">{{ action.label }}</p>
        </Link>
      </div>

    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'
import StatusBar from '@/Components/Dashboard/StatusBar.vue'

const props = defineProps({
  stats: { type: Array, default: () => [] },
  pendingPosts: { type: Array, default: () => [] },
  recentPosts: { type: Array, default: () => [] },
  breakingNews: { type: Array, default: () => [] },
  statusCounts: { type: Array, default: () => [] },
  topReporters: { type: Array, default: () => [] },
})

const today = new Date().toLocaleDateString('bn-BD', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })

const totalPosts = computed(() => props.statusCounts.reduce((s, x) => s + x.count, 0))

const quickActions = [
  { label: 'নতুন নিউজ',       emoji: '✍️', route: 'dashboard.posts.create' },
  { label: 'ছবি আপলোড',       emoji: '📷', route: 'dashboard.media.index' },
  { label: 'রিপোর্টার যোগ',   emoji: '👤', route: 'dashboard.reporters.index' },
  { label: 'সেটিংস',          emoji: '⚙️', route: 'dashboard.settings.index' },
]
</script>
