<template>
  <AppLayout title="নিউজ কুইজ ও ট্রিভিয়া">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">মডিউল</span>
            <h2 class="text-2xl font-bold text-gray-800">নিউজ কুইজ ও ট্রিভিয়া ম্যানেজার</h2>
          </div>
          <p class="text-sm text-gray-500 mt-1">কারেন্ট অ্যাফেয়ার্স ও সাধারণ জ্ঞানের কুইজ তৈরি করে পাঠকদের ব্যস্ত রাখুন</p>
        </div>
        <div>
          <button 
            @click="openCreateModal"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন কুইজ তৈরি করুন
          </button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট কুইজ</span>
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">🏆</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_quizzes }} টি</p>
          <span class="text-xs text-indigo-600 font-medium">কুইজ সংগ্রহ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">প্রকাশিত লাইভ কুইজ</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">🟢</div>
          </div>
          <p class="text-2xl font-bold text-emerald-600 mt-2">{{ stats.published_quizzes }} টি</p>
          <span class="text-xs text-emerald-600 font-medium">পাঠকদের জন্য উন্মুক্ত</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট অংশগ্রহণকারী</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">👥</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_participants }} বার</p>
          <span class="text-xs text-amber-600 font-medium">কুইজে উত্তর প্রদান</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">গড় স্কোর</span>
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">📊</div>
          </div>
          <p class="text-2xl font-bold text-purple-600 mt-2">{{ stats.avg_score }}%</p>
          <span class="text-xs text-purple-600 font-medium">সঠিক উত্তরের হার</span>
        </div>
      </div>

      <!-- Quizzes List -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="font-bold text-gray-800 text-sm">তৈরিকৃত কুইজ তালিকা</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-xs text-gray-400 font-semibold uppercase">
              <tr>
                <th class="px-5 py-3.5">কুইজের শিরোনাম</th>
                <th class="px-5 py-3.5 text-center">প্রশ্ন সংখ্যা</th>
                <th class="px-5 py-3.5 text-center">সময়সীমা</th>
                <th class="px-5 py-3.5 text-center">অংশগ্রহণ</th>
                <th class="px-5 py-3.5 text-center">স্ট্যাটাস</th>
                <th class="px-5 py-3.5">তৈরির তারিখ</th>
                <th class="px-5 py-3.5 text-right">অ্যাকশন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="q in quizzes.data" :key="q.id" class="hover:bg-gray-50/50 transition">
                <td class="px-5 py-4">
                  <div class="font-bold text-gray-800 text-sm">{{ q.title }}</div>
                  <span class="text-xs text-gray-400 font-mono">{{ q.slug }}</span>
                </td>
                <td class="px-5 py-4 text-center font-semibold text-gray-700">{{ q.questions_count }} টি</td>
                <td class="px-5 py-4 text-center text-xs text-gray-500">{{ q.time_limit_minutes }} মিনিট</td>
                <td class="px-5 py-4 text-center font-bold text-indigo-600">{{ q.attempts_count }} বার</td>
                <td class="px-5 py-4 text-center">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                    q.status === 'published' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                    q.status === 'draft' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                    'bg-gray-100 text-gray-600'
                  ]">
                    {{ q.status === 'published' ? 'প্রকাশিত' : q.status === 'draft' ? 'খসড়া' : 'আর্কাইভ' }}
                  </span>
                </td>
                <td class="px-5 py-4 text-xs text-gray-400">{{ new Date(q.created_at).toLocaleDateString('bn-BD') }}</td>
                <td class="px-5 py-4 text-right space-x-2">
                  <button 
                    v-if="q.status === 'draft'"
                    @click="updateStatus(q.id, 'published')"
                    class="p-1.5 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition"
                    title="পাবলিশ করুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </button>
                  <button 
                    v-if="q.status === 'published'"
                    @click="updateStatus(q.id, 'draft')"
                    class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50 transition"
                    title="ড্রাফট করুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  </button>
                  <button 
                    @click="deleteQuiz(q.id)"
                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" 
                    title="মুছে ফেলুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
              <tr v-if="!quizzes.data || quizzes.data.length === 0">
                <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                  এখনো কোনো কুইজ তৈরি করা হয়নি।
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Quiz Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-5 shadow-xl max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800 text-lg">নতুন কুইজ ও প্রশ্নমালা তৈরি করুন</h3>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="submitQuiz" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">কুইজের শিরোনাম *</label>
              <input v-model="form.title" type="text" required placeholder="উদা: সাপ্তাহিক আন্তর্জাতিক কুইজ ২০২৬" class="w-full border border-gray-200 rounded-xl px-3 py-2" />
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">বর্ণনা / নিয়মাবলী (ঐচ্ছিক)</label>
              <textarea v-model="form.description" rows="2" placeholder="কুইজের নিয়মাবলী লিখুন..." class="w-full border border-gray-200 rounded-xl px-3 py-2"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">সময়সীমা (মিনিট)</label>
                <input v-model.number="form.time_limit_minutes" type="number" min="1" max="120" required class="w-full border border-gray-200 rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">পাস মার্ক (%)</label>
                <input v-model.number="form.passing_score" type="number" min="1" max="100" required class="w-full border border-gray-200 rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">স্ট্যাটাস</label>
                <select v-model="form.status" class="w-full border border-gray-200 rounded-xl px-3 py-2">
                  <option value="draft">ড্রাফট (Draft)</option>
                  <option value="published">লাইভ পাবলিশ</option>
                </select>
              </div>
            </div>

            <!-- Questions Builder -->
            <div class="space-y-4 pt-2 border-t border-gray-100">
              <div class="flex items-center justify-between">
                <h4 class="font-bold text-gray-800 text-sm">প্রশ্নাবলী (মোট: {{ form.questions.length }} টি)</h4>
                <button type="button" @click="addQuestion" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition">
                  + নতুন প্রশ্ন যোগ করুন
                </button>
              </div>

              <div v-for="(q, qIndex) in form.questions" :key="qIndex" class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3 relative">
                <div class="flex items-center justify-between">
                  <span class="font-bold text-xs text-indigo-600">প্রশ্ন #{{ qIndex + 1 }}</span>
                  <button v-if="form.questions.length > 1" type="button" @click="removeQuestion(qIndex)" class="text-xs text-red-500 hover:underline">
                    মুছুন
                  </button>
                </div>

                <input v-model="q.question" type="text" required placeholder="প্রশ্নটি লিখুন..." class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-white" />

                <!-- Options -->
                <div class="space-y-2">
                  <span class="text-xs font-semibold text-gray-500">অপশনসমূহ (সঠিক উত্তরটিতে টিক দিন):</span>
                  <div v-for="(opt, oIndex) in q.options" :key="oIndex" class="flex items-center gap-2">
                    <input 
                      type="radio" 
                      :name="'correct_' + qIndex" 
                      :checked="opt.is_correct" 
                      @change="setCorrectOption(qIndex, oIndex)" 
                      class="text-indigo-600 focus:ring-indigo-500" 
                    />
                    <input v-model="opt.text" type="text" required :placeholder="'অপশন ' + (oIndex + 1)" class="flex-1 border border-gray-200 rounded-lg px-3 py-1.5 text-xs bg-white" />
                  </div>
                </div>

                <div>
                  <input v-model="q.explanation" type="text" placeholder="উত্তরের ব্যাখ্যা / তথ্যসূত্র (ঐচ্ছিক)" class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs bg-white" />
                </div>
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
              <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50">বাতিল</button>
              <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-semibold">কুইজ সেভ করুন</button>
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
  quizzes: Object,
  stats: Object,
})

