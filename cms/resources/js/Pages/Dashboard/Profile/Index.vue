<template>
  <AppLayout>
    <div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6">
      <!-- Breadcrumb & Page Title -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
            <Link :href="route('dashboard.overview')" class="hover:text-red-600 transition-colors">ড্যাশবোর্ড</Link>
            <span>/</span>
            <span class="text-gray-700 font-medium">প্রোফাইল সেটিংস</span>
          </div>
          <h1 class="text-2xl font-bold text-gray-900">ব্যবহারকারী প্রোফাইল ও নিরাপত্তা</h1>
          <p class="text-sm text-gray-500 mt-1">আপনার ব্যক্তিগত তথ্য, পাসওয়ার্ড ও অ্যাকাউন্ট নিরাপত্তা পরিচালনা করুন</p>
        </div>

        <div class="flex items-center gap-2">
          <span
            v-if="user.two_factor_enabled"
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            ২-ফ্যাক্টর সুরক্ষিত
          </span>
          <span
            v-else
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"
          >
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            ২-ফ্যাক্টর বন্ধ
          </span>
        </div>
      </div>

      <!-- Hero Profile Banner Card -->
      <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-red-950 text-white rounded-3xl p-6 sm:p-8 shadow-sm relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-red-600/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 relative z-10">
          <!-- Avatar Preview / Initials -->
          <div class="relative group">
            <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-800 ring-4 ring-white/20 shadow-xl flex items-center justify-center flex-shrink-0">
              <img
                v-if="avatarPreview || user.avatar"
                :src="avatarPreview || user.avatar"
                alt="Avatar"
                class="w-full h-full object-cover"
              />
              <span v-else class="text-3xl font-bold text-red-400">
                {{ userInitials }}
              </span>
            </div>
          </div>

          <!-- User Details -->
          <div class="space-y-1.5 flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <h2 class="text-2xl font-bold text-white truncate">{{ user.name }}</h2>
              <span
                v-for="role in roles"
                :key="role"
                class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-red-600/30 text-red-200 border border-red-500/30"
              >
                {{ formatRole(role) }}
              </span>
            </div>

            <p class="text-gray-300 text-sm flex flex-wrap items-center gap-x-4 gap-y-1">
              <span class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
                {{ user.email }}
              </span>
              <span v-if="user.username" class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                @{{ user.username }}
              </span>
              <span v-if="user.phone" class="inline-flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ user.phone }}
              </span>
            </p>

            <p v-if="user.bio" class="text-sm text-gray-300 italic pt-1 max-w-3xl line-clamp-2">
              "{{ user.bio }}"
            </p>
          </div>

          <!-- Quick Stats -->
          <div class="flex sm:flex-col items-center sm:items-end gap-2 text-xs text-gray-400 border-t sm:border-t-0 sm:border-l border-white/10 pt-4 sm:pt-0 sm:pl-6">
            <div>পোর্টাল: <span class="font-semibold text-white">{{ user.portal_name || 'সুপার পোর্টাল' }}</span></div>
            <div>যুক্ত হয়েছেন: <span class="text-gray-300">{{ formatDate(user.created_at) }}</span></div>
            <div v-if="user.last_login_at">সর্বশেষ লগইন: <span class="text-gray-300">{{ formatDateTime(user.last_login_at) }}</span></div>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="border-b border-gray-200">
        <nav class="flex gap-4 sm:gap-8 -mb-px overflow-x-auto">
          <button
            @click="activeTab = 'profile'"
            :class="[
              'pb-3.5 px-1 font-semibold text-sm flex items-center gap-2 border-b-2 whitespace-nowrap transition-colors',
              activeTab === 'profile'
                ? 'border-red-600 text-red-600'
                : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            ব্যক্তিগত তথ্য ও প্রোফাইল
          </button>

          <button
            @click="activeTab = 'security'"
            :class="[
              'pb-3.5 px-1 font-semibold text-sm flex items-center gap-2 border-b-2 whitespace-nowrap transition-colors',
              activeTab === 'security'
                ? 'border-red-600 text-red-600'
                : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            নিরাপত্তা ও পাসওয়ার্ড
          </button>

          <button
            @click="activeTab = 'account'"
            :class="[
              'pb-3.5 px-1 font-semibold text-sm flex items-center gap-2 border-b-2 whitespace-nowrap transition-colors',
              activeTab === 'account'
                ? 'border-red-600 text-red-600'
                : 'border-transparent text-gray-500 hover:text-gray-800 hover:border-gray-300'
            ]"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            অ্যাকাউন্ট বিবরণ ও লগইন ইতিহাস
          </button>
        </nav>
      </div>

      <!-- Tab 1: Personal Info -->
      <div v-if="activeTab === 'profile'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7 space-y-6">
          <form @submit.prevent="submitProfile" class="space-y-6">
            <!-- Profile Avatar Upload Section -->
            <div class="border-b border-gray-100 pb-6">
              <label class="block text-sm font-semibold text-gray-800 mb-2">প্রোফাইল ছবি (Avatar)</label>
              <div class="flex flex-col sm:flex-row items-center gap-5">
                <div class="relative w-20 h-20 rounded-2xl overflow-hidden bg-gray-100 border border-gray-200 flex-shrink-0 flex items-center justify-center">
                  <img
                    v-if="avatarPreview || user.avatar"
                    :src="avatarPreview || user.avatar"
                    alt="Preview"
                    class="w-full h-full object-cover"
                  />
                  <span v-else class="text-xl font-bold text-gray-400">
                    {{ userInitials }}
                  </span>
                </div>

                <div class="space-y-2 flex-1 text-center sm:text-left">
                  <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start">
                    <label class="cursor-pointer px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition-colors border border-gray-200 inline-flex items-center gap-1.5">
                      <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      ছবি পরিবর্তন করুন
                      <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleAvatarChange"
                      />
                    </label>

                    <button
                      v-if="avatarPreview || user.avatar"
                      type="button"
                      @click="removeAvatar"
                      class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-xl transition-colors inline-flex items-center gap-1"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      মুছে ফেলুন
                    </button>
                  </div>
                  <p class="text-xs text-gray-500">অনুমোদিত ফরম্যাট: JPG, PNG, WEBP (সর্বোচ্চ ২ মেগাবাইট)</p>
                  <p v-if="profileForm.errors.avatar" class="text-xs text-red-600 mt-1 font-medium">{{ profileForm.errors.avatar }}</p>
                </div>
              </div>
            </div>

            <!-- Basic Info Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                  পূর্ণ নাম <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="profileForm.name"
                  type="text"
                  required
                  placeholder="আপনার পূর্ণ নাম লিখুন"
                  class="w-full text-sm px-3.5 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                  :class="profileForm.errors.name ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
                />
                <p v-if="profileForm.errors.name" class="text-xs text-red-600 mt-1 font-medium">{{ profileForm.errors.name }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                  ইমেইল ঠিকানা <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="profileForm.email"
                  type="email"
                  required
                  placeholder="example@newsportal.com"
                  class="w-full text-sm px-3.5 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                  :class="profileForm.errors.email ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
                />
                <p v-if="profileForm.errors.email" class="text-xs text-red-600 mt-1 font-medium">{{ profileForm.errors.email }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                  ইউজারনেম (ব্যবহারকারী আইডি)
                </label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 text-sm">@</span>
                  <input
                    v-model="profileForm.username"
                    type="text"
                    placeholder="username"
                    class="w-full text-sm pl-8 pr-3.5 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                    :class="profileForm.errors.username ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
                  />
                </div>
                <p v-if="profileForm.errors.username" class="text-xs text-red-600 mt-1 font-medium">{{ profileForm.errors.username }}</p>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                  মোবাইল নম্বর
                </label>
                <input
                  v-model="profileForm.phone"
                  type="text"
                  placeholder="০১৭xxxxxxxx"
                  class="w-full text-sm px-3.5 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                  :class="profileForm.errors.phone ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
                />
                <p v-if="profileForm.errors.phone" class="text-xs text-red-600 mt-1 font-medium">{{ profileForm.errors.phone }}</p>
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                সংক্ষিপ্ত পরিচিতি ও বায়ো (Bio / Designation)
              </label>
              <textarea
                v-model="profileForm.bio"
                rows="4"
                placeholder="আপনার সাংবাদিকতা বা কর্মজীবন সংক্রান্ত সংক্ষিপ্ত বিবরণ লিখুন..."
                class="w-full text-sm px-3.5 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all resize-y"
                :class="profileForm.errors.bio ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
              ></textarea>
              <div class="flex items-center justify-between text-xs text-gray-400 mt-1">
                <span>সর্বোচ্চ ৫০০ অক্ষর</span>
                <span>{{ (profileForm.bio || '').length }}/500</span>
              </div>
              <p v-if="profileForm.errors.bio" class="text-xs text-red-600 mt-1 font-medium">{{ profileForm.errors.bio }}</p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
              <button
                type="button"
                @click="resetProfileForm"
                class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-xl transition-colors"
                :disabled="profileForm.processing"
              >
                রিসেট
              </button>
              <button
                type="submit"
                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all inline-flex items-center gap-2"
                :disabled="profileForm.processing"
              >
                <svg v-if="profileForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>{{ profileForm.processing ? 'সংরক্ষণ হচ্ছে...' : 'প্রোফাইল পরিবর্তন সংরক্ষণ করুন' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Sidebar Info Column -->
        <div class="space-y-6">
          <!-- Role & Permissions Box -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
              <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
              অ্যাসাইন করা ভূমিকা
            </h3>
            <p class="text-xs text-gray-500 leading-relaxed">
              আপনার অ্যাকাউন্টে অনুমোদিত ভূমিকা এবং সিস্টেম পারমিশন লেভেল:
            </p>

            <div class="space-y-2">
              <div
                v-for="role in roles"
                :key="role"
                class="p-3 bg-red-50/60 rounded-xl border border-red-100 flex items-center justify-between"
              >
                <div>
                  <div class="text-sm font-bold text-red-900">{{ formatRole(role) }}</div>
                  <div class="text-xs text-red-600 font-mono">{{ role }}</div>
                </div>
                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-red-600 text-white">সক্রিয়</span>
              </div>
            </div>

            <div v-if="user.reporter_type" class="pt-2 border-t border-gray-100">
              <span class="text-xs text-gray-500">রিপোর্টার ধরন:</span>
              <div class="text-sm font-semibold text-gray-800">{{ user.reporter_type }}</div>
            </div>

            <div v-if="user.institution" class="pt-2">
              <span class="text-xs text-gray-500">প্রতিষ্ঠান/বিশ্ববিদ্যালয়:</span>
              <div class="text-sm font-semibold text-gray-800">{{ user.institution }}</div>
            </div>
          </div>

          <!-- Quick Tip Card -->
          <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-amber-200 p-5 space-y-2 text-amber-900">
            <div class="flex items-center gap-2 font-bold text-sm">
              <span>💡</span> প্রোফাইল সম্পূর্ণতা
            </div>
            <p class="text-xs leading-relaxed text-amber-800">
              সঠিক নাম, মোবাইল নম্বর ও সংক্ষিপ্ত বায়ো যুক্ত রাখলে নিউজের লেখক কার্ডে ও প্রেস ক্রেডেনশিয়ালে আপনার সঠিক পরিচিতি প্রদর্শিত হবে।
            </p>
          </div>
        </div>
      </div>

      <!-- Tab 2: Security & Password -->
      <div v-if="activeTab === 'security'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Password Change Form -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-7 space-y-6">
          <div>
            <h3 class="font-bold text-gray-900 text-lg">পাসওয়ার্ড পরিবর্তন করুন</h3>
            <p class="text-xs text-gray-500 mt-1">অ্যাকাউন্টের সর্বোচ্চ সুরক্ষায় নিয়মিত শক্তিশালী পাসওয়ার্ড ব্যবহার করুন</p>
          </div>

          <form @submit.prevent="submitPassword" class="space-y-5">
            <!-- Current Password -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                বর্তমান পাসওয়ার্ড <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.current_password"
                  :type="showCurrentPassword ? 'text' : 'password'"
                  required
                  autocomplete="current-password"
                  placeholder="আপনার বর্তমান পাসওয়ার্ড দিন"
                  class="w-full text-sm px-3.5 py-2.5 pr-10 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                  :class="passwordForm.errors.current_password ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
                />
                <button
                  type="button"
                  @click="showCurrentPassword = !showCurrentPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                >
                  <svg v-if="showCurrentPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
              <p v-if="passwordForm.errors.current_password" class="text-xs text-red-600 mt-1 font-medium">{{ passwordForm.errors.current_password }}</p>
            </div>

            <!-- New Password -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                নতুন পাসওয়ার্ড <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="passwordForm.password"
                  :type="showNewPassword ? 'text' : 'password'"
                  required
                  autocomplete="new-password"
                  placeholder="কমপক্ষে ৮ অক্ষরের নতুন পাসওয়ার্ড"
                  class="w-full text-sm px-3.5 py-2.5 pr-10 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all"
                  :class="passwordForm.errors.password ? 'border-red-300 bg-red-50/20' : 'border-gray-200'"
                />
                <button
                  type="button"
                  @click="showNewPassword = !showNewPassword"
                  class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                >
                  <svg v-if="showNewPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                  </svg>
                  <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </button>
              </div>
              <p v-if="passwordForm.errors.password" class="text-xs text-red-600 mt-1 font-medium">{{ passwordForm.errors.password }}</p>
            </div>

            <!-- Confirm Password -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                নতুন পাসওয়ার্ড নিশ্চিত করুন <span class="text-red-500">*</span>
              </label>
              <input
                v-model="passwordForm.password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                placeholder="নতুন পাসওয়ার্ডটি পুনরায় লিখুন"
                class="w-full text-sm px-3.5 py-2.5 border rounded-xl outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all border-gray-200"
              />
            </div>

            <!-- Password Requirements Checklist -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 space-y-2 text-xs">
              <span class="font-bold text-gray-700">পাসওয়ার্ডের প্রয়োজনীয় শর্তসমূহ:</span>
              <ul class="space-y-1.5 text-gray-600">
                <li class="flex items-center gap-2">
                  <svg
                    :class="(passwordForm.password || '').length >= 8 ? 'text-emerald-500' : 'text-gray-300'"
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  কমপক্ষে ৮টি অক্ষর
                </li>
                <li class="flex items-center gap-2">
                  <svg
                    :class="passwordForm.password && passwordForm.password === passwordForm.password_confirmation ? 'text-emerald-500' : 'text-gray-300'"
                    class="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  পাসওয়ার্ড নিশ্চিতকরণ মিল থাকতে হবে
                </li>
              </ul>
            </div>

            <!-- Submit Button -->
            <div class="pt-2 flex justify-end">
              <button
                type="submit"
                class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow transition-all inline-flex items-center gap-2"
                :disabled="passwordForm.processing"
              >
                <svg v-if="passwordForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>{{ passwordForm.processing ? 'আপডেট হচ্ছে...' : 'পাসওয়ার্ড পরিবর্তন করুন' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- 2FA & Security Sidebar Column -->
        <div class="space-y-6">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-2 text-gray-900 font-bold text-base">
              <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.097.99-4.316.99-6.613a20 20 0 00-.776-5.5M6.24 6.24l11.52 11.52" />
              </svg>
              দ্বি-স্তর বিশিষ্ট নিরাপত্তা (2FA)
            </div>

            <p class="text-xs text-gray-500 leading-relaxed">
              আপনার অ্যাকাউন্টে লগইন করার সময় বাড়তি স্তরের নিরাপত্তা নিশ্চিত করে অননুমোদিত অ্যাক্সেস প্রতিরোধ করুন।
            </p>

            <div class="p-4 rounded-xl border flex items-center justify-between" :class="user.two_factor_enabled ? 'bg-emerald-50/60 border-emerald-200' : 'bg-gray-50 border-gray-200'">
              <div>
                <div class="text-sm font-bold" :class="user.two_factor_enabled ? 'text-emerald-900' : 'text-gray-800'">
                  {{ user.two_factor_enabled ? '২-ফ্যাক্টর সক্রিয় আছে' : '২-ফ্যাক্টর নিষ্ক্রিয়' }}
                </div>
                <div class="text-xs text-gray-500">
                  {{ user.two_factor_enabled ? 'লগইন অনুমোদনের জন্য সুরক্ষিত' : 'বর্তমানে বাড়তি স্তর বন্ধ রয়েছে' }}
                </div>
              </div>

              <button
                type="button"
                @click="toggle2fa(!user.two_factor_enabled)"
                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all shadow-sm"
                :class="user.two_factor_enabled ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white'"
              >
                {{ user.two_factor_enabled ? 'নিষ্ক্রিয় করুন' : 'সক্রিয় করুন' }}
              </button>
            </div>
          </div>

          <!-- Security Tip -->
          <div class="bg-gray-900 text-white rounded-2xl p-6 space-y-3 shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm text-red-400">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              নিরাপত্তা সতর্কতা
            </div>
            <p class="text-xs text-gray-300 leading-relaxed">
              কখনোই আপনার পাসওয়ার্ড অন্য কারো সাথে শেয়ার করবেন না। পাবলিক বা শেয়ার্ড কম্পিউটারে কাজ শেষে সর্বদা লগ আউট নিশ্চিত করুন।
            </p>
          </div>
        </div>
      </div>

      <!-- Tab 3: Account Overview & Sessions -->
      <div v-if="activeTab === 'account'" class="space-y-6">
        <!-- Grid of Account Details Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">অ্যাকাউন্ট তৈরি</span>
            <div class="text-base font-bold text-gray-800">{{ formatDate(user.created_at) }}</div>
            <div class="text-xs text-gray-500">{{ formatDateTime(user.created_at) }}</div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">সর্বশেষ সফল লগইন</span>
            <div class="text-base font-bold text-gray-800">{{ user.last_login_at ? formatDateTime(user.last_login_at) : 'অজানা' }}</div>
            <div class="text-xs text-gray-500 font-mono">IP: {{ user.last_login_ip || '127.0.0.1' }}</div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm space-y-1">
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">সংযুক্ত পোর্টাল</span>
            <div class="text-base font-bold text-gray-800">{{ user.portal_name || 'সর্বজনীন অ্যাডমিন (সকল পোর্টাল)' }}</div>
            <div class="text-xs text-emerald-600 font-semibold">স্ট্যাটাস: সক্রিয় (Active)</div>
          </div>
        </div>

        <!-- Permissions Badge Grid -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="font-bold text-gray-900 text-base">আপনার অনুমোদিত পারমিশনসমূহ (Permissions)</h3>
              <p class="text-xs text-gray-500">এই অ্যাকাউন্টের আওতাধীন সিস্টেম ক্ষমতা ও অ্যাক্সেস পারমিশন তালিকা</p>
            </div>
            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-lg">
              মোট: {{ permissions.length }} টি পারমিশন
            </span>
          </div>

          <div class="flex flex-wrap gap-2 max-h-48 overflow-y-auto p-1">
            <span
              v-for="perm in permissions"
              :key="perm"
              class="px-2.5 py-1 bg-gray-50 hover:bg-red-50 text-gray-700 hover:text-red-700 border border-gray-200 hover:border-red-200 rounded-lg text-xs font-mono transition-colors"
            >
              {{ perm }}
            </span>
            <span v-if="permissions.length === 0" class="text-sm text-gray-500 italic">
              সরাসরি কোনো স্পেসিফিক পারমিশন নেই (রোল ভিত্তিক ক্ষমতা সক্রিয়)।
            </span>
          </div>
        </div>

        <!-- Login History Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
              <h3 class="font-bold text-gray-900 text-base">সাম্প্রতিক লগইন ইতিহাস (Login History)</h3>
              <p class="text-xs text-gray-500 mt-0.5">আপনার অ্যাকাউন্টে সাম্প্রতিক ডিভাইসের লগইন প্রচেষ্টা ও আইপি</p>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead class="bg-gray-50 text-xs uppercase text-gray-500 border-b border-gray-100">
                <tr>
                  <th class="py-3.5 px-5">আইপি অ্যাড্রেস</th>
                  <th class="py-3.5 px-5">ডিভাইস / ব্রাউজার</th>
                  <th class="py-3.5 px-5">স্ট্যাটাস</th>
                  <th class="py-3.5 px-5 text-right">সময়</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-50">
                <tr v-for="item in sessions" :key="item.id" class="hover:bg-gray-50/80 transition-colors">
                  <td class="py-3.5 px-5 font-mono text-xs text-gray-800 font-semibold">{{ item.ip_address }}</td>
                  <td class="py-3.5 px-5 text-xs text-gray-600 max-w-xs truncate" :title="item.user_agent">
                    {{ item.device_name || formatUserAgent(item.user_agent) }}
                  </td>
                  <td class="py-3.5 px-5">
                    <span
                      class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      :class="item.status === 'success' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'"
                    >
                      {{ item.status === 'success' ? 'সফল' : 'ব্যর্থ' }}
                    </span>
                  </td>
                  <td class="py-3.5 px-5 text-right text-xs text-gray-500">
                    <div>{{ item.time_human }}</div>
                    <div class="text-[10px] text-gray-400">{{ formatDateTime(item.created_at) }}</div>
                  </td>
                </tr>
                <tr v-if="sessions.length === 0">
                  <td colspan="4" class="py-6 text-center text-sm text-gray-400 italic">
                    কোনো লগইন রেকর্ড পাওয়া যায়নি
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Audit Activities -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 text-base">সাম্প্রতিক কার্যকলাপ লগ (Audit Log)</h3>
            <p class="text-xs text-gray-500 mt-0.5">আপনার অ্যাকাউন্টের সাম্প্রতিক সংরক্ষিত অ্যাকশনসমূহ</p>
          </div>

          <div class="divide-y divide-gray-50">
            <div v-for="act in activities" :key="act.id" class="p-4 hover:bg-gray-50 flex items-center justify-between text-sm transition-colors">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-bold">
                  ⚡
                </div>
                <div>
                  <div class="font-semibold text-gray-800 text-xs font-mono">{{ act.action }}</div>
                  <div class="text-[11px] text-gray-400">আইপি: {{ act.ip_address }}</div>
                </div>
              </div>
              <div class="text-right text-xs text-gray-500">
                <div>{{ act.time_human }}</div>
                <div class="text-[10px] text-gray-400">{{ formatDateTime(act.created_at) }}</div>
              </div>
            </div>
            <div v-if="activities.length === 0" class="p-6 text-center text-sm text-gray-400 italic">
              কোনো সাম্প্রতিক অডিট রেকর্ড নেই
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  roles: {
    type: Array,
    default: () => [],
  },
  permissions: {
    type: Array,
    default: () => [],
  },
  sessions: {
    type: Array,
    default: () => [],
  },
  activities: {
    type: Array,
    default: () => [],
  },
})

