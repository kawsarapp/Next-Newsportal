<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-800">এআই নিউজরুম (AI News Assistant)</h2>
          <p class="text-sm text-gray-500 mt-0.5">কৃত্রিম বুদ্ধিমত্তার সাহায্যে দ্রুত শিরোনাম, সারাংশ ও সোশ্যাল ক্যাপশন তৈরি</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Input Sandbox -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-base">সংবাদের মূল খসড়া বা তথ্যসূত্র</h3>
          <textarea
            v-model="sourceText"
            rows="8"
            placeholder="এখানে যেকোনো প্রাথমিক তথ্যসূত্র বা খসড়া লেখা দিন..."
            class="w-full text-sm p-4 border rounded-xl outline-none focus:border-red-500 bg-gray-50/50"
          ></textarea>

          <div class="flex flex-wrap gap-2 pt-2">
            <button
              @click="generate('headlines')"
              :disabled="loading || !sourceText"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-40 text-white rounded-xl text-xs font-semibold shadow-sm transition-all"
            >
              ✨ শিরোনাম আইডিয়া
            </button>
            <button
              @click="generate('summary')"
              :disabled="loading || !sourceText"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-40 text-white rounded-xl text-xs font-semibold shadow-sm transition-all"
            >
              📄 সারসংক্ষেপ বানান
            </button>
            <button
              @click="generate('social')"
              :disabled="loading || !sourceText"
              class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 text-white rounded-xl text-xs font-semibold shadow-sm transition-all"
            >
              📱 সোশ্যাল ক্যাপশন
            </button>
          </div>
        </div>

        <!-- AI Output Display -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between border-b pb-3 mb-4">
              <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                <span>🤖</span> এআই ফলাফল
              </h3>
              <span v-if="loading" class="text-xs text-red-600 animate-pulse font-medium">প্রসেসিং হচ্ছে...</span>
            </div>

            <div v-if="!result" class="text-center py-16 text-gray-400 text-sm">
              বামপাশের বক্সে লেখা দিয়ে যেকোনো এআই টুলে ক্লিক করুন
            </div>

            <div v-else class="space-y-3 bg-gray-50 p-4 rounded-xl text-sm text-gray-800 whitespace-pre-line leading-relaxed">
              {{ result }}
            </div>
          </div>

          <div v-if="result" class="pt-4 border-t mt-4 flex justify-end">
            <button @click="copyResult" class="text-xs text-red-600 font-semibold hover:underline">
              📋 ক্লিপবোর্ডে কপি করুন
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const sourceText = ref('')
const result = ref('')
const loading = ref(false)

const generate = (type) => {
  loading.value = true
  result.value = ''

  setTimeout(() => {
    loading.value = false
    if (type === 'headlines') {
      result.value = `১. ${sourceText.value.slice(0, 30)}: নতুন দিগন্ত উন্মোচিত\n২. জরুরি পদক্ষেপে গুরুত্বপূর্ণ সিদ্ধান্ত গ্রহণ\n৩. বিস্তারিত আসছে: বিশেষ প্রতিবেদন`
    } else if (type === 'summary') {
      result.value = `সারসংক্ষেপ:\nতথ্যপ্রযুক্তি ও উন্নয়নের ক্ষেত্রে নতুন উদ্যোগ বাস্তবায়িত হয়েছে। এর ফলে সাধারণ নাগরিকেরা উপকৃত হবেন এবং কার্যক্রমে গতিশীলতা আসবে।`
    } else {
      result.value = `🔥 ব্রেকিং নিউজ!\n${sourceText.value.slice(0, 50)}...\n\n👉 বিস্তারিত পড়ুন আমাদের পোর্টালে!\n#Bangladesh #News #LatestNews`
    }
  }, 700)
}

const copyResult = () => {
  navigator.clipboard.writeText(result.value)
  alert('ফলাফল কপি করা হয়েছে!')
}
</script>
