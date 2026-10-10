<template>
  <Link
    v-if="!collapsed"
    :href="item.route ? route(item.route) : '#'"
    :class="[
      'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150',
      isActive
        ? 'bg-red-600 text-white'
        : 'text-gray-400 hover:bg-gray-700 hover:text-white'
    ]"
  >
    <NavIcon :name="item.icon" class="w-5 h-5 flex-shrink-0" />
    <span class="truncate">{{ item.label }}</span>
  </Link>

  <!-- Collapsed mode — icon only with tooltip -->
  <div v-else class="relative group">
    <Link
      :href="item.route ? route(item.route) : '#'"
      :class="[
        'flex items-center justify-center w-10 h-10 rounded-lg transition-all',
        isActive ? 'bg-red-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white'
      ]"
    >
      <NavIcon :name="item.icon" class="w-5 h-5" />
    </Link>
    <!-- Tooltip -->
    <div class="absolute left-14 top-1/2 -translate-y-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
      {{ item.label }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NavIcon from '@/Components/Sidebar/NavIcon.vue'

const props = defineProps({
  item: { type: Object, required: true },
  collapsed: { type: Boolean, default: false },
})

const page = usePage()
const isActive = computed(() => {
  try {
    return route().current(props.item.route + '*')
  } catch {
    return false
  }
})
</script>
