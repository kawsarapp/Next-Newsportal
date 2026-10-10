<template>
  <AppLayout title="চাকরির খবর ও শোকবার্তা">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">মডিউল</span>
            <h2 class="text-2xl font-bold text-gray-800">চাকরির খবর ও শোকবার্তা সেকশন</h2>
          </div>
          <p class="text-sm text-gray-500 mt-1">নিয়োগ বিজ্ঞপ্তি ও স্মরণিকা/শোকবার্তা পরিচালনা করুন</p>
        </div>
        <div class="flex items-center gap-3">
          <button 
            v-if="activeTab === 'jobs'"
            @click="showCreateJobModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন চাকরির বিজ্ঞপ্তি
          </button>
          <button 
            v-if="activeTab === 'obituaries'"
            @click="showCreateObituaryModal = true"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 hover:bg-black text-white text-sm font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন শোকবার্তা প্রকাশ
          </button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট চাকরির বিজ্ঞপ্তি</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">💼</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_jobs }} টি</p>
          <span class="text-xs text-amber-600 font-medium">নিয়োগ সার্কুলার</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">চলমান বা সক্রিয় নিয়োগ</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">🟢</div>
          </div>
          <p class="text-2xl font-bold text-emerald-600 mt-2">{{ stats.active_jobs }} টি</p>
          <span class="text-xs text-emerald-600 font-medium">আবেদনের মেয়াদ চলমান</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট শোকবার্তা ও স্মরণিকা</span>
            <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center font-bold">🕯️</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_obituaries }} টি</p>
          <span class="text-xs text-gray-500 font-medium">স্মরণিকা রেকর্ড</span>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="flex items-center gap-3 border-b border-gray-200">
        <button 
          @click="activeTab = 'jobs'"
          :class="['pb-3 px-3 text-sm font-bold border-b-2 transition flex items-center gap-2', activeTab === 'jobs' ? 'border-amber-600 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-800']">
          💼 চাকরির বিজ্ঞপ্তি তালিকা
          <span class="px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'jobs' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600'">{{ jobs.total || jobs.data?.length || 0 }}</span>
        </button>
        <button 
          @click="activeTab = 'obituaries'"
          :class="['pb-3 px-3 text-sm font-bold border-b-2 transition flex items-center gap-2', activeTab === 'obituaries' ? 'border-gray-800 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-800']">
          🕯️ শোকবার্তা ও স্মরণিকা
          <span class="px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'obituaries' ? 'bg-gray-200 text-gray-800' : 'bg-gray-100 text-gray-600'">{{ obituaries.total || obituaries.data?.length || 0 }}</span>
        </button>
      </div>

      <!-- Tab 1: Jobs Content -->
      <div v-if="activeTab === 'jobs'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-xs text-gray-400 font-semibold uppercase">
              <tr>
                <th class="px-5 py-3.5">বিজ্ঞপ্তির শিরোনাম</th>
                <th class="px-5 py-3.5">প্রতিষ্ঠান</th>
                <th class="px-5 py-3.5 text-center">ধরন</th>
                <th class="px-5 py-3.5 text-center">পদসংখ্যা</th>
                <th class="px-5 py-3.5">আবেদনের শেষ তারিখ</th>
                <th class="px-5 py-3.5 text-center">স্ট্যাটাস</th>
                <th class="px-5 py-3.5 text-right">মুছে ফেলুন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="j in jobs.data" :key="j.id" class="hover:bg-gray-50/50 transition">
                <td class="px-5 py-4">
                  <div class="font-bold text-gray-800 text-sm">{{ j.title }}</div>
                  <span v-if="j.location" class="text-xs text-gray-400">📍 {{ j.location }}</span>
                </td>
                <td class="px-5 py-4 font-semibold text-gray-700 text-xs">{{ j.organization }}</td>
                <td class="px-5 py-4 text-center">
                  <span class="px-2 py-0.5 rounded-full text-xs font-semibold uppercase bg-amber-50 text-amber-700">
                    {{ j.job_type }}
                  </span>
                </td>
                <td class="px-5 py-4 text-center text-xs font-medium">{{ j.vacancy_count || '-' }}</td>
                <td class="px-5 py-4 text-xs font-medium text-gray-700">
                  {{ j.deadline ? new Date(j.deadline).toLocaleDateString('bn-BD') : 'নির্দিষ্ট নয়' }}
                </td>
                <td class="px-5 py-4 text-center">
                  <span :class="[
                    'px-2 py-0.5 rounded-full text-xs font-semibold',
                    j.status === 'published' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500'
                  ]">
                    {{ j.status === 'published' ? 'সক্রিয়' : 'খসড়া' }}
                  </span>
                </td>
                <td class="px-5 py-4 text-right">
                  <button 
                    @click="deleteJob(j.id)"
                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" 
                    title="মুছে ফেলুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
              <tr v-if="!jobs.data || jobs.data.length === 0">
                <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                  কোনো চাকরির বিজ্ঞপ্তি পাওয়া যায়নি।
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab 2: Obituaries Content -->
      <div v-if="activeTab === 'obituaries'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-xs text-gray-400 font-semibold uppercase">
              <tr>
                <th class="px-5 py-3.5">মরহুমের নাম</th>
                <th class="px-5 py-3.5 text-center">বয়স</th>
                <th class="px-5 py-3.5">ইন্তেকালের তারিখ</th>
                <th class="px-5 py-3.5">স্থান</th>
                <th class="px-5 py-3.5">জানাজা ও দাফন</th>
                <th class="px-5 py-3.5 text-right">মুছে ফেলুন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="o in obituaries.data" :key="o.id" class="hover:bg-gray-50/50 transition">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-600 font-bold flex items-center justify-center text-xs">
                      {{ o.deceased_name.charAt(0) }}
                    </div>
                    <div>
                      <p class="font-bold text-gray-800 text-sm">{{ o.deceased_name }}</p>
                      <span class="text-xs text-gray-400">{{ o.family_contact }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 text-center text-xs font-semibold">{{ o.age ? o.age + ' বছর' : '-' }}</td>
                <td class="px-5 py-4 text-xs font-medium text-gray-700">
                  {{ o.death_date ? new Date(o.death_date).toLocaleDateString('bn-BD') : 'N/A' }}
                </td>
                <td class="px-5 py-4 text-xs text-gray-600">{{ o.passed_away_place || '-' }}</td>
                <td class="px-5 py-4 text-xs text-gray-500 max-w-xs truncate">{{ o.funeral_details || '-' }}</td>
                <td class="px-5 py-4 text-right">
                  <button 
                    @click="deleteObituary(o.id)"
                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" 
                    title="মুছে ফেলুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
              <tr v-if="!obituaries.data || obituaries.data.length === 0">
                <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                  কোনো শোকবার্তা রেকর্ড নেই।
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Job Modal -->
      <div v-if="showCreateJobModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
          <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-lg">নতুন চাকরির সার্কুলার যুক্ত করুন</h3>
            <button @click="showCreateJobModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="submitJob" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">পদের নাম / শিরোনাম *</label>
              <input v-model="jobForm.title" type="text" required class="w-full border rounded-xl px-3 py-2" placeholder="উদা: সিনিয়র সফটওয়্যার ইঞ্জিনিয়ার" />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">প্রতিষ্ঠানের নাম *</label>
                <input v-model="jobForm.organization" type="text" required class="w-full border rounded-xl px-3 py-2" placeholder="প্রতিষ্ঠানের নাম" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">চাকরির ধরন</label>
                <select v-model="jobForm.job_type" class="w-full border rounded-xl px-3 py-2">
                  <option value="govt">সরকারি</option>
                  <option value="private">বেসরকারি</option>
                  <option value="bank">ব্যাংক</option>
                  <option value="ngo">এনজিও</option>
                  <option value="education">শিক্ষা প্রতিষ্ঠান</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">পদসংখ্যা</label>
                <input v-model="jobForm.vacancy_count" type="text" placeholder="উদা: ২৫ জন" class="w-full border rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">আবেদনের শেষ তারিখ</label>
                <input v-model="jobForm.deadline" type="date" class="w-full border rounded-xl px-3 py-2" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">বেতন সীমা</label>
                <input v-model="jobForm.salary_range" type="text" placeholder="আলোচনা সাপেক্ষে" class="w-full border rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">কর্মস্থল / লোকেশন</label>
                <input v-model="jobForm.location" type="text" placeholder="ঢাকা / সারা বাংলাদেশ" class="w-full border rounded-xl px-3 py-2" />
              </div>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">অনলাইনে আবেদনের লিঙ্ক (URL)</label>
              <input v-model="jobForm.application_url" type="url" placeholder="https://..." class="w-full border rounded-xl px-3 py-2" />
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t">
              <button type="button" @click="showCreateJobModal = false" class="px-4 py-2 border rounded-xl text-gray-600">বাতিল</button>
              <button type="submit" class="px-5 py-2 bg-amber-600 text-white rounded-xl font-semibold hover:bg-amber-700">প্রকাশ করুন</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Create Obituary Modal -->
      <div v-if="showCreateObituaryModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl">
          <div class="flex items-center justify-between border-b pb-3">
            <h3 class="font-bold text-gray-800 text-lg">নতুন শোকবার্তা প্রকাশ</h3>
            <button @click="showCreateObituaryModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="submitObituary" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">মরহুম / পরলোকগতের পুরো নাম *</label>
              <input v-model="obituaryForm.deceased_name" type="text" required class="w-full border rounded-xl px-3 py-2" placeholder="মরহুম..." />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">বয়স</label>
                <input v-model.number="obituaryForm.age" type="number" min="0" placeholder="উদা: ৭২" class="w-full border rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">মৃত্যুর তারিখ</label>
                <input v-model="obituaryForm.death_date" type="date" class="w-full border rounded-xl px-3 py-2" />
              </div>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">ইন্তেকালের স্থান</label>
              <input v-model="obituaryForm.passed_away_place" type="text" placeholder="হাসপাতাল / নিজ বাসভবন" class="w-full border rounded-xl px-3 py-2" />
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">জানাজা ও দাফন সংক্রান্ত বিবরণ</label>
              <textarea v-model="obituaryForm.funeral_details" rows="2" placeholder="জানাজার সময় ও মসজিদের বিবরণ..." class="w-full border rounded-xl px-3 py-2"></textarea>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">সংক্ষিপ্ত পরিচিতি / শোকগাথা</label>
              <textarea v-model="obituaryForm.bio" rows="2" placeholder="মরহুমের সংক্ষিপ্ত জীবনালেখ্য..." class="w-full border rounded-xl px-3 py-2"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t">
              <button type="button" @click="showCreateObituaryModal = false" class="px-4 py-2 border rounded-xl text-gray-600">বাতিল</button>
              <button type="submit" class="px-5 py-2 bg-gray-900 text-white rounded-xl font-semibold hover:bg-black">শোকবার্তা প্রকাশ করুন</button>
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
  tab: String,
  jobs: Object,
  obituaries: Object,
  stats: Object,
})

