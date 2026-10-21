<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Sidebar + Main -->
    <div class="flex h-screen overflow-hidden">
      <!-- Sidebar -->
      <aside
        :class="[
          'flex flex-col bg-gray-900 text-white transition-all duration-200 ease-in-out flex-shrink-0',
          sidebarOpen ? 'w-64' : 'w-16'
        ]"
      >
        <!-- Logo -->
        <div class="flex items-center h-16 px-4 border-b border-gray-700">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">N</div>
            <span v-if="sidebarOpen" class="font-bold text-sm truncate">{{ portalName }}</span>
          </div>
        </div>

        <!-- Nav Items -->
        <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
          <NavItem v-for="item in navItems" :key="item.route" :item="item" :collapsed="!sidebarOpen" />
        </nav>

        <!-- Collapse Button -->
        <div class="border-t border-gray-700 p-3">
          <button
            @click="sidebarOpen = !sidebarOpen"
            class="w-full flex items-center justify-center py-2 rounded-lg hover:bg-gray-700 text-gray-400 hover:text-white transition-colors"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path v-if="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7M18 19l-7-7 7-7"/>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M6 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </aside>

      <!-- Main Area -->
      <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
          <!-- Page Title -->
          <h1 class="text-lg font-semibold text-gray-800">{{ $page.props.pageTitle ?? 'Dashboard' }}</h1>

          <!-- Right Actions -->
          <div class="flex items-center gap-4">
            <!-- Quick Add Post -->
            <Link :href="route('dashboard.posts.create')" class="btn-primary text-sm py-1.5 px-3">
              + নতুন নিউজ
            </Link>

            <!-- Notifications -->
            <button class="relative p-2 text-gray-500 hover:text-gray-700">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User Menu -->
            <div class="relative" ref="userMenuRef">
              <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 text-sm">
                <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                  {{ userInitials }}
                </div>
                <span class="hidden md:block text-gray-700">{{ $page.props.auth?.user?.name ?? 'ইউজার' }}</span>
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>

              <!-- Dropdown -->
              <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                <Link :href="route('dashboard.profile.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">প্রোফাইল</Link>
                <hr class="my-1">
                <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">লগ আউট</Link>
              </div>
            </div>
          </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto">
          <slot />
        </main>
      </div>
    </div>

    <!-- Flash Messages -->
    <FlashMessage />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavItem from '@/Components/Sidebar/NavItem.vue'
import FlashMessage from '@/Components/UI/FlashMessage.vue'

const page = usePage()
const sidebarOpen = ref(true)
const userMenuOpen = ref(false)
const userMenuRef = ref(null)

const portalName = computed(() => page.props.portal?.name ?? 'Newsportal CMS')
const userInitials = computed(() => {
  const name = page.props.auth?.user?.name ?? 'U'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

// Close user menu on outside click
const handleClick = (e) => {
  if (userMenuRef.value && !userMenuRef.value.contains(e.target)) {
    userMenuOpen.value = false
  }
}
onMounted(() => document.addEventListener('click', handleClick))
onUnmounted(() => document.removeEventListener('click', handleClick))

const navItems = [
  { label: 'ওভারভিউ',       icon: 'home',       route: 'dashboard.overview',    permission: null },
  { label: 'নিউজ',           icon: 'newspaper',  route: 'dashboard.posts.index', permission: 'post.view' },
  { label: 'ফটাকার্ড ও সোশ্যাল', icon: 'camera', route: 'dashboard.social-publisher.index', permission: null },
  { label: 'Subeditor24 ইন্টিগ্রেশন', icon: 'bolt', route: 'dashboard.subeditor.index', permission: null },
  { label: 'এডিটোরিয়াল',    icon: 'workflow',   route: 'dashboard.editorial.index', permission: 'editorial.review' },
  { label: 'ক্যাটাগরি',      icon: 'folder',     route: 'dashboard.categories.index', permission: 'category.view' },
  { label: 'রিপোর্টার',      icon: 'users',      route: 'dashboard.reporters.index', permission: 'reporter.view' },
  { label: 'মিডিয়া',        icon: 'image',      route: 'dashboard.media.index', permission: 'media.view' },
  { label: 'SEO',            icon: 'search',     route: 'dashboard.seo.index',   permission: 'seo.view' },
  { label: 'সোশ্যাল মিডিয়া', icon: 'share',     route: 'dashboard.social.index', permission: 'social.view' },
  { label: 'AI নিউজরুম',     icon: 'sparkles',   route: 'dashboard.ai.index',    permission: 'ai.use' },
  { label: 'অ্যানালিটিক্স',  icon: 'chart',      route: 'dashboard.analytics.index', permission: 'analytics.view' },
  { label: 'বিজ্ঞাপন',       icon: 'ad',         route: 'dashboard.ads.index',   permission: 'ads.view' },
  { label: 'পাঠক',           icon: 'audience',   route: 'dashboard.audience.index', permission: null },
  { label: 'ফ্রন্টএন্ড',    icon: 'layout',     route: 'dashboard.frontend.index', permission: 'settings.view' },
  { label: 'ব্যবহারকারী',    icon: 'user-cog',   route: 'dashboard.users.index', permission: 'user.view' },
  { label: 'সেটিংস',         icon: 'settings',   route: 'dashboard.settings.index', permission: 'settings.view' },
  { label: 'মডিউল ও প্লাগইন',icon: 'layout',     route: 'dashboard.modules.index',  permission: 'settings.view' },
  { label: 'অডিট লগ',        icon: 'shield',     route: 'dashboard.audit.index', permission: 'audit.view' },
]
</script>

<style scoped>
.btn-primary {
  background-color: #dc2626;
  color: white;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: background-color 0.15s;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}
.btn-primary:hover { background-color: #b91c1c; }
</style>
