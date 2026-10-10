<template>
  <span :class="['px-2.5 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1', badgeClass]">
    <span :class="['w-1.5 h-1.5 rounded-full', dotClass]"></span>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true,
  }
})

const badgeConfig = {
  draft: {
    label: 'খসড়া',
    badge: 'bg-gray-100 text-gray-700',
    dot: 'bg-gray-400',
  },
  pending_review: {
    label: 'অনুমোদন বাকি',
    badge: 'bg-yellow-50 text-yellow-800 border border-yellow-200',
    dot: 'bg-yellow-500',
  },
  approved: {
    label: 'অনুমোদিত',
    badge: 'bg-blue-50 text-blue-700 border border-blue-200',
    dot: 'bg-blue-500',
  },
  published: {
    label: 'প্রকাশিত',
    badge: 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    dot: 'bg-emerald-500',
  },
  scheduled: {
    label: 'শিডিউলড',
    badge: 'bg-purple-50 text-purple-700 border border-purple-200',
    dot: 'bg-purple-500',
  },
  returned_for_revision: {
    label: 'সংশোধনে ফেরত',
    badge: 'bg-orange-50 text-orange-700 border border-orange-200',
    dot: 'bg-orange-500',
  },
  archived: {
    label: 'সংরক্ষিত',
    badge: 'bg-slate-100 text-slate-700',
    dot: 'bg-slate-400',
  },
  trashed: {
    label: 'ট্র্যাশ',
    badge: 'bg-rose-50 text-rose-700 border border-rose-200',
    dot: 'bg-rose-500',
  },
}

const current = computed(() => badgeConfig[props.status] || {
  label: props.status,
  badge: 'bg-gray-100 text-gray-600',
  dot: 'bg-gray-400',
})

const label = computed(() => current.value.label)
const badgeClass = computed(() => current.value.badge)
const dotClass = computed(() => current.value.dot)
</script>
