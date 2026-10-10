<template>
  <AppLayout title="ওয়েব পুশ নোটিফিকেশন">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">মডিউল</span>
            <h2 class="text-2xl font-bold text-gray-800">ওয়েব পুশ নোটিফিকেশন ম্যানেজার</h2>
          </div>
          <p class="text-sm text-gray-500 mt-1">পাঠকদের ব্রাউজার ও ডিভাইসে ইনস্ট্যান্ট ব্রেকিং নিউজ পুশ নোটিফিকেশন পাঠান</p>
        </div>
        <div>
          <button 
            @click="showSendModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            নতুন পুশ নোটিফিকেশন পাঠান
          </button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট গ্রাহক (Subscribers)</span>
            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">🔔</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_subscribers }} জন</p>
          <span class="text-xs text-rose-600 font-medium">সক্রিয় পুশ অনুমতিপ্রাপ্ত</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">ডেস্কটপ / মোবাইল অনুপাত</span>
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">📱</div>
          </div>
          <p class="text-lg font-bold text-gray-800 mt-2">💻 {{ stats.desktop_count }} | 📱 {{ stats.mobile_count }}</p>
          <span class="text-xs text-blue-600 font-medium">ডিভাইস সেগমেন্টেশন</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট প্রেরিত নোটিফিকেশন</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">🚀</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_sent }} টি</p>
          <span class="text-xs text-emerald-600 font-medium">ডেলিভারকৃত বার্তা</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">ক্লিক রেট (CTR)</span>
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">🎯</div>
          </div>
          <p class="text-2xl font-bold text-purple-600 mt-2">{{ stats.click_rate }}%</p>
          <span class="text-xs text-purple-600 font-medium">মোট {{ stats.total_clicks }} বার ওপেন</span>
        </div>
      </div>

      <!-- Quick Push from Recent Articles -->
      <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-3">
        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
          <span>⚡</span> সাম্প্রতিক নিউজ থেকে তাৎক্ষণিক পুশ পাঠান:
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          <div 
            v-for="post in recentPosts" 
            :key="post.id"
            class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:border-rose-200 hover:bg-rose-50/20 transition group">
            <div class="pr-2 truncate">
              <p class="text-xs font-semibold text-gray-800 truncate">{{ post.title }}</p>
              <span class="text-[11px] text-gray-400">ID: #{{ post.id }}</span>
            </div>
            <button 
              @click="populateFromPost(post)"
              class="shrink-0 px-2.5 py-1 bg-rose-50 group-hover:bg-rose-600 text-rose-600 group-hover:text-white rounded-lg text-xs font-medium transition">
              পুশ করুন
            </button>
          </div>
        </div>
      </div>

      <!-- Campaigns History Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-gray-800 text-sm">প্রেরিত নোটিফিকেশন হিস্ট্রি (Campaigns)</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-xs text-gray-400 font-semibold uppercase">
              <tr>
                <th class="px-5 py-3.5">শিরোনাম ও বার্তা</th>
                <th class="px-5 py-3.5">টার্গেট লিঙ্ক</th>
                <th class="px-5 py-3.5 text-center">গ্রাহক ক্যাটাগরি</th>
                <th class="px-5 py-3.5 text-center">ডেলিভারি</th>
                <th class="px-5 py-3.5">প্রেরক</th>
                <th class="px-5 py-3.5">তারিখ ও সময়</th>
                <th class="px-5 py-3.5 text-right">মুছে ফেলুন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="c in campaigns.data" :key="c.id" class="hover:bg-gray-50/50 transition">
                <td class="px-5 py-4 max-w-sm">
                  <div class="flex items-start gap-3">
                    <img v-if="c.image_url" :src="c.image_url" class="w-10 h-10 rounded-lg object-cover border shrink-0" />
                    <div>
                      <p class="font-bold text-gray-800 text-sm">{{ c.title }}</p>
                      <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ c.message }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 text-xs font-mono text-gray-500 max-w-xs truncate">
                  <a :href="c.target_url" target="_blank" class="hover:text-rose-600 hover:underline">{{ c.target_url }}</a>
                </td>
                <td class="px-5 py-4 text-center">
                  <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 capitalize">
                    {{ c.segment === 'all' ? 'সকল ডিভাইস' : c.segment }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center font-bold text-rose-600">{{ c.sent_count }} জন</td>
                <td class="px-5 py-4 text-xs text-gray-600">{{ c.sender?.name || 'অ্যাডমিন' }}</td>
                <td class="px-5 py-4 text-xs text-gray-400">{{ new Date(c.created_at).toLocaleString('bn-BD') }}</td>
                <td class="px-5 py-4 text-right">
                  <button 
                    @click="deleteCampaign(c.id)"
                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" 
                    title="মুছে ফেলুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
              <tr v-if="!campaigns.data || campaigns.data.length === 0">
                <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                  এখনো কোনো পুশ নোটিফিকেশন পাঠানো হয়নি।
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Send Notification Modal -->
      <div v-if="showSendModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800 text-lg">ইনস্ট্যান্ট ওয়েব পুশ পাঠান</h3>
            <button @click="showSendModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="sendNotification" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">নোটিফিকেশনের শিরোনাম (Title) *</label>
              <input v-model="form.title" type="text" maxlength="150" required placeholder="ব্রেকিং নিউজ: ..." class="w-full border border-gray-200 rounded-xl px-3 py-2" />
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">সংক্ষিপ্ত বিবরণ (Message) *</label>
              <textarea v-model="form.message" rows="3" maxlength="300" required placeholder="বিস্তারিত পড়তে এখানে ট্যাপ করুন..." class="w-full border border-gray-200 rounded-xl px-3 py-2"></textarea>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">ক্লিক করলে যে লিঙ্কে যাবে (URL)</label>
              <input v-model="form.target_url" type="url" placeholder="https://..." class="w-full border border-gray-200 rounded-xl px-3 py-2" />
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">ছবি বা ব্যানারের লিঙ্ক (Image URL - ঐচ্ছিক)</label>
              <input v-model="form.image_url" type="url" placeholder="https://..." class="w-full border border-gray-200 rounded-xl px-3 py-2" />
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">টার্গেট গ্রাহক (Segment)</label>
              <select v-model="form.segment" class="w-full border border-gray-200 rounded-xl px-3 py-2">
                <option value="all">সকল গ্রাহক (Desktop + Mobile)</option>
                <option value="mobile">শুধু মোবাইল গ্রাহক</option>
                <option value="desktop">শুধু ডেস্কটপ গ্রাহক</option>
              </select>
            </div>

            <!-- Preview Card -->
            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-1">
              <span class="text-[11px] font-bold text-gray-400 uppercase">ডিভাইস প্রিভিউ:</span>
              <div class="flex items-center gap-3 bg-white p-3 rounded-lg border shadow-xs">
                <div class="w-8 h-8 rounded-full bg-rose-500 text-white flex items-center justify-center font-bold text-xs shrink-0">🔔</div>
                <div class="min-w-0 flex-1">
                  <p class="font-bold text-gray-800 text-xs truncate">{{ form.title || 'শিরোনাম এখানে দেখাবে' }}</p>
                  <p class="text-[11px] text-gray-500 truncate">{{ form.message || 'সংক্ষিপ্ত বার্তা এখানে দেখাবে' }}</p>
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
              <button type="button" @click="showSendModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50">বাতিল</button>
              <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                ব্রডকাস্ট করুন
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
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  campaigns: Object,
  recentPosts: Array,
  stats: Object,
})

const showSendModal = ref(false)

const form = ref({
  title: '',
  message: '',
  target_url: '',
  image_url: '',
  segment: 'all',
})

function populateFromPost(post) {
  form.value = {
    title: post.title,
    message: 'বিস্তারিত পড়তে ক্লিক করুন — তাজা খবর সরাসরি আপনার হাতে।',
    target_url: `${window.location.origin}/news/${post.slug || post.id}`,
    image_url: post.featured_image || '',
    segment: 'all',
  }
  showSendModal.value = true
}

function sendNotification() {
  router.post(route('module.PushNotifications.send'), form.value, {
    onSuccess: () => {
      showSendModal.value = false
      form.value = {
        title: '',
        message: '',
        target_url: '',
        image_url: '',
        segment: 'all',
      }
    }
  })
}

function deleteCampaign(id) {
  if (confirm('আপনি কি নিশ্চিত যে এই ক্যাম্পেইন হিস্ট্রিটি মুছে ফেলতে চান?')) {
    router.delete(route('module.PushNotifications.destroy', id))
  }
}
</script>
