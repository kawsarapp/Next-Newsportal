<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 border border-gray-200/80 shadow-lg text-center space-y-4">
      <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-3xl font-black shadow-xs">
        {{ error?.statusCode === 404 ? '404' : '⚠️' }}
      </div>
      <h2 class="text-xl font-extrabold text-gray-900">
        {{ error?.statusCode === 404 ? 'পৃষ্ঠাটি খুঁজে পাওয়া যায়নি' : 'একটি সমস্যা দেখা দিয়েছে' }}
      </h2>
      <p class="text-xs text-gray-500 leading-relaxed">
        {{ error?.statusMessage || (error?.statusCode === 404 ? 'আপনি যে পাতাটিতে যাওয়ার চেষ্টা করছেন তা মুছে ফেলা হয়েছে অথবা ঠিকানা ভুল।' : 'দুঃখিত, প্রযুক্তিগত সমস্যার কারণে পৃষ্ঠাটি লোড করা যায়নি। কিছুক্ষণ পর আবার চেষ্টা করুন।') }}
      </p>
      <div class="pt-4 flex items-center justify-center gap-3">
        <button
          @click="handleClearError"
          class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition shadow-sm"
        >
          ← প্রচ্ছদে ফিরে যান
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  error: {
    type: Object,
    default: () => ({ statusCode: 404 }),
  },
})

const handleClearError = () => {
  clearError({ redirect: '/' })
}
</script>
