<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Flash Messages -->
      <div v-if="$page.props.flash?.success" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between">
        <span>✓ {{ $page.props.flash.success }}</span>
      </div>
      <div v-if="$page.props.flash?.error" class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm flex items-center justify-between">
        <span>✕ {{ $page.props.flash.error }}</span>
      </div>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-semibold">সোশ্যাল ডিস্ট্রিবিউশন</span>
            <h2 class="text-xl font-bold text-gray-800">সোশ্যাল মিডিয়া ডিস্ট্রিবিউশন</h2>
          </div>
          <p class="text-sm text-gray-500 mt-0.5">ফেসবুক পেজ, টেলিগ্রাম, এক্স (টুইটার) ও ইউটিউবে স্বয়ংক্রিয় নিউজ সম্প্রচার ও লগ</p>
        </div>

        <button
          @click="showAddModal = true"
          type="button"
          class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-1.5"
        >
          <span class="text-base font-bold">+</span> নতুন চ্যানেল যুক্ত করুন
        </button>
      </div>

      <!-- Connected Channels -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-bold text-gray-700">সংযুক্ত সোশ্যাল অ্যাকাউন্টস ({{ accounts.length }})</h3>
        </div>

        <div v-if="accounts.length === 0" class="bg-white p-8 rounded-2xl border border-gray-100 text-center text-gray-400">
          কোনো সোশ্যাল চ্যানেল যুক্ত করা হয়নি। "নতুন চ্যানেল যুক্ত করুন" বাটনে ক্লিক করে ফেসবুক, টেলিগ্রাম বা এক্স যুক্ত করুন।
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="acc in accounts"
            :key="acc.id"
            class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between group hover:border-gray-200 transition-all"
          >
            <div class="flex items-center gap-3">
              <div :class="['w-11 h-11 rounded-xl flex items-center justify-center text-xl font-bold', platformStyle(acc.platform)]">
                {{ platformIcon(acc.platform) }}
              </div>
              <div>
                <h3 class="font-bold text-gray-800 text-sm">{{ acc.account_name }}</h3>
                <p class="text-xs text-gray-400 font-mono">{{ acc.account_id || acc.platform }}</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-medium">
                {{ acc.status === 'active' ? 'সক্রিয়' : acc.status }}
              </span>
              <button
                @click="removeAccount(acc)"
                title="চ্যানেল মুছুন"
                class="text-gray-300 hover:text-red-500 p-1 rounded-lg transition-colors"
              >
                🗑️
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Publish Form -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-base border-b pb-3 flex items-center gap-2">
            <span>📢</span> সোশ্যাল মিডিয়া পোস্ট পাবলিশ করুন
          </h3>

          <form @submit.prevent="submitPublish" class="space-y-4">
            <!-- Article Selector (Optional) -->
            <div v-if="recent_articles && recent_articles.length > 0">
              <label class="block text-xs font-semibold text-gray-700 mb-1">নিবন্ধ নির্বাচন করুন (স্বয়ংক্রিয় ডেটা ভরাট)</label>
              <select
                @change="onArticleSelect($event)"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500 bg-white"
              >
                <option value="">-- নিবন্ধ সিলেক্ট করুন অথবা সরাসরি বার্তা লিখুন --</option>
                <option v-for="post in recent_articles" :key="post.id" :value="post.id">
                  {{ post.title }}
                </option>
              </select>
            </div>

            <!-- Caption / Message -->
            <div>
              <div class="flex justify-between items-center mb-1">
                <label class="block text-xs font-semibold text-gray-700">ক্যাপশন / পোস্ট বার্তা <span class="text-red-500">*</span></label>
                <span class="text-xs text-gray-400">{{ publishForm.caption.length }} অক্ষর</span>
              </div>
              <textarea
                v-model="publishForm.caption"
                rows="3"
                placeholder="সোশ্যাল মিডিয়ায় প্রচারের জন্য আকর্ষণীয় ক্যাপশন ও বিবরণ লিখুন..."
                class="w-full text-sm p-3 border rounded-xl outline-none focus:border-red-500 bg-gray-50/50"
                required
              ></textarea>
              <p v-if="publishForm.errors.caption" class="text-xs text-red-500 mt-1">{{ publishForm.errors.caption }}</p>
            </div>

            <!-- News URL -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">সংবাদের লিংক (URL)</label>
              <input
                v-model="publishForm.news_url"
                type="url"
                placeholder="https://newsportal.com/news/example-article"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
              />
              <p v-if="publishForm.errors.news_url" class="text-xs text-red-500 mt-1">{{ publishForm.errors.news_url }}</p>
            </div>

            <!-- Account Selection -->
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-2">কোন কোন চ্যানেলে প্রকাশ হবে:</label>
              <div v-if="accounts.length === 0" class="text-xs text-amber-600 bg-amber-50 p-2.5 rounded-lg border border-amber-200">
                পোস্ট করার আগে অন্তত একটি সোশ্যাল চ্যানেল সংযুক্ত করুন।
              </div>
              <div v-else class="flex flex-wrap gap-4 text-xs text-gray-700">
                <label
                  v-for="acc in accounts"
                  :key="acc.id"
                  class="flex items-center gap-2 p-2 bg-gray-50 hover:bg-gray-100 rounded-xl cursor-pointer border border-gray-200 transition-colors"
                >
                  <input
                    type="checkbox"
                    :value="acc.id"
                    v-model="publishForm.account_ids"
                    class="rounded text-red-600 focus:ring-red-500"
                  />
                  <span>{{ platformIcon(acc.platform) }} {{ acc.account_name }}</span>
                </label>
              </div>
            </div>

            <div class="flex items-center justify-end pt-3 border-t">
              <button
                type="submit"
                :disabled="publishForm.processing || !publishForm.caption.trim() || accounts.length === 0"
                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-40 text-white rounded-xl text-sm font-semibold shadow-sm flex items-center gap-2 transition-colors"
              >
                <span v-if="publishForm.processing" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span>{{ publishForm.processing ? 'শেয়ার হচ্ছে...' : 'তাত্ক্ষণিক শেয়ার করুন' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Channel Summary Sidebar -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
          <h3 class="font-bold text-gray-800 text-sm border-b pb-3">প্ল্যাটফর্ম স্ট্যাটাস</h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between text-xs p-2.5 bg-blue-50/50 rounded-xl border border-blue-100">
              <span class="flex items-center gap-2 font-medium text-blue-900">
                <span>📘</span> ফেসবুক পেজ API
              </span>
              <span class="font-semibold text-emerald-600">রেডি ✓</span>
            </div>
            <div class="flex items-center justify-between text-xs p-2.5 bg-sky-50/50 rounded-xl border border-sky-100">
              <span class="flex items-center gap-2 font-medium text-sky-900">
                <span>✈️</span> টেলিগ্রাম বট
              </span>
              <span class="font-semibold text-emerald-600">রেডি ✓</span>
            </div>
            <div class="flex items-center justify-between text-xs p-2.5 bg-gray-50 rounded-xl border border-gray-100">
              <span class="flex items-center gap-2 font-medium text-gray-900">
                <span>✖️</span> এক্স (টুইটার) হ্যান্ডেল
              </span>
              <span class="font-semibold text-emerald-600">রেডি ✓</span>
            </div>
          </div>

          <div class="mt-6 pt-4 border-t text-xs text-gray-500 space-y-2">
            <p class="font-semibold text-gray-700">📌 অটোমেশন টিপস:</p>
            <p>• প্রতিটি নিবন্ধ প্রকাশের সময় সাথে সাথে সোশ্যাল চ্যানেলে স্বয়ংক্রিয়ভাবে লিংক চলে যায়।</p>
            <p>• টেলিগ্রাম চ্যানেলে তাৎক্ষণিক ব্রেকিং অ্যালার্ট পাঠানো সম্ভব।</p>
          </div>
        </div>
      </div>

      <!-- Publication History / Logs Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
          <div>
            <h3 class="font-bold text-gray-800 text-base">সাম্প্রতিক সোশ্যাল পাবলিকেশন ইতিহাস</h3>
            <p class="text-xs text-gray-400 mt-0.5">ডাটাবেসে সংরক্ষিত রিয়েল ডিস্ট্রিবিউশন লগ</p>
          </div>
          <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-semibold">
            মোট {{ history.length }} টি লগ
          </span>
        </div>

        <div v-if="history.length === 0" class="p-8 text-center text-sm text-gray-400">
          এখনো কোনো সোশ্যাল পোস্ট প্রকাশিত হয়নি। উপরের ফর্ম থেকে প্রকাশ করুন!
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 bg-gray-50/60 uppercase border-b border-gray-100">
              <tr>
                <th class="px-5 py-3">প্ল্যাটফর্ম ও চ্যানেল</th>
                <th class="px-5 py-3">পোস্ট ক্যাপশন</th>
                <th class="px-5 py-3">সংযুক্ত নিবন্ধ</th>
                <th class="px-5 py-3">স্ট্যাটাস</th>
                <th class="px-5 py-3">প্রকাশের সময়</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr v-for="item in history" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-5 py-3 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <span class="text-base">{{ platformIcon(item.platform) }}</span>
                    <div>
                      <p class="font-semibold text-xs text-gray-800">{{ item.social_account?.account_name || item.platform }}</p>
                      <p class="text-[11px] text-gray-400 font-mono">{{ item.platform_post_id }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3 max-w-xs">
                  <p class="text-xs line-clamp-2 text-gray-800">{{ item.caption }}</p>
                </td>
                <td class="px-5 py-3 text-xs max-w-xs">
                  <span v-if="item.post" class="text-blue-600 font-medium line-clamp-1">
                    {{ item.post.title }}
                  </span>
                  <span v-else class="text-gray-400">সরাসরি পোস্ট</span>
                </td>
                <td class="px-5 py-3 whitespace-nowrap">
                  <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    {{ item.status === 'published' ? 'প্রকাশিত ✓' : item.status }}
                  </span>
                </td>
                <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-400">
                  {{ formatDate(item.published_at || item.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Channel Modal -->
      <div v-if="showAddModal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
          <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-base">নতুন সোশ্যাল চ্যানেল সংযুক্ত করুন</h3>
            <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
          </div>

          <form @submit.prevent="submitAddAccount" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">প্ল্যাটফর্ম <span class="text-red-500">*</span></label>
              <select
                v-model="addForm.platform"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500 bg-white"
                required
              >
                <option value="facebook">📘 ফেসবুক পেজ (Facebook Page)</option>
                <option value="telegram">✈️ টেলিগ্রাম চ্যানেল (Telegram Channel)</option>
                <option value="x">✖️ এক্স / টুইটার (X / Twitter)</option>
                <option value="youtube">▶️ ইউটিউব চ্যানেল (YouTube)</option>
                <option value="instagram">📷 ইনস্টাগ্রাম (Instagram)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">চ্যানেল / অ্যাকাউন্টের নাম <span class="text-red-500">*</span></label>
              <input
                v-model="addForm.account_name"
                type="text"
                placeholder="যেমন: মূল ফেসবুক পেজ বা সংবাদ টেলিগ্রাম"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
                required
              />
              <p v-if="addForm.errors.account_name" class="text-xs text-red-500 mt-1">{{ addForm.errors.account_name }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">হ্যান্ডেল বা আইডি</label>
              <input
                v-model="addForm.account_id"
                type="text"
                placeholder="@mychannel অথবা page_id"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 mb-1">API এক্সেস টোকেন / বট সিক্রেট (ঐচ্ছিক)</label>
              <input
                v-model="addForm.access_token"
                type="password"
                placeholder="গোপন টোকেন থাকলে প্রদান করুন (না থাকলে স্বয়ংক্রিয় তৈরি হবে)"
                class="w-full text-sm px-3 py-2 border rounded-xl outline-none focus:border-red-500"
              />
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t">
              <button
                type="button"
                @click="showAddModal = false"
                class="px-4 py-2 border rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50"
              >
                বাতিল
              </button>
              <button
                type="submit"
                :disabled="addForm.processing"
                class="px-5 py-2 bg-red-600 hover:bg-red-700 disabled:opacity-40 text-white rounded-xl text-sm font-semibold transition-colors"
              >
                {{ addForm.processing ? 'সংরক্ষণ হচ্ছে...' : 'সংরক্ষণ করুন' }}
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
  accounts: {
    type: Array,
    default: () => []
  },
  history: {
    type: Array,
    default: () => []
  },
  recent_articles: {
    type: Array,
    default: () => []
  }
})

const showAddModal = ref(false)

const publishForm = useForm({
  caption: '',
  news_url: '',
  post_id: null,
  account_ids: props.accounts.map(a => a.id)
})

const addForm = useForm({
  platform: 'facebook',
  account_name: '',
  account_id: '',
  access_token: ''
})

const platformIcon = (p) => {
  if (p === 'facebook') return '📘'
  if (p === 'telegram') return '✈️'
  if (p === 'x') return '✖️'
  if (p === 'youtube') return '▶️'
  if (p === 'instagram') return '📷'
  return '🌐'
}

const platformStyle = (p) => {
  if (p === 'facebook') return 'bg-blue-50 text-blue-600'
  if (p === 'telegram') return 'bg-sky-50 text-sky-600'
  if (p === 'youtube') return 'bg-red-50 text-red-600'
  if (p === 'instagram') return 'bg-pink-50 text-pink-600'
  return 'bg-gray-100 text-gray-800'
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleString('bn-BD', {
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch (e) {
    return dateStr
  }
}

const onArticleSelect = (e) => {
  const selectedId = parseInt(e.target.value)
  if (!selectedId) {
    publishForm.post_id = null
    return
  }
  const article = props.recent_articles.find(a => a.id === selectedId)
  if (article) {
    publishForm.post_id = article.id
    publishForm.caption = article.title
    publishForm.news_url = `${window.location.origin}/news/${article.slug}`
  }
}

const submitPublish = () => {
  publishForm.post('/dashboard/social/publish', {
    preserveScroll: true,
    onSuccess: () => {
      publishForm.caption = ''
      publishForm.news_url = ''
      publishForm.post_id = null
    }
  })
}

const submitAddAccount = () => {
  addForm.post('/dashboard/social/accounts', {
    preserveScroll: true,
    onSuccess: () => {
      showAddModal.value = false
      addForm.reset()
      // update default checked accounts
      publishForm.account_ids = props.accounts.map(a => a.id)
    }
  })
}

const removeAccount = (account) => {
  if (confirm(`আপনি কি নিশ্চিত যে "${account.account_name}" চ্যানেলটি মুছে ফেলতে চান?`)) {
    router.delete(`/dashboard/social/accounts/${account.id}`, {
      preserveScroll: true
    })
  }
}
</script>
