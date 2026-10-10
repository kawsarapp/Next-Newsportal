<template>
  <div class="flex items-center gap-3">
    <span class="text-xs text-gray-600 w-24 truncate flex-shrink-0">{{ label }}</span>
    <div class="flex-1 bg-gray-100 rounded-full h-2">
      <div :class="['h-2 rounded-full transition-all duration-500', barColor]" :style="{ width: pct + '%' }"></div>
    </div>
    <span class="text-xs font-semibold text-gray-700 w-8 text-right flex-shrink-0">{{ count }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ label: String, count: Number, color: String, total: Number })
const pct = computed(() => props.total ? Math.round((props.count / props.total) * 100) : 0)
const barColor = computed(() => ({
  green:  'bg-green-500',
  yellow: 'bg-yellow-400',
  blue:   'bg-blue-500',
  purple: 'bg-purple-500',
  gray:   'bg-gray-400',
  red:    'bg-red-500',
}[props.color] ?? 'bg-gray-400'))
</script>
