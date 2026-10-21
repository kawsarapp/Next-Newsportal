<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-gray-800">ক্যাটাগরি ব্যবস্থাপনা</h2>
          <p class="text-sm text-gray-500 mt-0.5">
            সকল সম্পাদকীয় ক্যাটাগরি ও নেভিগেশন মেনু ব্যবস্থাপনা
          </p>
        </div>
        <div class="flex items-center gap-3">
          <!-- Search filter -->
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="ক্যাটাগরি, বিভাগ বা জেলা খুঁজুন..."
              class="w-48 sm:w-64 text-xs px-3 py-2 pl-8 pr-7 border rounded-xl outline-none focus:border-red-500 bg-white"
            />
            <span class="absolute left-2.5 top-2 text-gray-400 text-xs">🔍</span>
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              type="button"
              class="absolute right-2.5 top-2 text-gray-400 hover:text-gray-600 text-xs"
              title="অনুসন্ধান মুছুন"
            >
              ✕
            </button>
          </div>

          <button
            @click="openModal()"
            class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-1.5 shrink-0"
          >
            <span>+</span> নতুন ক্যাটাগরি
          </button>
        </div>
      </div>

      <!-- Quick Filter Tabs & Tree Controls Bar -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-1 border-b border-gray-100">
        <!-- Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
          <button
            v-for="tab in filterTabs"
            :key="tab.id"
            type="button"
            @click="setTab(tab.id)"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer',
              activeTab === tab.id
                ? 'bg-red-600 text-white shadow-xs'
                : 'bg-white hover:bg-gray-100 text-gray-600 border border-gray-200/60'
            ]"
          >
            <span>{{ tab.name }}</span>
            <span
              :class="[
                'text-[10px] px-1.5 py-0.2 rounded-full font-mono',
                activeTab === tab.id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-600'
              ]"
            >
              {{ toBn(tab.count) }}
            </span>
          </button>
        </div>

        <!-- Tree Controls (Expand / Collapse All) -->
        <div class="flex items-center gap-2 shrink-0 text-xs">
          <span class="text-gray-400 hidden lg:inline">
            প্রদর্শিত হচ্ছে: <strong class="text-gray-700 font-mono">{{ toBn(filteredCategories.length) }}</strong> টি ক্যাটাগরি
          </span>

          <button
            type="button"
            @click="expandAll"
            class="px-2.5 py-1 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium inline-flex items-center gap-1 transition-colors shadow-2xs"
            title="সকল সাবক্যাটাগরি, বিভাগ ও জেলা প্রসারিত করুন"
          >
            <span class="text-gray-500 font-mono text-[11px]">⊞</span> সব খুলুন
          </button>

          <button
            type="button"
            @click="collapseAll"
            class="px-2.5 py-1 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-medium inline-flex items-center gap-1 transition-colors shadow-2xs"
            title="সকল সাবক্যাটাগরি সংকুচিত করুন"
          >
            <span class="text-gray-500 font-mono text-[11px]">⊟</span> সব বন্ধ করুন
          </button>
        </div>
      </div>

      <!-- Active Search Notice -->
      <div v-if="searchQuery.trim()" class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 text-xs text-amber-800 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <span>🔍</span>
          <span>"<strong>{{ searchQuery }}</strong>" অনুসন্ধানের জন্য <strong>{{ toBn(filteredCategories.length) }}</strong>টি ক্যাটাগরি পাওয়া গেছে।</span>
        </div>
        <button
          type="button"
          @click="searchQuery = ''"
          class="text-amber-900 underline font-semibold hover:text-amber-950"
        >
          অনুসন্ধান মুছুন
        </button>
      </div>

      <!-- Categories Tree Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-100 bg-gray-50/80 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <th class="py-3.5 px-4 w-12 text-center">#</th>
                <th class="py-3.5 px-4 min-w-[280px]">ক্যাটাগরির নাম (বাংলা)</th>
                <th class="py-3.5 px-4">ধরন / লেভেল</th>
                <th class="py-3.5 px-4">প্যারেন্ট ক্যাটাগরি</th>
                <th class="py-3.5 px-4">ইংরেজি নাম</th>
                <th class="py-3.5 px-4">স্লাগ</th>
                <th class="py-3.5 px-4 text-center">ক্রম</th>
                <th class="py-3.5 px-4 text-center">মেনুতে দেখাবে</th>
                <th class="py-3.5 px-4 text-center">স্ট্যাটাস</th>
                <th class="py-3.5 px-4 text-right">অ্যাকশন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="filteredCategories.length === 0">
                <td colspan="10" class="py-12 text-center text-gray-400 text-sm">
                  কোনো ক্যাটাগরি পাওয়া যায়নি
                </td>
              </tr>
              <tr
                v-for="(cat, idx) in filteredCategories"
                :key="cat.id"
                class="transition-colors group"
                :class="[
                  cat.depth === 0 ? 'bg-white hover:bg-gray-50/80 font-medium' : '',
                  cat.depth === 1 ? 'bg-emerald-50/20 hover:bg-emerald-50/50' : '',
                  cat.depth >= 2 ? 'bg-blue-50/15 hover:bg-blue-50/40 text-gray-700' : '',
                  cat.slug === 'saradesh' ? 'border-l-4 border-l-emerald-500' : ''
                ]"
              >
                <!-- Serial Number -->
                <td class="py-3 px-4 text-center text-xs text-gray-400 font-mono">{{ idx + 1 }}</td>

                <!-- Category Name with Interactive Tree Toggle & Indentation -->
                <td class="py-3 px-4 text-sm">
                  <div
                    class="flex items-center gap-1.5 flex-wrap sm:flex-nowrap"
                    :style="{ paddingLeft: `${getIndentPx(cat.depth)}px` }"
                  >
                    <!-- Tree Branch Guide Line for Children -->
                    <span
                      v-if="cat.depth === 1"
                      class="text-emerald-500 select-none font-bold text-xs"
                      title="বিভাগ / লেভেল ১ সাবক্যাটাগরি"
                    >
                      ↳
                    </span>
                    <span
                      v-else-if="cat.depth >= 2"
                      class="text-blue-400 select-none font-mono text-xs"
                      title="জেলা / লেভেল ২ সাবক্যাটাগরি"
                    >
                      ↳↳
                    </span>

                    <!-- Expand/Collapse Chevron Button (if has children) -->
                    <button
                      v-if="hasChildren(cat.id)"
                      type="button"
                      @click.stop="toggleExpand(cat.id)"
                      class="w-6 h-6 flex items-center justify-center rounded-md hover:bg-gray-200/80 text-gray-600 transition-colors shrink-0 cursor-pointer focus:outline-none"
                      :title="isExpanded(cat.id) ? 'সংকুচিত করুন' : 'প্রসারিত করুন'"
                    >
                      <svg
                        class="w-3.5 h-3.5 transition-transform duration-200"
                        :class="{'rotate-90 text-red-600': isExpanded(cat.id), 'text-gray-500': !isExpanded(cat.id)}"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                      >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                      </svg>
                    </button>
                    <!-- Spacer dot if no children -->
                    <span v-else class="w-6 h-6 flex items-center justify-center text-gray-300 text-xs shrink-0 select-none">
                      •
                    </span>

                    <!-- Category Color Dot -->
                    <span
                      class="w-2.5 h-2.5 rounded-full shrink-0 shadow-2xs"
                      :style="{
                        backgroundColor: cat.color || (cat.depth === 0 ? '#dc2626' : (cat.depth === 1 ? '#059669' : '#2563eb'))
                      }"
                    ></span>

                    <!-- Category Name Text -->
                    <span
                      @click="hasChildren(cat.id) ? toggleExpand(cat.id) : null"
                      :class="[
                        hasChildren(cat.id) ? 'cursor-pointer hover:text-red-600 select-none' : '',
                        cat.depth === 0 ? 'text-gray-900 font-bold text-sm' : '',
                        cat.depth === 1 ? 'text-emerald-900 font-semibold text-xs' : '',
                        cat.depth >= 2 ? 'text-gray-800 font-medium text-xs' : ''
                      ]"
                    >
                      {{ cat.name }}
                    </span>

                    <!-- General Badge for categories with children -->
                    <span
                      v-if="getChildrenCount(cat.id) > 0"
                      @click.stop="toggleExpand(cat.id)"
                      class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800 border border-amber-200/80 cursor-pointer hover:bg-amber-200 transition-colors shrink-0"
                      title="ক্লিক করে সাবক্যাটাগরি দেখুন / লুকান"
                    >
                      <span>📁</span>
                      <span>{{ toBn(getChildrenCount(cat.id)) }}টি সাবক্যাটাগরি</span>
                      <span class="text-[9px] text-amber-700 font-mono">{{ isExpanded(cat.id) ? '▼' : '▶' }}</span>
                    </span>
                  </div>
                </td>

                <!-- Category Type / Level Badge -->
                <td class="py-3 px-4 text-xs">
                  <span
                    v-if="cat.depth === 0"
                    class="px-2 py-0.5 rounded-md bg-red-50 text-red-700 font-semibold text-[11px] inline-flex items-center gap-1"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    মূল ক্যাটাগরি
                  </span>
                  <span
                    v-else-if="cat.parent && cat.parent.slug === 'sports'"
                    class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold text-[11px] inline-flex items-center gap-1"
                  >
                    ⚽ খেলা সাবক্যাটাগরি
                  </span>
                  <span
                    v-else-if="cat.parent && (cat.parent.slug === 'international' || cat.parent.slug === 'biswa')"
                    class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 font-semibold text-[11px] inline-flex items-center gap-1"
                  >
                    🌐 বিশ্ব সাবক্যাটাগরি
                  </span>
                  <span
                    v-else-if="cat.parent && (cat.parent.slug === 'others' || cat.parent.slug === 'ananyo')"
                    class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-semibold text-[11px] inline-flex items-center gap-1"
                  >
                    📁 অন্যান্য সাবক্যাটাগরি
                  </span>
                  <span
                    v-else-if="cat.parent && cat.parent.slug === 'saradesh'"
                    class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px] inline-flex items-center gap-1"
                  >
                    🗺️ সারা দেশ সাবক্যাটাগরি
                  </span>
                  <span
                    v-else
                    class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-medium text-[11px]"
                  >
                    সাবক্যাটাগরি
                  </span>
                </td>

                <!-- Parent Category with Proper Hierarchy Label -->
                <td class="py-3 px-4 text-xs text-gray-500">
                  <span
                    v-if="cat.parent"
                    class="inline-flex items-center px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 font-medium text-[11px] border border-gray-200/60"
                  >
                    ↳ {{ cat.parent.name }}
                  </span>
                  <span v-else class="text-gray-300 font-mono">—</span>
                </td>

                <!-- English Name -->
                <td class="py-3 px-4 text-sm text-gray-600">{{ cat.name_en || '-' }}</td>

                <!-- Slug -->
                <td class="py-3 px-4 text-sm text-gray-500 font-mono text-xs">{{ cat.slug }}</td>

                <!-- Display Order -->
                <td class="py-3 px-4 text-center text-sm font-medium text-gray-700 font-mono">{{ cat.order }}</td>

                <!-- Show in Navbar -->
                <td class="py-3 px-4 text-center">
                  <span :class="['px-2 py-0.5 rounded text-[11px] font-semibold', cat.show_in_nav ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500']">
                    {{ cat.show_in_nav ? 'হ্যাঁ' : 'না' }}
                  </span>
                </td>

                <!-- Active Status -->
                <td class="py-3 px-4 text-center">
                  <span :class="['px-2 py-0.5 rounded-full text-xs font-medium', cat.is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700']">
                    {{ cat.is_active ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Quick Add Subcategory Button (if category can be a parent) -->
                    <button
                      v-if="canHaveChildren(cat)"
                      @click="openModal(null, cat.id)"
                      class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition-colors shadow-2xs"
                      title="নতুন সাবক্যাটাগরি যোগ করুন"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                      </svg>
                      <span class="hidden sm:inline text-[11px]">+ সাবক্যাটাগরি</span>
                    </button>

                    <!-- Edit Button -->
                    <button
                      @click="openModal(cat)"
                      class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-600 hover:text-blue-600 transition-colors"
                      title="সম্পাদনা"
                    >
                      ✏️
                    </button>

                    <!-- Delete Button -->
                    <button
                      @click="deleteCategory(cat)"
                      class="p-1.5 hover:bg-red-50 rounded-lg text-gray-400 hover:text-red-600 transition-colors"
                      title="মুছুন"
                    >
                      🗑️
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Category Modal (Create / Edit) -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-bold text-gray-900">
              {{ editItem ? 'ক্যাটাগরি সম্পাদনা' : (selectedParentCategory ? `'${selectedParentCategory.name}' ক্যাটাগরির অধীনে নতুন সাবক্যাটাগরি যোগ` : 'নতুন ক্যাটাগরি যোগ') }}
            </h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="saveCategory" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরির নাম (বাংলা) <span class="text-red-500">*</span></label>
              <input
                v-model="form.name"
                type="text"
                required
                placeholder="উদা: জাতীয় / খুলনা"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ইংরেজি নাম</label>
              <input
                v-model="form.name_en"
                type="text"
                placeholder="e.g. National / Khulna"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">প্যারেন্ট ক্যাটাগরি (ঐচ্ছিক)</label>
              <select
                v-model="form.parent_id"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500 bg-white"
              >
                <option :value="null">-- প্রধান মূল ক্যাটাগরি (কোনো প্যারেন্ট নেই) --</option>
                <option
                  v-for="parent in availableParents"
                  :key="parent.id"
                  :value="parent.id"
                >
                  {{ parent.depth === 1 ? '— ' : '' }}{{ parent.name }} ({{ parent.name_en || parent.slug }})
                </option>
              </select>
              <p class="text-[11px] text-gray-400 mt-1">
                সাবক্যাটাগরি তৈরি করতে মূল ক্যাটাগরি নির্বাচন করুন (ঐচ্ছিক)
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">স্লাগ (URL)</label>
              <input
                v-model="form.slug"
                type="text"
                placeholder="national"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">প্রদর্শন ক্রম</label>
                <input
                  v-model.number="form.order"
                  type="number"
                  class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">রঙ (Color)</label>
                <input
                  v-model="form.color"
                  type="color"
                  class="w-full h-10 border rounded-xl p-1 cursor-pointer"
                />
              </div>
            </div>

            <div class="space-y-2 pt-2 border-t">
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input
                  v-model="form.show_in_nav"
                  type="checkbox"
                  class="rounded text-red-600 focus:ring-red-500"
                />
                প্রধান মেনুতে প্রদর্শন করুন
              </label>
              <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                <input
                  v-model="form.is_active"
                  type="checkbox"
                  class="rounded text-red-600 focus:ring-red-500"
                />
                সক্রিয় স্ট্যাটাস
              </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t">
              <button
                type="button"
                @click="showModal = false"
                class="px-4 py-2 text-sm text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200"
              >
                বাতিল
              </button>
              <button
                type="submit"
                :disabled="form.processing"
                class="px-5 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 flex items-center gap-2"
              >
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
import { ref, computed, watch } from 'vue'
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
const searchQuery = ref('')
const activeTab = ref('all')

const form = useForm({
  name: '',
  name_en: '',
  slug: '',
  parent_id: null,
  order: 0,
  color: '#e63946',
  show_in_nav: true,
  is_active: true,
})

const selectedParentCategory = computed(() => {
  return form.parent_id ? props.categories.find(c => Number(c.id) === Number(form.parent_id)) : null
})

// Expanded category IDs set: expand all parents by default so subcategories (under খেলা, অন্যান্য, etc.) are immediately visible!
const expandedIds = ref(new Set(
  props.categories
    .filter(c => props.categories.some(child => child.parent_id === c.id))
    .map(c => c.id)
))

// Bengali numbers helper
const toBn = (n) => {
  if (n === null || n === undefined) return '০'
  const digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']
  return String(n).replace(/\d/g, d => digits[d])
}

// Indentation helper
const getIndentPx = (depth = 0) => {
  if (!depth || depth <= 0) return 0
  if (depth === 1) return 24
  return 24 + (depth - 1) * 28
}

// Fast lookup map by ID
const categoryMap = computed(() => {
  const map = new Map()
  props.categories.forEach(c => map.set(c.id, c))
  return map
})

// Fast lookup of direct children: parent_id -> Array<Category>
const childrenMap = computed(() => {
  const map = new Map()
  props.categories.forEach(c => {
    const pid = c.parent_id ?? null
    if (!map.has(pid)) {
      map.set(pid, [])
    }
    map.get(pid).push(c)
  })
  return map
})

const getChildren = (catId) => {
  return childrenMap.value.get(catId) || []
}

const getChildrenCount = (catId) => {
  return getChildren(catId).length
}

const hasChildren = (catId) => {
  return getChildrenCount(catId) > 0
}

// Tab bar items
const filterTabs = computed(() => [
  { id: 'all', name: 'সকল ক্যাটাগরি', count: props.categories.length },
  { id: 'nav', name: 'মেনুতে প্রদর্শিত', count: props.categories.filter(c => c.show_in_nav).length },
  { id: 'active', name: 'সক্রিয় ক্যাটাগরি', count: props.categories.filter(c => c.is_active).length },
])

// Expand / Collapse methods
const isExpanded = (catId) => {
  return expandedIds.value.has(catId)
}

const toggleExpand = (catId) => {
  const next = new Set(expandedIds.value)
  if (next.has(catId)) {
    next.delete(catId)
  } else {
    next.add(catId)
  }
  expandedIds.value = next
}

const expandAll = () => {
  const ids = props.categories.filter(c => hasChildren(c.id)).map(c => c.id)
  expandedIds.value = new Set(ids)
}

const collapseAll = () => {
  expandedIds.value = new Set()
}

const setTab = (tabId) => {
  activeTab.value = tabId
}

// Tree visibility check: a node is visible in tree if every ancestor is expanded
const isAncestorPathExpanded = (cat) => {
  let curr = cat
  while (curr && curr.parent_id) {
    if (!expandedIds.value.has(curr.parent_id)) {
      return false
    }
    curr = categoryMap.value.get(curr.parent_id)
  }
  return true
}

// Filtered categories for table display
const filteredCategories = computed(() => {
  // If search query is entered, search across ALL categories directly
  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    return props.categories.filter(cat =>
      cat.name.toLowerCase().includes(q) ||
      (cat.name_en && cat.name_en.toLowerCase().includes(q)) ||
      cat.slug.toLowerCase().includes(q) ||
      (cat.parent && cat.parent.name.toLowerCase().includes(q))
    )
  }

  if (activeTab.value === 'nav') {
    return props.categories.filter(c => c.show_in_nav)
  }

  if (activeTab.value === 'active') {
    return props.categories.filter(c => c.is_active)
  }

  return props.categories.filter(cat => {
    if (!cat.parent_id) {
      return true
    }
    return isAncestorPathExpanded(cat)
  })
})

// Rule for whether a category can have children (depth 0 or depth 1 categories)
const canHaveChildren = (cat) => {
  if (!cat) return false
  return !cat.parent_id || (cat.depth ?? 0) < 2
}

// Available parents for Create/Edit Modal dropdown
const availableParents = computed(() => {
  return props.categories.filter(c => {
    if (editItem.value && c.id === editItem.value.id) return false
    return !c.parent_id
  })
})

// Modal open
const openModal = (item = null, parentId = null) => {
  editItem.value = item
  if (item) {
    form.name = item.name
    form.name_en = item.name_en || ''
    form.slug = item.slug
    form.parent_id = item.parent_id || null
    form.order = item.order || 0
    form.color = item.color || '#e63946'
    form.show_in_nav = Boolean(item.show_in_nav)
    form.is_active = Boolean(item.is_active)
  } else {
    form.reset()
    form.parent_id = parentId !== null ? parentId : null
    form.order = props.categories.length + 1
    form.color = '#e63946'
    form.show_in_nav = true
    form.is_active = true
  }
  showModal.value = true
}

// Save category
const saveCategory = () => {
  const targetParentId = form.parent_id
  if (editItem.value) {
    form.put(route('dashboard.categories.update', editItem.value.id), {
      onSuccess: () => {
        showModal.value = false
        if (targetParentId) {
          expandedIds.value.add(targetParentId)
          expandedIds.value = new Set(expandedIds.value)
        }
      },
    })
  } else {
    form.post(route('dashboard.categories.store'), {
      onSuccess: () => {
        showModal.value = false
        if (targetParentId) {
          expandedIds.value.add(targetParentId)
          expandedIds.value = new Set(expandedIds.value)
        }
      },
    })
  }
}

// Delete category
const deleteCategory = (cat) => {
  const childCount = getChildrenCount(cat.id)
  const message = childCount > 0
    ? `'${cat.name}' ক্যাটাগরির অধীনে ${toBn(childCount)}টি সাবক্যাটাগরি রয়েছে। আপনি কি নিশ্চিতভাবে এটি মুছে ফেলতে চান?`
    : `'${cat.name}' ক্যাটাগরি কি মুছে ফেলতে চান?`

  if (confirm(message)) {
    router.delete(route('dashboard.categories.destroy', cat.id))
  }
}
</script>