const activeTab = ref('profile')
const avatarPreview = ref(null)
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)

const userInitials = computed(() => {
  const name = props.user?.name ?? 'ইউজার'
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
})

// Tab 1: Profile Form
const profileForm = useForm({
  name: props.user.name || '',
  email: props.user.email || '',
  username: props.user.username || '',
  phone: props.user.phone || '',
  bio: props.user.bio || '',
  avatar: null,
  remove_avatar: false,
})

const handleAvatarChange = (e) => {
  const file = e.target.files[0]
  if (!file) return

  profileForm.avatar = file
  profileForm.remove_avatar = false

  const reader = new FileReader()
  reader.onload = (event) => {
    avatarPreview.value = event.target.result
  }
  reader.readAsDataURL(file)
}

const removeAvatar = () => {
  profileForm.avatar = null
  profileForm.remove_avatar = true
  avatarPreview.value = null
}

const resetProfileForm = () => {
  profileForm.reset()
  avatarPreview.value = null
}

const submitProfile = () => {
  profileForm.post(route('dashboard.profile.update'), {
    preserveScroll: true,
    forceFormData: true,
    onSuccess: () => {
      avatarPreview.value = null
    },
  })
}

// Tab 2: Password Form
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submitPassword = () => {
  passwordForm.put(route('dashboard.profile.password'), {
    preserveScroll: true,
    onSuccess: () => {
      passwordForm.reset()
    },
  })
}

