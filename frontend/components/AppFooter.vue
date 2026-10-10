<template>
  <div class="bg-gray-900 text-gray-400 border-t border-gray-800 text-xs py-10 px-4 sm:px-8 mt-16">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
      <!-- 1. About / Bio -->
      <div class="space-y-3">
        <h3 class="text-white font-bold text-base">{{ siteName }}</h3>
        <p class="leading-relaxed">
          {{ hfConfig.about_bio || 'সত্য ও বস্তুনিষ্ঠ সাংবাদিকতার অঙ্গীকার নিয়ে সর্বাধুনিক হেডলেস প্রযুক্তিতে পরিচালিত ডিজিটাল নিউজ মিডিয়া।' }}
        </p>

        <!-- Social Media Links -->
        <div class="flex items-center gap-2 pt-2">
          <a
            v-if="hfConfig.facebook_url"
            :href="hfConfig.facebook_url"
            target="_blank"
            class="w-7 h-7 rounded-lg bg-gray-800 hover:bg-blue-600 text-white flex items-center justify-center transition-colors"
            title="Facebook"
          >
            f
          </a>
          <a
            v-if="hfConfig.twitter_url"
            :href="hfConfig.twitter_url"
            target="_blank"
            class="w-7 h-7 rounded-lg bg-gray-800 hover:bg-black text-white flex items-center justify-center transition-colors"
            title="X (Twitter)"
          >
            𝕏
          </a>
          <a
            v-if="hfConfig.youtube_url"
            :href="hfConfig.youtube_url"
            target="_blank"
            class="w-7 h-7 rounded-lg bg-gray-800 hover:bg-red-600 text-white flex items-center justify-center transition-colors"
            title="YouTube"
          >
            ▶
          </a>
        </div>
      </div>

      <!-- 2. Quick Links -->
      <div>
        <h4 class="text-white font-semibold text-sm mb-3">গুরুত্বপূর্ণ লিংক</h4>
        <ul class="space-y-2">
          <li><NuxtLink to="/category/national" class="hover:text-white transition-colors">জাতীয় সংবাদ</NuxtLink></li>
          <li><NuxtLink to="/category/politics" class="hover:text-white transition-colors">রাজনীতি</NuxtLink></li>
          <li><NuxtLink to="/category/economy" class="hover:text-white transition-colors">অর্থনীতি ও ব্যবসা</NuxtLink></li>
          <li><NuxtLink to="/category/sports" class="hover:text-white transition-colors">খেলাধুলা</NuxtLink></li>
        </ul>
      </div>

      <!-- 3. Contact Info -->
      <div>
        <h4 class="text-white font-semibold text-sm mb-3">যোগাযোগ</h4>
        <p class="leading-relaxed space-y-1">
          <span class="block">{{ hfConfig.office_address || 'প্রধান কার্যালয়: ঢাকা, বাংলাদেশ' }}</span>
          <span class="block">ইমেইল: {{ hfConfig.contact_email || 'newsroom@dainikbangla.com' }}</span>
          <span class="block">ফোন: {{ hfConfig.contact_phone || '+৮৮০১৭০০০০০০০০' }}</span>
        </p>
      </div>

      <!-- 4. Architecture / About Headless -->
      <div>
        <h4 class="text-white font-semibold text-sm mb-3">ডিজিটাল আর্কিটেকচার</h4>
        <p class="leading-relaxed">
          এই ফ্রন্টএন্ডটি সেন্ট্রাল হেডলেস সিএমএস ব্যাকএন্ডের সাথে যুক্ত। সম্পূর্ণ সিঙ্গেল পেজ অ্যাপ্লিকেশন (SPA) আর্কিটেকচার।
        </p>
      </div>
    </div>

    <!-- Bottom Copyright -->
    <div class="max-w-7xl mx-auto pt-6 border-t border-gray-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px]">
      <p>{{ hfConfig.copyright_text || '© ২০২৬ দৈনিক বাংলা নিউজপোর্টাল। সর্বস্বত্ব সংরক্ষিত।' }}</p>
      <p>Developed with Nuxt 3 SPA & Laravel Headless CMS Engine</p>
    </div>
  </div>
</template>

<script setup>
const config = useRuntimeConfig()
const siteName = config.public.siteName || 'দৈনিক বাংলা'

const { data: hfResponse } = await useFetch(`${config.public.apiBase}/header-footer-config`, {
  headers: {
    'Accept': 'application/json',
    'X-Portal-Key': config.public.portalKey,
  },
  key: 'footer-header-footer-config',
  default: () => ({ success: true, data: {} }),
})

const hfConfig = computed(() => hfResponse.value?.data || {})
</script>
