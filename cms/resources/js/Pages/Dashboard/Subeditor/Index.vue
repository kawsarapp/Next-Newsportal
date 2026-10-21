<template>
  <AppLayout>
    <div class="p-4 sm:p-6 space-y-8 max-w-7xl mx-auto">
      <!-- Flash Messages -->
      <div
        v-if="$page.props.flash?.success"
        class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center justify-between shadow-sm animate-fadeIn"
      >
        <div class="flex items-center gap-2.5">
          <span class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">✓</span>
          <span class="font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <div
        v-if="$page.props.flash?.error"
        class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-sm flex items-center justify-between shadow-sm animate-fadeIn"
      >
        <div class="flex items-center gap-2.5">
          <span class="w-6 h-6 rounded-full bg-red-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">✕</span>
          <span class="font-medium">{{ $page.props.flash.error }}</span>
        </div>
      </div>

      <!-- Hero Header Section -->
      <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-gray-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-indigo-900/40">
        <!-- Background decorative ambient circles -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
          <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2.5">
              <!-- Subeditor24 Logo Badge -->
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-red-600 to-rose-600 text-white shadow-md shadow-red-600/30">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Subeditor24 Bridge
              </span>

              <!-- Status Indicator Badge -->
              <span
                v-if="form.subeditor_enabled"
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30"
              >
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                সার্ভিস সক্রিয় (Active)
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-700/60 text-gray-400 border border-gray-600/40"
              >
                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                সার্ভিস বন্ধ (Disabled)
              </span>

              <!-- Mode Badge -->
              <span
                v-if="form.subeditor_auto_publish"
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300 border border-blue-500/30"
              >
                ⚡ অটো-পাবলিশ মোড
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/20 text-amber-300 border border-amber-500/30"
              >
                📝 ড্রাফট রিভিউ মোড
              </span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-2">
              Subeditor24 ইন্টিগ্রেশন ও অটোমেশন কন্ট্রোল
            </h1>
            <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
              Subeditor24 ডেক্সটপ বা ওয়েব অ্যাপ্লিকেশন থেকে সরাসরি আপনার নিউজ পোর্টালে সংবাদ সিন্ডিকেশন, ক্যাটাগরি ম্যাপিং ও লাইভ পাবলিশিং চ্যানেল নিয়ন্ত্রণ করুন।
            </p>
          </div>

          <!-- Quick Stats Pills -->
          <div class="flex flex-wrap lg:flex-nowrap gap-3 shrink-0">
            <div class="bg-white/5 backdrop-blur border border-white/10 px-4 py-3 rounded-2xl flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-indigo-500/20 border border-indigo-400/20 flex items-center justify-center text-indigo-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-400 font-medium">রিসিভড সংবাদ</p>
                <p class="text-lg font-bold text-white">{{ logs.total ?? logs.data?.length ?? 0 }} টি</p>
              </div>
            </div>

            <div class="bg-white/5 backdrop-blur border border-white/10 px-4 py-3 rounded-2xl flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-rose-500/20 border border-rose-400/20 flex items-center justify-center text-rose-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                </svg>
              </div>
              <div>
                <p class="text-xs text-slate-400 font-medium">সক্রিয় ক্যাটাগরি</p>
                <p class="text-lg font-bold text-white">{{ categories.length }} টি</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column: Credentials & Settings (7 cols) -->
        <div class="lg:col-span-7 space-y-8">
          <!-- Connection Credentials Card -->
          <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-gray-100 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-bold text-gray-900">কানেকশন এন্ডপয়েন্ট ও সিক্রেট টোকেন</h2>
                  <p class="text-xs text-gray-500">Subeditor24 সফটওয়্যারে কানেক্ট করার জন্য নিচের তথ্যগুলো ব্যবহার করুন</p>
                </div>
              </div>
            </div>

            <!-- Webhook URL Field -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-gray-700 tracking-wide">
                সংবাদ রিসিভ Webhook URL
              </label>
              <div class="relative flex items-center">
                <input
                  type="text"
                  readonly
                  :value="endpoints.webhook_url"
                  class="w-full text-xs sm:text-sm bg-gray-50 border border-gray-200 rounded-xl pl-3 pr-28 py-2.5 text-gray-700 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                />
                <button
                  type="button"
                  @click="copyToClipboard(endpoints.webhook_url, 'webhook_url')"
                  class="absolute right-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 shadow-sm"
                  :class="copiedField === 'webhook_url' ? 'bg-emerald-600 text-white' : 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-200'"
                >
                  <span v-if="copiedField === 'webhook_url'">✓ কপি হয়েছে</span>
                  <span v-else class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    কপি করুন
                  </span>
                </button>
              </div>
              <p class="text-[11px] text-gray-400">
                বিকল্প এন্ডপয়েন্ট: <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">{{ endpoints.webhook_url_alias }}</code>
              </p>
            </div>

            <!-- Category Sync URL Field -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-gray-700 tracking-wide">
                ক্যাটাগরি সিঙ্ক URL (Category Sync API)
              </label>
              <div class="relative flex items-center">
                <input
                  type="text"
                  readonly
                  :value="endpoints.category_url"
                  class="w-full text-xs sm:text-sm bg-gray-50 border border-gray-200 rounded-xl pl-3 pr-28 py-2.5 text-gray-700 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                />
                <button
                  type="button"
                  @click="copyToClipboard(endpoints.category_url, 'category_url')"
                  class="absolute right-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 shadow-sm"
                  :class="copiedField === 'category_url' ? 'bg-emerald-600 text-white' : 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-200'"
                >
                  <span v-if="copiedField === 'category_url'">✓ কপি হয়েছে</span>
                  <span v-else class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                    </svg>
                    কপি করুন
                  </span>
                </button>
              </div>
              <p class="text-[11px] text-gray-400">
                বিকল্প এন্ডপয়েন্ট: <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">{{ endpoints.category_url_alias }}</code>
              </p>
            </div>

            <!-- Secret API Token Field -->
            <div class="space-y-1.5 pt-2 border-t border-gray-100">
              <div class="flex items-center justify-between">
                <label class="block text-xs font-semibold text-gray-700 tracking-wide">
                  Secret API Token (গোপন নিরাপত্তা চাবি)
                </label>
                <button
                  type="button"
                  @click="showTokenModal = true"
                  class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  নতুন টোকেন তৈরি করুন
                </button>
              </div>

              <div class="relative flex items-center">
                <input
                  :type="showToken ? 'text' : 'password'"
                  readonly
                  :value="settings.subeditor_api_secret"
                  class="w-full text-xs sm:text-sm bg-indigo-50/50 border border-indigo-200 rounded-xl pl-3 pr-36 py-2.5 text-indigo-900 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                />

                <div class="absolute right-1.5 flex items-center gap-1">
                  <!-- Show/Hide Button -->
                  <button
                    type="button"
                    @click="showToken = !showToken"
                    class="p-1.5 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-white/80 transition-colors"
                    :title="showToken ? 'লুকান' : 'দেখুন'"
                  >
                    <svg v-if="showToken" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>

                  <!-- Copy Token Button -->
                  <button
                    type="button"
                    @click="copyToClipboard(settings.subeditor_api_secret, 'token')"
                    class="px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-all flex items-center gap-1 shadow-sm"
                    :class="copiedField === 'token' ? 'bg-emerald-600 text-white' : 'bg-white hover:bg-gray-100 text-gray-700 border border-gray-200'"
                  >
                    <span v-if="copiedField === 'token'">✓ কপি হয়েছে</span>
                    <span v-else>কপি টোকেন</span>
                  </button>
                </div>
              </div>

              <div class="p-3 bg-amber-50/70 border border-amber-200/80 rounded-xl text-amber-900 text-xs flex items-start gap-2">
                <span class="text-amber-600 font-bold shrink-0 mt-0.5">⚠️</span>
                <span>
                  টোকেনটি পাসওয়ার্ডের মতোই সংবেদনশীল। Subeditor24 থেকে সংবাদ রিসিভ করার সময় রিকোয়েস্ট হেডারে <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">X-Api-Secret</code> অথবা <code class="bg-amber-100 px-1 py-0.5 rounded font-mono">Authorization: Bearer [token]</code> হিসেবে ভেরিফাই করা হয়।
                </span>
              </div>
            </div>
          </div>

          <!-- Configuration Card -->
          <form @submit.prevent="saveSettings" class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-gray-100 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-gray-100">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                  <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div>
                  <h2 class="text-lg font-bold text-gray-900">অটোমেশন ও পাবলিশিং কনফিগারেশন</h2>
                  <p class="text-xs text-gray-500">ইনকামিং সংবাদ হ্যান্ডেল ও ডিফল্ট আচরণ নির্ধারণ করুন</p>
                </div>
              </div>
            </div>

            <!-- Master Toggle Switch -->
            <div class="flex items-center justify-between p-4 bg-gray-50/80 rounded-2xl border border-gray-200/70">
              <div class="space-y-0.5">
                <span class="text-sm font-bold text-gray-900 flex items-center gap-2">
                  <span :class="form.subeditor_enabled ? 'text-emerald-600' : 'text-gray-400'">●</span>
                  Subeditor24 ব্রিজ চালু রাখুন
                </span>
                <p class="text-xs text-gray-500">
                  নিষ্ক্রিয় করলে Subeditor24 থেকে পাঠানো কোনো সংবাদ বা রিকোয়েস্ট পোর্টাল গ্রহণ করবে না।
                </p>
              </div>

              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="form.subeditor_enabled"
                  class="sr-only peer"
                />
                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
              </label>
            </div>

            <!-- Publishing Mode Toggle Cards -->
            <div class="space-y-2">
              <label class="block text-xs font-semibold text-gray-700 tracking-wide">
                সংবাদ প্রকাশ পদ্ধতি (Publishing Strategy)
              </label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Option 1: Auto-Publish Direct -->
                <div
                  @click="form.subeditor_auto_publish = true"
                  class="cursor-pointer p-4 rounded-2xl border transition-all text-left relative"
                  :class="form.subeditor_auto_publish ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20' : 'border-gray-200 bg-white hover:border-gray-300'"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                      <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                      সরাসরি লাইভ প্রকাশ
                    </span>
                    <input
                      type="radio"
                      :checked="form.subeditor_auto_publish === true"
                      class="text-emerald-600 focus:ring-emerald-500"
                    />
                  </div>
                  <p class="text-xs text-gray-600 leading-relaxed">
                    সংবাদ গ্রহণের সাথে সাথে ওয়েবসাইটে তাৎক্ষণিক লাইভ পাবলিশ হবে। ব্রেকিং সংবাদের জন্য দ্রুততম উপায়।
                  </p>
                </div>

                <!-- Option 2: Save as Draft -->
                <div
                  @click="form.subeditor_auto_publish = false"
                  class="cursor-pointer p-4 rounded-2xl border transition-all text-left relative"
                  :class="!form.subeditor_auto_publish ? 'border-amber-500 bg-amber-50/40 ring-2 ring-amber-500/20' : 'border-gray-200 bg-white hover:border-gray-300'"
                >
                  <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-800 flex items-center gap-1.5">
                      <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                      খসড়া সংরক্ষণ (ড্রাফট)
                    </span>
                    <input
                      type="radio"
                      :checked="form.subeditor_auto_publish === false"
                      class="text-amber-600 focus:ring-amber-500"
                    />
                  </div>
                  <p class="text-xs text-gray-600 leading-relaxed">
                    সংবাদটি ড্রাফট মোডে জমা থাকবে। এডিটর বা বার্তা বিভাগের ম্যানুয়াল রিভিউর পর প্রকাশ করা যাবে।
                  </p>
                </div>
              </div>
            </div>

            <!-- Default Category & Default Author Dropdowns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Default Category -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-700 tracking-wide">
                  ডিফল্ট ক্যাটাগরি (Fallback Category)
                </label>
                <select
                  v-model="form.subeditor_default_category_id"
                  class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                >
                  <option
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="cat.id"
                  >
                    {{ cat.name }}
                  </option>
                </select>
                <p class="text-[11px] text-gray-400">
                  ইনকামিং সংবাদের ক্যাটাগরি না মিললে এতে যুক্ত হবে
                </p>
              </div>

              <!-- Default Author -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-gray-700 tracking-wide">
                  ডিফল্ট লেখক / রিপোর্টার (Default Author)
                </label>
                <select
                  v-model="form.subeditor_default_author_id"
                  class="w-full text-sm bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                >
                  <option
                    v-for="author in authors"
                    :key="author.id"
                    :value="author.id"
                  >
                    {{ author.name }} ({{ author.email }})
                  </option>
                </select>
                <p class="text-[11px] text-gray-400">
                  সংবাদের লেখক হিসেবে এই ব্যবহারকারী সংরক্ষিত হবেন
                </p>
              </div>
            </div>

            <!-- Save Button -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end">
              <button
                type="submit"
                :disabled="form.processing"
                class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-red-600/25 transition-all flex items-center gap-2 disabled:opacity-50"
              >
                <svg v-if="form.processing" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>{{ form.processing ? 'সংরক্ষণ হচ্ছে...' : 'সেটিংস সংরক্ষণ করুন' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Right Column: Step-by-Step Guide (5 cols) -->
        <div class="lg:col-span-5 space-y-6">
          <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-6 sm:p-7 shadow-sm border border-indigo-900/50 space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-white/10">
              <div class="w-10 h-10 rounded-2xl bg-indigo-600/30 border border-indigo-400/30 flex items-center justify-center font-bold text-indigo-300">
                📖
              </div>
              <div>
                <h3 class="text-base font-bold text-white">Subeditor24 সেটআপ নির্দেশিকা</h3>
                <p class="text-xs text-indigo-200/70">মাত্র ৪টি সহজ ধাপে আপনার নিউজ ডেস্ক কানেক্ট করুন</p>
              </div>
            </div>

            <!-- Steps List -->
            <div class="space-y-4">
              <!-- Step 1 -->
              <div class="flex items-start gap-3.5">
                <span class="w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-sm">
                  ১
                </span>
                <div class="space-y-1">
                  <h4 class="text-sm font-semibold text-white">টোকেন ও URL কপি করুন</h4>
                  <p class="text-xs text-slate-300 leading-relaxed">
                    বামপাশের কার্ড থেকে <strong>Webhook URL</strong> এবং <strong>Secret API Token</strong> কপি করে নিরাপদ স্থানে রাখুন।
                  </p>
                </div>
              </div>

              <!-- Step 2 -->
              <div class="flex items-start gap-3.5">
                <span class="w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-sm">
                  ২
                </span>
                <div class="space-y-1">
                  <h4 class="text-sm font-semibold text-white">Subeditor24 সেটিংসে যান</h4>
                  <p class="text-xs text-slate-300 leading-relaxed">
                    আপনার ডেস্কটপ বা অনলাইন Subeditor24 ড্যাশবোর্ডের <strong>Settings > CMS Webhook</strong> মেন্যুতে প্রবেশ করুন।
                  </p>
                </div>
              </div>

              <!-- Step 3 -->
              <div class="flex items-start gap-3.5">
                <span class="w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-sm">
                  ৩
                </span>
                <div class="space-y-1">
                  <h4 class="text-sm font-semibold text-white">কানেকশন টেস্ট ও ক্যাটাগরি সিঙ্ক</h4>
                  <p class="text-xs text-slate-300 leading-relaxed">
                    Webhook URL ও Token পেস্ট করে <strong>Test Connection</strong> বাটনে চাপ দিন এবং <strong>Sync Categories</strong> বাটনে ক্লিক করে পোর্টালের ক্যাটাগরিগুলো লোড করুন।
                  </p>
                </div>
              </div>

              <!-- Step 4 -->
              <div class="flex items-start gap-3.5">
                <span class="w-7 h-7 rounded-full bg-red-600/90 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5 shadow-sm">
                  ৪
                </span>
                <div class="space-y-1">
                  <h4 class="text-sm font-semibold text-white">সংবাদ পাবলিশ করুন</h4>
                  <p class="text-xs text-slate-300 leading-relaxed">
                    সংবাদ রচনা ও সম্পাদনা শেষে <strong>Publish to Website</strong> চাপলেই মুহূর্তের মধ্যে ওয়েবসাইটে সংবাদ প্রকাশিত হবে!
                  </p>
                </div>
              </div>
            </div>

            <!-- Pro Tip Card -->
            <div class="p-4 bg-white/5 border border-white/10 rounded-2xl text-xs space-y-1.5 text-slate-300">
              <span class="font-bold text-amber-300 flex items-center gap-1">
                💡 স্মার্ট ফিচার
              </span>
              <p class="leading-relaxed">
                Subeditor24 থেকে প্রেরিত ফিচারের ছবি, ব্রেকিং ফ্ল্যাগ ও মেটা ডেসক্রিপশন স্বয়ংক্রিয়ভাবে ফরম্যাট হয়ে ওয়েবসাইটের নির্দিষ্ট ব্লকে চলে যায়।
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Activity Log Section (Full Width) -->
      <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-gray-100 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h2 class="text-lg font-bold text-gray-900">ইনকামিং ওয়েবহুক অ্যাক্টিভিটি লগ</h2>
                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 font-semibold text-xs rounded-full">
                  {{ logs.total ?? logs.data?.length ?? 0 }} টি রেকর্ড
                </span>
              </div>
              <p class="text-xs text-gray-500">Subeditor24 থেকে পোর্টাল কর্তৃক সম্প্রতি প্রাপ্ত সংবাদ ও রিকোয়েস্ট লগ</p>
            </div>
          </div>

          <!-- Actions: Clear Logs -->
          <div class="flex items-center gap-2">
            <button
              v-if="logs.data?.length > 0"
              type="button"
              @click="showClearModal = true"
              class="px-3.5 py-2 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-xl transition-colors flex items-center gap-1.5"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
              সমস্ত লগ মুছুন
            </button>
            <button
              type="button"
              @click="reloadLogs"
              class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors"
              title="রিফ্রেশ করুন"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Table or Empty State -->
        <div v-if="logs.data && logs.data.length > 0" class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead>
              <tr class="border-b border-gray-100 text-gray-400 font-medium">
                <th class="py-3 px-3">সময় ও তারিখ</th>
                <th class="py-3 px-3">সংবাদের শিরোনাম</th>
                <th class="py-3 px-3">ক্যাটাগরি</th>
                <th class="py-3 px-3 text-center">স্ট্যাটাস</th>
                <th class="py-3 px-3 text-right">লাইভ পোস্ট / অ্যাকশন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="log in logs.data"
                :key="log.id"
                class="hover:bg-gray-50/70 transition-colors"
              >
                <!-- Timestamp -->
                <td class="py-3.5 px-3 text-gray-600 whitespace-nowrap">
                  <div class="font-medium text-gray-800">{{ formatDate(log.created_at) }}</div>
                  <div class="text-[11px] text-gray-400">{{ formatTimeAgo(log.created_at) }}</div>
                </td>

                <!-- Title -->
                <td class="py-3.5 px-3 max-w-md">
                  <div class="font-semibold text-gray-900 line-clamp-2 leading-snug">
                    {{ log.article_title || log.title || 'শিরোনামহীন সংবাদ' }}
                  </div>
                  <div v-if="log.error_message" class="text-[11px] text-red-600 mt-1 line-clamp-1">
                    ত্রুটি: {{ log.error_message }}
                  </div>
                </td>

                <!-- Category -->
                <td class="py-3.5 px-3 whitespace-nowrap">
                  <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-medium text-xs rounded-lg">
                    {{ log.category_name || log.category || 'অনির্দিষ্ট' }}
                  </span>
                </td>

                <!-- Status Badge -->
                <td class="py-3.5 px-3 whitespace-nowrap text-center">
                  <span
                    v-if="log.status === 'published' || log.status === 'live'"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    লাইভ প্রকাশিত
                  </span>
                  <span
                    v-else-if="log.status === 'draft'"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                    ড্রাফট সংরক্ষিত
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800"
                  >
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                    ব্যর্থ (Failed)
                  </span>
                </td>

                <!-- Actions / Link -->
                <td class="py-3.5 px-3 whitespace-nowrap text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- If post exists or log has url, link to live preview/view -->
                    <a
                      v-if="log.url || log.post?.slug"
                      :href="log.url || (`/news/${log.post.slug}`)"
                      target="_blank"
                      class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition-colors"
                      title="ওয়েবসাইটে দেখুন"
                    >
                      <span>ভিজিট</span>
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </a>

                    <!-- Link to CMS edit page if post exists -->
                    <Link
                      v-if="log.post_id"
                      :href="route('dashboard.posts.edit', log.post_id)"
                      class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold inline-flex items-center gap-1 transition-colors"
                      title="CMS-এ সম্পাদনা করুন"
                    >
                      <span>এডিট</span>
                      <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </Link>

                    <!-- Error info view if failed -->
                    <button
                      v-if="log.error_message"
                      type="button"
                      @click="selectedErrorLog = log"
                      class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-semibold transition-colors"
                    >
                      কারণ দেখুন
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <div v-if="logs.links && logs.links.length > 3" class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-500">
              মোট {{ logs.total }} টি রেকর্ডের মধ্যে {{ logs.from }}-{{ logs.to }} দেখাচ্ছে
            </p>
            <div class="flex gap-1">
              <Link
                v-for="link in logs.links"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="[
                  'px-3 py-1.5 text-xs rounded-lg transition-colors',
                  link.active ? 'bg-red-600 text-white font-semibold' : 'text-gray-600 hover:bg-gray-100',
                  !link.url ? 'opacity-30 pointer-events-none' : ''
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-12 text-center space-y-3">
          <div class="w-16 h-16 rounded-3xl bg-indigo-50 text-indigo-500 mx-auto flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <h3 class="text-base font-bold text-gray-800">এখনো কোনো ইনকামিং সংবাদের রেকর্ড নেই</h3>
          <p class="text-xs text-gray-500 max-w-md mx-auto leading-relaxed">
            Subeditor24 সফটওয়্যার থেকে কোনো সংবাদ পোস্ট করা হলে তার বিশদ রেকর্ড, স্ট্যাটাস ও লাইভ পোস্ট লিংক এখানে স্বয়ংক্রিয়ভাবে প্রদর্শিত হবে।
          </p>
        </div>
      </div>
    </div>

    <!-- Confirm Modal for Token Regeneration -->
    <ConfirmModal
      v-if="showTokenModal"
      title="নতুন API সিক্রেট টোকেন তৈরি করবেন?"
      message="নতুন টোকেন তৈরি করা হলে পূর্বের টোকেনটি নিষ্ক্রিয় হয়ে যাবে। আপনাকে অবশ্যই Subeditor24 অ্যাপে গিয়ে নতুন টোকেনটি আপডেট করতে হবে, অন্যথায় নতুন সংবাদ সিন্ডিকেশন ব্যর্থ হতে পারে।"
      confirm-label="হ্যাঁ, নতুন টোকেন তৈরি করুন"
      @confirm="generateNewToken"
      @cancel="showTokenModal = false"
    />

    <!-- Confirm Modal for Clearing Logs -->
    <ConfirmModal
      v-if="showClearModal"
      title="সমস্ত ইনকামিং অ্যাক্টিভিটি লগ মুছবেন?"
      message="এই অপারেশনের ফলে Subeditor24 থেকে প্রাপ্ত পূর্ববর্তী সমস্ত লগ স্থায়ীভাবে মুছে যাবে। এটি পুনরুদ্ধার করা সম্ভব নয়।"
      confirm-label="হ্যাঁ, সম্পূর্ণ মুছুন"
      @confirm="clearAllLogs"
      @cancel="showClearModal = false"
    />

    <!-- Error Detail Modal -->
    <div
      v-if="selectedErrorLog"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
    >
      <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 space-y-4 animate-scale-in">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
          <h3 class="text-base font-bold text-red-600 flex items-center gap-2">
            <span>⚠️</span>
            সংবাদ গ্রহণ ব্যর্থতার বিবরণ
          </h3>
          <button
            @click="selectedErrorLog = null"
            class="text-gray-400 hover:text-gray-600 p-1 rounded-lg"
          >
            ✕
          </button>
        </div>

        <div class="space-y-3 text-xs">
          <div>
            <span class="font-semibold text-gray-700">সংবাদ শিরোনাম:</span>
            <p class="text-gray-900 font-medium mt-0.5">{{ selectedErrorLog.article_title || selectedErrorLog.title || 'N/A' }}</p>
          </div>

          <div>
            <span class="font-semibold text-gray-700">ত্রুটি বার্তা:</span>
            <div class="p-3 bg-red-50 text-red-800 rounded-xl font-mono text-[11px] mt-1 break-words">
              {{ selectedErrorLog.error_message || 'অজানা কোনো ত্রুটি ঘটেছে' }}
            </div>
          </div>

          <div v-if="selectedErrorLog.payload">
            <span class="font-semibold text-gray-700">প্রেরিত রিকোয়েস্ট পেলোড:</span>
            <pre class="p-3 bg-gray-900 text-gray-100 rounded-xl text-[10px] max-h-40 overflow-y-auto mt-1 font-mono">{{ JSON.stringify(selectedErrorLog.payload, null, 2) }}</pre>
          </div>
        </div>

        <div class="pt-3 border-t border-gray-100 flex justify-end">
          <button
            type="button"
            @click="selectedErrorLog = null"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-xs transition-colors"
          >
            বন্ধ করুন
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ConfirmModal from '@/Components/UI/ConfirmModal.vue'

const props = defineProps({
  settings: {
    type: Object,
    required: true,
  },
  authors: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
  logs: {
    type: Object,
    default: () => ({ data: [], total: 0 }),
  },
  endpoints: {
    type: Object,
    required: true,
  },
})

// Form State
const form = useForm({
  subeditor_api_secret: props.settings.subeditor_api_secret ?? '',
  subeditor_auto_publish: props.settings.subeditor_auto_publish ?? true,
  subeditor_default_author_id: props.settings.subeditor_default_author_id ?? 1,
  subeditor_default_category_id: props.settings.subeditor_default_category_id ?? 1,
  subeditor_enabled: props.settings.subeditor_enabled ?? true,
})

// Keep form in sync when backend updates settings (e.g. token generation)
watch(
  () => props.settings,
  (newSettings) => {
    if (newSettings?.subeditor_api_secret) {
      form.subeditor_api_secret = newSettings.subeditor_api_secret
    }
    if (newSettings?.subeditor_auto_publish !== undefined) {
      form.subeditor_auto_publish = newSettings.subeditor_auto_publish
    }
    if (newSettings?.subeditor_default_author_id !== undefined) {
      form.subeditor_default_author_id = newSettings.subeditor_default_author_id
    }
    if (newSettings?.subeditor_default_category_id !== undefined) {
      form.subeditor_default_category_id = newSettings.subeditor_default_category_id
    }
    if (newSettings?.subeditor_enabled !== undefined) {
      form.subeditor_enabled = newSettings.subeditor_enabled
    }
  },
  { deep: true }
)

// UI State
const showToken = ref(false)
const copiedField = ref('')
const showTokenModal = ref(false)
const showClearModal = ref(false)
const selectedErrorLog = ref(null)

// Copy to Clipboard
const copyToClipboard = async (text, fieldName) => {
  if (!text) return
  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)
    } else {
      const textarea = document.createElement('textarea')
      textarea.value = text
      textarea.style.position = 'fixed'
      document.body.appendChild(textarea)
      textarea.select()
      document.execCommand('copy')
      document.body.removeChild(textarea)
    }
    copiedField.value = fieldName
    setTimeout(() => {
      if (copiedField.value === fieldName) {
        copiedField.value = ''
      }
    }, 2000)
  } catch (err) {
    console.error('Failed to copy to clipboard', err)
  }
}