// 2FA Toggle
const toggle2fa = (enable) => {
  const routeName = enable ? 'dashboard.profile.2fa.enable' : 'dashboard.profile.2fa.disable'
  router.post(route(routeName), {}, {
    preserveScroll: true,
  })
}

// Helper formats
const formatRole = (role) => {
  const map = {
    super_admin: 'সুপার অ্যাডমিন',
    portal_admin: 'পোর্টাল অ্যাডমিন',
    chief_editor: 'প্রধান সম্পাদক',
    editor: 'সম্পাদক',
    sub_editor: 'সহ-সম্পাদক',
    reporter: 'স্টাফ রিপোর্টার',
    district_reporter: 'জেলা প্রতিনিধি',
    campus_reporter: 'ক্যাম্পাস প্রতিনিধি',
  }
  return map[role] || role
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('bn-BD', { year: 'numeric', month: 'long', day: 'numeric' })
  } catch (e) {
    return dateStr
  }
}

const formatDateTime = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleString('bn-BD', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch (e) {
    return dateStr
  }
}

const formatUserAgent = (ua) => {
  if (!ua) return 'ব্রাউজার'
  if (ua.includes('Chrome')) return 'Chrome ব্রাউজার'
  if (ua.includes('Firefox')) return 'Firefox ব্রাউজার'
  if (ua.includes('Safari')) return 'Safari ব্রাউজার'
  if (ua.includes('Edge')) return 'Edge ব্রাউজার'
  return ua.slice(0, 40) + '...'
}
</script>