const showCreateModal = ref(false)

const form = ref({
  title: '',
  description: '',
  time_limit_minutes: 5,
  passing_score: 60,
  status: 'draft',
  questions: [
    {
      question: '',
      options: [
        { text: '', is_correct: true },
        { text: '', is_correct: false },
        { text: '', is_correct: false },
        { text: '', is_correct: false },
      ],
      explanation: '',
    }
  ]
})

function openCreateModal() {
  form.value = {
    title: '',
    description: '',
    time_limit_minutes: 5,
    passing_score: 60,
    status: 'draft',
    questions: [
      {
        question: '',
        options: [
          { text: '', is_correct: true },
          { text: '', is_correct: false },
          { text: '', is_correct: false },
          { text: '', is_correct: false },
        ],
        explanation: '',
      }
    ]
  }
  showCreateModal.value = true
}

function addQuestion() {
  form.value.questions.push({
    question: '',
    options: [
      { text: '', is_correct: true },
      { text: '', is_correct: false },
      { text: '', is_correct: false },
      { text: '', is_correct: false },
    ],
    explanation: '',
  })
}

function removeQuestion(index) {
  form.value.questions.splice(index, 1)
}

function setCorrectOption(qIndex, oIndex) {
  form.value.questions[qIndex].options.forEach((opt, idx) => {
    opt.is_correct = (idx === oIndex)
  })
}

function submitQuiz() {
  router.post(route('module.NewsQuiz.store'), form.value, {
    onSuccess: () => {
      showCreateModal.value = false
    }
  })
}

function updateStatus(id, newStatus) {
  router.patch(route('module.NewsQuiz.update-status', id), { status: newStatus })
}

function deleteQuiz(id) {
  if (confirm('আপনি কি নিশ্চিত যে এই কুইজটি মুছে ফেলতে চান?')) {
    router.delete(route('module.NewsQuiz.destroy', id))
  }
}
</script>