// Save Settings
const saveSettings = () => {
  form.post(route('dashboard.subeditor.settings.update'), {
    preserveScroll: true,
  })
}

// Generate Token
const generateNewToken = () => {
  showTokenModal.value = false
  router.post(route('dashboard.subeditor.token.generate'), {}, {
    preserveScroll: true,
  })
}

// Clear All Logs
const clearAllLogs = () => {
  showClearModal.value = false
  router.delete(route('dashboard.subeditor.logs.clear'), {
    preserveScroll: true,
  })
}

// Reload Logs
const reloadLogs = () => {
  router.reload({ only: ['logs'] })
}

// Date Formatter
const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('bn-BD', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateStr
  }
}

// Time Ago Formatter
const formatTimeAgo = (dateStr) => {
  if (!dateStr) return ''
  try {
    const diffMs = Date.now() - new Date(dateStr).getTime()
    const diffSec = Math.floor(diffMs / 1000)
    const diffMin = Math.floor(diffSec / 60)
    const diffHours = Math.floor(diffMin / 60)
    const diffDays = Math.floor(diffHours / 24)

    if (diffDays > 0) return `${diffDays} দিন আগে`
    if (diffHours > 0) return `${diffHours} ঘণ্টা আগে`
    if (diffMin > 0) return `${diffMin} মিনিট আগে`
    return 'এইমাত্র'
  } catch {
    return ''
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fadeIn {
  animation: fadeIn 0.25s ease-out forwards;
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.animate-scale-in {
  animation: scaleIn 0.2s ease-out forwards;
}
</style>
