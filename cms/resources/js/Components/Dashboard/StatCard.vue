<template>
  <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
    <div class="flex items-start justify-between">
      <div>
        <p class="text-sm text-gray-500 font-medium">{{ label }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">{{ value?.toLocaleString('bn-BD') ?? 0 }}</p>
        <p v-if="change !== undefined" :class="['text-xs mt-1 font-medium', change >= 0 ? 'text-green-600' : 'text-red-500']">
          {{ change >= 0 ? '↑' : '↓' }} {{ Math.abs(change) }}% আগের দিনের তুলনায়
        </p>
      </div>
      <div :class="['w-10 h-10 rounded-xl flex items-center justify-center text-lg', colorBg]">
        {{ icon }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  label:  { type: String,  required: true },
  value:  { type: Number,  default: 0 },
  icon:   { type: String,  default: '📊' },
  color:  { type: String,  default: 'red' },
  change: { type: Number,  default: undefined },
})
const colorBg = computed(() => ({
  red:    'bg-red-50',
  blue:   'bg-blue-50',
  green:  'bg-green-50',
  yellow: 'bg-yellow-50',
  purple: 'bg-purple-50',
}[props.color] ?? 'bg-gray-50'))
</script>
