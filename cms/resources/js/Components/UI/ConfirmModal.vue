<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 animate-scale-in">
      <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>

      <h3 class="text-lg font-bold text-gray-900 mb-2">{{ title }}</h3>
      <p class="text-sm text-gray-500 mb-6 leading-relaxed">{{ message }}</p>

      <div class="flex items-center justify-end gap-3">
        <button
          type="button"
          @click="$emit('cancel')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
        >
          বাতিল
        </button>
        <button
          type="button"
          @click="$emit('confirm')"
          class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm"
        >
          {{ confirmLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'নিশ্চিতকরণ',
  },
  message: {
    type: String,
    required: true,
  },
  confirmLabel: {
    type: String,
    default: 'নিশ্চিত করুন',
  },
})

defineEmits(['confirm', 'cancel'])
</script>
