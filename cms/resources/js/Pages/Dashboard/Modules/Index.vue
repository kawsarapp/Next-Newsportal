<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">মডিউল ও প্লাগইন ম্যানেজার</h2>
          <p class="text-sm text-gray-500 mt-0.5">ওয়ার্ডপ্রেসের মতো জিপ আপলোড করে নতুন ফিচার যুক্ত ও পরিচালনা করুন</p>
        </div>
        <label class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2 cursor-pointer">
          <span>📦</span> নতুন মডিউল আপলোড (.zip)
          <input type="file" @change="uploadZip" accept=".zip" class="hidden" />
        </label>
      </div>

      <!-- Upload Loading Banner -->
      <div v-if="uploading" class="p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl text-sm flex items-center gap-3">
        <span class="w-4 h-4 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></span>
        মডিউল জিপ এক্সট্রাক্ট এবং ইন্সটল করা হচ্ছে...
      </div>

      <!-- Modules Grid -->
      <div v-if="modules.length === 0" class="bg-white rounded-2xl p-16 text-center text-gray-400 border border-gray-100 shadow-sm">
        <div class="text-5xl mb-3">🧩</div>
        <h3 class="text-lg font-bold text-gray-700 mb-1">কোনো কাস্টম মডিউল নেই</h3>
        <p class="text-xs text-gray-400">উপরের বাটন দিয়ে তৈরি করা .zip মডিউল আপলোড করুন</p>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
          v-for="item in modules"
          :key="item.slug"
          class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between space-y-4"
        >
          <div>
            <div class="flex items-start justify-between">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl font-bold">
                  {{ item.icon === 'poll' ? '📊' : (item.icon === 'cricket' ? '🏏' : '🧩') }}
                </div>
                <div>
                  <h3 class="font-bold text-gray-800 text-sm">{{ item.name }}</h3>
                  <span class="text-[11px] text-gray-400 font-mono">v{{ item.version }}</span>
                </div>
              </div>

              <!-- Toggle Active Switch -->
              <button
                @click="toggle(item)"
                :class="[
                  'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                  item.enabled ? 'bg-red-600' : 'bg-gray-200'
                ]"
              >
                <span
                  :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    item.enabled ? 'translate-x-5' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>

            <p class="text-xs text-gray-500 mt-3 line-clamp-2 leading-relaxed">
              {{ item.description || 'কাস্টম মডিউল প্লাগইন।' }}
            </p>
          </div>

          <div class="pt-3 border-t border-gray-50 flex items-center justify-between text-xs text-gray-400">
            <span>লেখক: {{ item.author || 'Custom' }}</span>
            <div class="flex items-center gap-2">
              <a 
                v-if="['ReporterBilling', 'PushNotifications', 'NewsQuiz', 'NewsArchive', 'JobsAndObituary'].includes(item.slug)" 
                :href="`/dashboard/m/${item.slug}`" 
                class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-900 text-white hover:bg-black transition flex items-center gap-1">
                ম্যানেজার ↗
              </a>
              <span :class="['px-2 py-0.5 rounded text-[10px] font-semibold', item.enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500']">
                {{ item.enabled ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
              </span>
              <button @click="deleteModule(item)" class="text-red-500 hover:text-red-700 ml-1" title="মুছে ফেলুন">
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
  modules: {
    type: Array,
    default: () => [],
  }
})

const uploading = ref(false)

const uploadZip = (e) => {
  const file = e.target.files[0]
  if (!file) return

  uploading.value = true
  const formData = new FormData()
  formData.append('module_zip', file)

  router.post(route('dashboard.modules.upload'), formData, {
    forceFormData: true,
    onFinish: () => {
      uploading.value = false
    }
  })
}

const toggle = (item) => {
  router.post(route('dashboard.modules.toggle', item.slug), {
    enabled: !item.enabled,
  }, {
    preserveScroll: true,
  })
}

const deleteModule = (item) => {
  if (confirm(`'${item.name}' মডিউলটি সম্পূর্ণ মুছে ফেলতে চান?`)) {
    router.delete(route('dashboard.modules.destroy', item.slug))
  }
}
</script>
