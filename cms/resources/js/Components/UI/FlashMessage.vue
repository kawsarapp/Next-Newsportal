<template>
  <transition name="flash">
    <div v-if="message" :class="['fixed bottom-6 right-6 z-50 px-5 py-3 rounded-xl shadow-lg flex items-center gap-3 text-sm font-medium', typeClasses]">
      <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path v-if="type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        <path v-else-if="type === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      {{ message }}
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const message = ref(null)
const type = ref('success')

const typeClasses = {
  success: 'bg-green-600 text-white',
  error:   'bg-red-600 text-white',
  info:    'bg-blue-600 text-white',
}

watch(() => page.props.flash, (flash) => {
  if (flash?.success) { message.value = flash.success; type.value = 'success' }
  else if (flash?.error)   { message.value = flash.error; type.value = 'error' }
  else if (flash?.info)    { message.value = flash.info; type.value = 'info' }

  if (message.value) {
    setTimeout(() => { message.value = null }, 4000)
  }
}, { immediate: true, deep: true })
</script>

<style scoped>
.flash-enter-active, .flash-leave-active { transition: all 0.3s ease; }
.flash-enter-from, .flash-leave-to { opacity: 0; transform: translateY(10px); }
</style>
