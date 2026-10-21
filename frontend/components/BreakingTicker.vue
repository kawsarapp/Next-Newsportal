<template>
  <div v-if="items.length > 0" class="py-1.5 sm:py-2 bg-white border-b border-gray-200/90 shadow-2xs relative z-30 select-none w-full">
    <div class="w-full px-3 sm:px-6 flex items-center justify-between gap-3">
      <!-- Left Accent Bar & Container -->
      <div class="flex items-center gap-2 overflow-hidden flex-1 relative">
        <!-- Far-left Red Accent Strip -->
        <span class="w-1 h-4.5 sm:h-5 bg-red-600 rounded-full shrink-0 shadow-2xs"></span>

        <!-- Scrolling Ticker Track -->
        <div
          ref="tickerContainer"
          class="flex-1 overflow-hidden relative"
          @mouseenter="isHovered = true"
          @mouseleave="isHovered = false"
        >
          <div
            class="ticker-track flex items-center whitespace-nowrap"
            :class="{ 'paused': isPaused || isHovered }"
            :style="{ animationDuration: `${scrollDuration}s` }"
          >
            <!-- First Set -->
            <div class="ticker-content flex items-center shrink-0">
              <span
                v-for="(item, idx) in items"
                :key="`t1-${item.id || idx}-${idx}`"
                class="inline-flex items-center"
              >
                <NuxtLink
                  :to="`/news/${item.slug}`"
                  class="font-bold text-xs sm:text-sm text-gray-900 hover:text-red-600 transition-colors flex items-center gap-1.5"
                >
                  <span v-if="item.is_breaking" class="bg-red-600 text-white text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded font-bold tracking-wide shrink-0">
                    ব্রেকিং
                  </span>
                  <span>{{ item.title }}</span>
                </NuxtLink>
                <!-- Red Sparkle / 4-point star separator -->
                <span class="text-red-600 mx-3 font-black text-xs sm:text-sm select-none">✦</span>
              </span>
            </div>

            <!-- Duplicated Set for Seamless Infinite Loop -->
            <div class="ticker-content flex items-center shrink-0" aria-hidden="true">
              <span
                v-for="(item, idx) in items"
                :key="`t2-${item.id || idx}-${idx}`"
                class="inline-flex items-center"
              >
                <NuxtLink
                  :to="`/news/${item.slug}`"
                  class="font-bold text-xs sm:text-sm text-gray-900 hover:text-red-600 transition-colors flex items-center gap-1.5"
                >
                  <span v-if="item.is_breaking" class="bg-red-600 text-white text-[10px] sm:text-[11px] px-1.5 py-0.5 rounded font-bold tracking-wide shrink-0">
                    ব্রেকিং
                  </span>
                  <span>{{ item.title }}</span>
                </NuxtLink>
                <!-- Red Sparkle / 4-point star separator -->
                <span class="text-red-600 mx-3 font-black text-xs sm:text-sm select-none">✦</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Controls: [ < ] [ || ] [ > ] -->
      <div class="inline-flex items-center bg-gray-50 border border-gray-200 rounded divide-x divide-gray-200 shadow-2xs shrink-0 text-gray-700 text-xs">
        <!-- Previous Button -->
        <button
          type="button"
          @click="stepBackward"
          class="px-2 py-0.5 hover:bg-gray-200 hover:text-gray-900 transition-colors cursor-pointer text-xs font-bold"
          title="পূর্ববর্তী সংবাদ"
        >
          ‹
        </button>

        <!-- Pause / Play Toggle -->
        <button
          type="button"
          @click="togglePlayPause"
          class="px-2 py-0.5 hover:bg-gray-200 hover:text-gray-900 font-mono transition-colors cursor-pointer text-xs font-bold"
          :title="isPaused ? 'চালু করুন' : 'থামান'"
        >
          {{ isPaused ? '▶' : '||' }}
        </button>

        <!-- Next Button -->
        <button
          type="button"
          @click="stepForward"
          class="px-2 py-0.5 hover:bg-gray-200 hover:text-gray-900 transition-colors cursor-pointer text-xs font-bold"
          title="পরবর্তী সংবাদ"
        >
          ›
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  breakingNews: {
    type: Array,
    default: () => []
  }
})

const isPaused = ref(false)
const isHovered = ref(false)
const scrollDuration = ref(240) // Extra slow, gentle & ultra-readable scroll pace (240s)

const items = computed(() => {
  const raw = Array.isArray(props.breakingNews) ? props.breakingNews : []
  if (raw.length === 0) return []
  let list = [...raw]
  while (list.length < 10) {
    list = list.concat(raw)
  }
  return list
})

const togglePlayPause = () => {
  isPaused.value = !isPaused.value
}

const stepForward = () => {
  // Speed up momentarily or nudge forward
  scrollDuration.value = Math.max(40, scrollDuration.value - 40)
  setTimeout(() => {
    scrollDuration.value = 240
  }, 1000)
}

const stepBackward = () => {
  scrollDuration.value = Math.min(400, scrollDuration.value + 60)
  setTimeout(() => {
    scrollDuration.value = 240
  }, 1000)
}
</script>

<style scoped>
.ticker-track {
  display: flex;
  width: max-content;
  animation: tickerScroll 240s linear infinite;
}

.ticker-track.paused,
.ticker-track:hover {
  animation-play-state: paused !important;
}

@keyframes tickerScroll {
  0% {
    transform: translate3d(0, 0, 0);
  }
  100% {
    transform: translate3d(-50%, 0, 0);
  }
}
</style>
