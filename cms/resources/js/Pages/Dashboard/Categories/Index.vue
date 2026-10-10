<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">ক্যাটাগরি ব্যবস্থাপনা</h2>
          <p class="text-sm text-gray-500 mt-0.5">ওয়েবসাইটের মেনু ও বিভাগীয় বিন্যাস পরিচালনা করুন</p>
        </div>
        <button
          @click="openModal()"
          class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-1.5"
        >
          <span>+</span> নতুন ক্যাটাগরি
        </button>
      </div>

      <!-- Categories List -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
              <th class="py-3.5 px-4 w-12 text-center">#</th>
              <th class="py-3.5 px-4">নাম (বাংলা)</th>
              <th class="py-3.5 px-4">ইংরেজি নাম</th>
              <th class="py-3.5 px-4">স্লাগ</th>
              <th class="py-3.5 px-4 text-center">ক্রম</th>
              <th class="py-3.5 px-4 text-center">মেনুতে দেখাবে</th>
              <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
              <th class="py-3.5 px-4 text-right">অ্যাকশন</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="categories.length === 0">
              <td colspan="8" class="py-12 text-center text-gray-400 text-sm">কোনো ক্যাটাগরি তৈরি করা হয়নি</td>
            </tr>
            <tr v-for="(cat, idx) in categories" :key="cat.id" class="hover:bg-gray-50/70 transition-colors">
              <td class="py-3.5 px-4 text-center text-sm text-gray-400 font-mono">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-semibold text-gray-800 text-sm flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: cat.color || '#e5e7eb' }"></span>
                {{ cat.name }}
              </td>
              <td class="py-3.5 px-4 text-sm text-gray-600">{{ cat.name_en || '-' }}</td>
              <td class="py-3.5 px-4 text-sm text-gray-500 font-mono text-xs">{{ cat.slug }}</td>
              <td class="py-3.5 px-4 text-center text-sm font-medium text-gray-700">{{ cat.order }}</td>
              <td class="py-3.5 px-4 text-center">
                <span :class="['px-2 py-0.5 rounded text-[11px] font-semibold', cat.show_in_nav ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500']">
                  {{ cat.show_in_nav ? 'হ্যাঁ' : 'না' }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-center">
                <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', cat.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700']">
                  {{ cat.is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openModal(cat)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-600 hover:text-blue-600 transition-colors" title="সম্পাদনা">
                    ✏️
                  </button>
                  <button @click="deleteCategory(cat)" class="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-600 transition-colors" title="মুছুন">
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Category Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4">
          <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-bold text-gray-900">{{ editItem ? 'ক্যাটাগরি সম্পাদনা' : 'নতুন ক্যাটাগরি যোগ' }}</h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="saveCategory" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরির নাম (বাংলা) <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" required placeholder="উদা: জাতীয়" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ইংরেজি নাম</label>
              <input v-model="form.name_en" type="text" placeholder="e.g. National" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">স্লাগ (URL)</label>
              <input v-model="form.slug" type="text" placeholder="national" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">প্রদর্শন ক্রম</label>
                <input v-model.number="form.order" type="number" class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">রঙ (Color)</label>
                <input v-model="form.color" type="color" class="w-full h-10 border rounded-xl p-1 cursor-pointer" />
              </div>
            </div>

            <div class="space-y-2 pt-2 border-t">
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input v-model="form.show_in_nav" type="checkbox" class="rounded text-red-600 focus:ring-red-500" />
                প্রধান মেনুতে প্রদর্শন করুন
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input v-model="form.is_active" type="checkbox" class="rounded text-red-600 focus:ring-red-500" />
                সক্রিয় স্ট্যাটাস
              </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t">
              <button type="button" @click="showModal = false" class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">বাতিল</button>
              <button type="submit" :disabled="form.processing" class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 flex items-center gap-2">
                সংরক্ষণ করুন
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  }
})

const showModal = ref(false)
const editItem = ref(null)

const form = useForm({
  name: '',
  name_en: '',
  slug: '',
  order: 0,
  color: '#e63946',
  show_in_nav: true,
  is_active: true,
})

const openModal = (item = null) => {
  editItem.value = item
  if (item) {
    form.name = item.name
    form.name_en = item.name_en || ''
    form.slug = item.slug
    form.order = item.order || 0
    form.color = item.color || '#e63946'
    form.show_in_nav = Boolean(item.show_in_nav)
    form.is_active = Boolean(item.is_active)
  } else {
    form.reset()
    form.order = props.categories.length + 1
    form.color = '#e63946'
    form.show_in_nav = true
    form.is_active = true
  }
  showModal.value = true
}

const saveCategory = () => {
  if (editItem.value) {
    form.put(route('dashboard.categories.update', editItem.value.id), {
      onSuccess: () => { showModal.value = false },
    })
  } else {
    form.post(route('dashboard.categories.store'), {
      onSuccess: () => { showModal.value = false },
    })
  }
}

const deleteCategory = (cat) => {
  if (confirm(`'${cat.name}' ক্যাটাগরি কি মুছে ফেলতে চান?`)) {
    router.delete(route('dashboard.categories.destroy', cat.id))
  }
}
</script>
