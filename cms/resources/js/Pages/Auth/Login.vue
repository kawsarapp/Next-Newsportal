<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-red-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-600 rounded-2xl mb-4 shadow-xl">
          <span class="text-white font-bold text-2xl">N</span>
        </div>
        <h1 class="text-2xl font-bold text-white">Newsportal CMS</h1>
        <p class="text-gray-400 mt-1 text-sm">সম্পাদনা ডেস্কে স্বাগতম</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">লগইন করুন</h2>

        <form @submit.prevent="submit">
          <!-- Email -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ইমেইল</label>
            <input
              v-model="form.email"
              type="email"
              autocomplete="email"
              autofocus
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
              placeholder="admin@example.com"
            >
            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
          </div>

          <!-- Password -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">পাসওয়ার্ড</label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPass ? 'text' : 'password'"
                autocomplete="current-password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm pr-10"
                placeholder="••••••••"
              >
              <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path v-if="!showPass" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
            <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
          </div>

          <!-- Remember -->
          <div class="flex items-center justify-between mb-6">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
              <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
              মনে রাখুন
            </label>
          </div>

          <!-- Error -->
          <div v-if="form.errors.email" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ form.errors.email }}
          </div>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm flex items-center justify-center gap-2"
          >
            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            {{ form.processing ? 'লগইন হচ্ছে...' : 'লগইন করুন' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Head } from '@inertiajs/vue3'

const showPass = ref(false)

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>