const activeTab = ref(props.tab || 'jobs')

// Modals
const showCreateJobModal = ref(false)
const showCreateObituaryModal = ref(false)

const jobForm = ref({
  title: '',
  organization: '',
  job_type: 'govt',
  vacancy_count: '',
  salary_range: '',
  location: '',
  deadline: '',
  application_url: '',
  description: '',
  status: 'published',
})

const obituaryForm = ref({
  deceased_name: '',
  age: null,
  death_date: new Date().toISOString().slice(0, 10),
  passed_away_place: '',
  funeral_details: '',
  bio: '',
  family_contact: '',
  status: 'published',
})

function submitJob() {
  router.post(route('module.JobsAndObituary.jobs.store'), jobForm.value, {
    onSuccess: () => {
      showCreateJobModal.value = false
    }
  })
}

function deleteJob(id) {
  if (confirm('আপনি কি নিশ্চিত যে এই চাকরির বিজ্ঞপ্তিটি মুছে ফেলতে চান?')) {
    router.delete(route('module.JobsAndObituary.jobs.destroy', id))
  }
}

function submitObituary() {
  router.post(route('module.JobsAndObituary.obituaries.store'), obituaryForm.value, {
    onSuccess: () => {
      showCreateObituaryModal.value = false
    }
  })
}

function deleteObituary(id) {
  if (confirm('আপনি কি নিশ্চিত যে এই শোকবার্তাটি মুছে ফেলতে চান?')) {
    router.delete(route('module.JobsAndObituary.obituaries.destroy', id))
  }
}
</script>
