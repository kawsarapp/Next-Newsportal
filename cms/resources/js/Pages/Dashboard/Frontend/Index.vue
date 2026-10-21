<template>
  <AppLayout title="হোমপেজ ও ফ্রন্টএন্ড লেআউট বিল্ডার">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="text-xs bg-red-100 text-red-700 px-2.5 py-0.5 rounded-full font-bold">ভিজ্যুয়াল লেআউট</span>
            <h2 class="text-2xl font-bold text-gray-800">হোমপেজ ও ফ্রন্টএন্ড লেআউট বিল্ডার</h2>
          </div>
          <p class="text-sm text-gray-500 mt-1">
            হোমপেজের প্রতিটি ব্লক, সাইডবার এবং হেডার-ফুটারের প্রতিটি উপাদান সম্পূর্ণ ডায়নামিকালি কাস্টমাইজ করুন
          </p>
        </div>

        <div v-if="activeTab === 'homepage'" class="flex items-center gap-3">
          <button
            @click="openModal()"
            class="bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
          >
            <span>+</span> নতুন সেকশন যোগ করুন
          </button>
          <button
            @click="saveOrder"
            :disabled="saving"
            class="bg-gray-900 hover:bg-black text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
          >
            <span>💾</span> {{ saving ? 'সংরক্ষণ হচ্ছে...' : 'ক্রম সংরক্ষণ করুন' }}
          </button>
        </div>

        <div v-else-if="activeTab === 'sidebar'" class="flex items-center gap-3">
          <button
            @click="saveSidebarSettings"
            :disabled="savingSidebar"
            class="bg-red-600 hover:bg-red-700 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
          >
            <span>💾</span> {{ savingSidebar ? 'সংরক্ষণ হচ্ছে...' : 'সাইডবার সেটিংস সংরক্ষণ করুন' }}
          </button>
        </div>

        <div v-else-if="activeTab === 'header-footer'" class="flex items-center gap-3">
          <button
            @click="saveHeaderFooterSettings"
            :disabled="savingHeaderFooter"
            class="bg-red-600 hover:bg-red-700 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
          >
            <span>💾</span> {{ savingHeaderFooter ? 'সংরক্ষণ হচ্ছে...' : 'হেডার ও ফুটার সংরক্ষণ করুন' }}
          </button>
        </div>

        <div v-else-if="activeTab === 'youtube'" class="flex items-center gap-3">
          <button
            @click="saveYouTubeSettings"
            :disabled="savingYouTube"
            class="bg-red-600 hover:bg-red-700 text-white font-medium px-5 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
          >
            <span>💾</span> {{ savingYouTube ? 'সংরক্ষণ হচ্ছে...' : 'YouTube লাইভ ও ভিডিও সংরক্ষণ করুন' }}
          </button>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="flex border-b border-gray-200 gap-4 overflow-x-auto">
        <button
          @click="activeTab = 'homepage'"
          :class="[
            'pb-3 text-sm font-bold flex items-center gap-2 transition-all border-b-2 whitespace-nowrap',
            activeTab === 'homepage'
              ? 'border-red-600 text-red-600'
              : 'border-transparent text-gray-500 hover:text-gray-800'
          ]"
        >
          <span>🏠</span> হোমপেজ সেকশন বিল্ডার
          <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-mono">
            {{ localSections.length }}
          </span>
        </button>

        <button
          @click="activeTab = 'sidebar'"
          :class="[
            'pb-3 text-sm font-bold flex items-center gap-2 transition-all border-b-2 whitespace-nowrap',
            activeTab === 'sidebar'
              ? 'border-red-600 text-red-600'
              : 'border-transparent text-gray-500 hover:text-gray-800'
          ]"
        >
          <span>📑</span> সিঙ্গেল নিউজ সাইডবার
        </button>

        <button
          @click="activeTab = 'header-footer'"
          :class="[
            'pb-3 text-sm font-bold flex items-center gap-2 transition-all border-b-2 whitespace-nowrap',
            activeTab === 'header-footer'
              ? 'border-red-600 text-red-600'
              : 'border-transparent text-gray-500 hover:text-gray-800'
          ]"
        >
          <span>🌐</span> হেডার ও ফুটার
        </button>

        <button
          @click="activeTab = 'youtube'"
          :class="[
            'pb-3 text-sm font-bold flex items-center gap-2 transition-all border-b-2 whitespace-nowrap',
            activeTab === 'youtube'
              ? 'border-red-600 text-red-600'
              : 'border-transparent text-gray-500 hover:text-gray-800'
          ]"
        >
          <span>📺</span> YouTube লাইভ ও ভিডিও
          <span class="text-[10px] bg-red-600 text-white px-2 py-0.5 rounded-full font-bold">
            LIVE
          </span>
        </button>
      </div>

      <!-- TAB 1: HOMEPAGE SECTIONS BUILDER -->
      <div v-if="activeTab === 'homepage'" class="space-y-6">
        <!-- Quick Guidance Banner -->
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between text-xs text-amber-900">
          <div class="flex items-center gap-2">
            <span class="text-base">💡</span>
            <span>
              যেকোনো সেকশন উপরে-নিচে সরাতে <strong>▲ / ▼</strong> বাটনে ক্লিক করুন। কোনো সেকশন সাময়িক লুকাতে <strong>সক্রিয়/লুকানো</strong> বাটনে ক্লিক করুন।
            </span>
          </div>
          <span class="font-mono text-[11px] bg-amber-100 px-2 py-0.5 rounded">মোট: {{ localSections.length }} টি সেকশন</span>
        </div>

        <!-- Reordering Canvas -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4 max-w-4xl">
          <h3 class="font-bold text-gray-800 text-sm border-b pb-3 flex items-center justify-between">
            <span>হোমপেজ সেকশন তালিকা (ডিসপ্লে ক্রম অনুসারে)</span>
            <span class="text-xs text-gray-400 font-normal">পরিবর্তনের পর 'ক্রম সংরক্ষণ করুন' এ ক্লিক করুন</span>
          </h3>

          <div v-if="localSections.length === 0" class="py-12 text-center text-gray-400 text-sm">
            বর্তমানে কোনো সেকশন নেই। উপরের বাটন দিয়ে নতুন সেকশন যুক্ত করুন।
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="(sec, idx) in localSections"
              :key="sec.id"
              :class="[
                'flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl border transition-all gap-4',
                sec.is_active ? 'bg-white border-gray-200 hover:border-gray-300 shadow-2xs' : 'bg-gray-50/70 border-gray-200 opacity-60'
              ]"
            >
              <!-- Left Info -->
              <div class="flex items-center gap-3.5 min-w-0">
                <span class="w-6 h-6 rounded-lg bg-gray-100 text-gray-600 font-mono text-xs font-bold flex items-center justify-center shrink-0">
                  {{ idx + 1 }}
                </span>

                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg shrink-0" :class="getBadgeBg(sec.section_type)">
                  {{ getIcon(sec.section_type) }}
                </div>

                <div class="min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-bold text-gray-900 text-sm truncate">{{ sec.title }}</h4>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full capitalize" :class="getBadgeBg(sec.section_type)">
                      {{ sec.section_type }}
                    </span>
                    <span v-if="sec.category" class="text-[10px] bg-red-50 text-red-700 px-2 py-0.5 rounded-full font-semibold">
                      📂 {{ sec.category.name }}
                    </span>
                  </div>
                  <p class="text-xs text-gray-400 mt-0.5">
                    লেআউট: <span class="capitalize text-gray-600 font-medium">{{ sec.layout_type }}</span> · সংবাদ সংখ্যা: <span class="text-gray-600 font-semibold">{{ sec.posts_count }} টি</span>
                  </p>
                </div>
              </div>

              <!-- Right Controls -->
              <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <div class="flex items-center border rounded-lg bg-gray-50 overflow-hidden">
                  <button
                    type="button"
                    @click="moveUp(idx)"
                    :disabled="idx === 0"
                    class="px-2 py-1 hover:bg-white text-gray-600 disabled:opacity-20 text-xs border-r"
                    title="উপরে নিন"
                  >
                    ▲
                  </button>
                  <button
                    type="button"
                    @click="moveDown(idx)"
                    :disabled="idx === localSections.length - 1"
                    class="px-2 py-1 hover:bg-white text-gray-600 disabled:opacity-20 text-xs"
                    title="নিচে নিন"
                  >
                    ▼
                  </button>
                </div>

                <button
                  type="button"
                  @click="toggleActive(sec)"
                  class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors"
                  :class="sec.is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                >
                  {{ sec.is_active ? '👁️ সক্রিয়' : '🙈 লুকানো' }}
                </button>

                <button
                  type="button"
                  @click="openModal(sec)"
                  class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 transition-colors"
                  title="সম্পাদনা করুন"
                >
                  ✏️
                </button>

                <button
                  type="button"
                  @click="deleteSection(sec)"
                  class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                  title="মুছে ফেলুন"
                >
                  🗑️
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 2: SINGLE NEWS SIDEBAR CUSTOMIZER -->
      <div v-else-if="activeTab === 'sidebar'" class="space-y-6 max-w-4xl">
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between text-xs text-blue-900">
          <div class="flex items-center gap-2">
            <span class="text-base">📌</span>
            <span>
              সিঙ্গেল নিউজ পেজের ডানপাশের সাইডবারের প্রতিটা উইজেট (Tabs, Related, Ad Slot, Newsletter) এখান থেকে সম্পূর্ণ কাস্টমাইজ বা অন/অফ করতে পারবেন।
            </span>
          </div>
        </div>

        <form @submit.prevent="saveSidebarSettings" class="space-y-6">
          <!-- Widget 1: Latest vs Popular Tabs -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
              <div class="flex items-center gap-2">
                <span class="text-xl">🔥</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">১. সর্বশেষ ও সর্বাধিক পঠিত সংবাদ (Tabbed Widget)</h4>
                  <p class="text-xs text-gray-400">পাঠকদের জন্য ১ ক্লিকে সর্বশেষ ও সর্বাধিক পঠিত খবরের তালিকা</p>
                </div>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="sidebarForm.show_tabs" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
              </label>
            </div>

            <div v-if="sidebarForm.show_tabs" class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ট্যাব ১ এর শিরোনাম</label>
                <input
                  v-model="sidebarForm.tabs_title_1"
                  type="text"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ট্যাব ২ এর শিরোনাম</label>
                <input
                  v-model="sidebarForm.tabs_title_2"
                  type="text"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">সংবাদের সংখ্যা</label>
                <input
                  v-model.number="sidebarForm.tabs_count"
                  type="number"
                  min="1"
                  max="15"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
            </div>
          </div>

          <!-- Widget 2: Related News -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
              <div class="flex items-center gap-2">
                <span class="text-xl">📌</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">২. একই বিভাগের অন্যান্য খবর (Related News)</h4>
                  <p class="text-xs text-gray-400">বর্তমান সংবাদের ক্যাটাগরি অনুযায়ী সম্পর্কিত খবর প্রদর্শন</p>
                </div>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="sidebarForm.show_related" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
              </label>
            </div>

            <div v-if="sidebarForm.show_related" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">শিরোনাম ফরম্যাট</label>
                <input
                  v-model="sidebarForm.related_title"
                  type="text"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">সর্বোচ্চ সংবাদের সংখ্যা</label>
                <input
                  v-model.number="sidebarForm.related_count"
                  type="number"
                  min="1"
                  max="10"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
            </div>
          </div>

          <!-- Widget 3: Sidebar Ad Slot -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
              <div class="flex items-center gap-2">
                <span class="text-xl">📢</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">৩. সাইডবার বিজ্ঞাপন ব্যানার স্লট (Sidebar Ad 300x250)</h4>
                  <p class="text-xs text-gray-400">সাইডবারে কাস্টম ব্যানার, ছবি বা প্রচারমূলক টেক্সট</p>
                </div>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="sidebarForm.show_ad" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
              </label>
            </div>

            <div v-if="sidebarForm.show_ad" class="space-y-3 pt-2 text-sm">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">বিজ্ঞাপন লেবেল</label>
                  <input
                    v-model="sidebarForm.ad_title"
                    type="text"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">বিজ্ঞাপনের মূল বার্তা</label>
                  <input
                    v-model="sidebarForm.ad_text"
                    type="text"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">যোগাযোগের তথ্য / ইমেইল</label>
                  <input
                    v-model="sidebarForm.ad_contact"
                    type="text"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">ক্লিক করার লিংক (ঐচ্ছিক URL)</label>
                  <input
                    v-model="sidebarForm.ad_link"
                    type="url"
                    placeholder="https://example.com"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Widget 4: Newsletter Box -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
              <div class="flex items-center gap-2">
                <span class="text-xl">✉️</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">৪. ই-নিউজলেটার সাবস্ক্রিপশন বক্স</h4>
                  <p class="text-xs text-gray-400">পাঠকদের ইমেইল সংগ্রহের বক্স</p>
                </div>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="sidebarForm.show_newsletter" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
              </label>
            </div>

            <div v-if="sidebarForm.show_newsletter" class="space-y-3 pt-2 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">নিউজলেটার শিরোনাম</label>
                <input
                  v-model="sidebarForm.newsletter_title"
                  type="text"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">বর্ণনা / বার্তা</label>
                <textarea
                  v-model="sidebarForm.newsletter_desc"
                  rows="2"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="savingSidebar"
              class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
            >
              <span>💾</span> {{ savingSidebar ? 'সংরক্ষণ হচ্ছে...' : 'সাইডবার সেটিংস সংরক্ষণ করুন' }}
            </button>
          </div>
        </form>
      </div>

      <!-- TAB 3: HEADER & FOOTER CUSTOMIZER -->
      <div v-else-if="activeTab === 'header-footer'" class="space-y-6 max-w-4xl">
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-xs text-emerald-900">
          <div class="flex items-center gap-2">
            <span class="text-base">🌐</span>
            <span>
              ওয়েবসাইটের উপরের টপ-বার, স্লোগান, ই-পেপার/লাইভ বাটন এবং নিচের ফুটারের সমস্ত বিবরণ, যোগাযোগের তথ্য ও সোশ্যাল মিডিয়া লিংক এখান থেকে পরিবর্তন করুন।
            </span>
          </div>
        </div>

        <form @submit.prevent="saveHeaderFooterSettings" class="space-y-6">
          <!-- Section 1: Header Top Bar & Branding -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <h4 class="font-bold text-gray-900 text-sm border-b pb-3 flex items-center justify-between">
              <span class="flex items-center gap-2"><span>🔝</span> ১. হেডার টপ-বার ও ইউটিলিটি সেটিংস</span>
              <label class="flex items-center gap-2 cursor-pointer text-xs font-normal text-gray-600">
                <input type="checkbox" v-model="hfForm.show_topbar" class="rounded text-red-600 focus:ring-red-500" />
                টপ-বার সক্রিয় রাখুন
              </label>
            </h4>

            <div v-if="hfForm.show_topbar" class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">অবস্থান / শহর</label>
                <input
                  v-model="hfForm.topbar_location"
                  type="text"
                  placeholder="ঢাকা, বাংলাদেশ"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">নামাজের সময় টেক্সট</label>
                <input
                  v-model="hfForm.prayer_time"
                  type="text"
                  placeholder="🕌 নামাজের সময়সূচি (অটোমেটিক)"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">আবহাওয়া টেক্সট</label>
                <input
                  v-model="hfForm.weather_text"
                  type="text"
                  placeholder="🌦️ ২৮° সে."
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
            </div>

            <div class="pt-2 border-t border-gray-100">
              <label class="block text-xs font-semibold text-gray-600 mb-1">লোগোর নিচের স্লোগান (Tagline)</label>
              <input
                v-model="hfForm.site_tagline"
                type="text"
                placeholder="সত্য ও ন্যায়ের সাথে সার্বক্ষণিক"
                class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
              />
            </div>

            <!-- Header Quick Action Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 text-sm">
              <div class="p-3 bg-gray-50 rounded-xl space-y-2">
                <label class="flex items-center gap-2 cursor-pointer font-semibold text-xs text-gray-700">
                  <input type="checkbox" v-model="hfForm.show_epaper_btn" class="rounded text-red-600 focus:ring-red-500" />
                  📰 'ই-পেপার' বাটন দেখান
                </label>
                <input
                  v-if="hfForm.show_epaper_btn"
                  v-model="hfForm.epaper_url"
                  type="text"
                  placeholder="/epaper"
                  class="w-full border bg-white rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-red-500"
                />
              </div>

              <div class="p-3 bg-gray-50 rounded-xl space-y-2">
                <label class="flex items-center gap-2 cursor-pointer font-semibold text-xs text-gray-700">
                  <input type="checkbox" v-model="hfForm.show_live_btn" class="rounded text-red-600 focus:ring-red-500" />
                  🔴 'লাইভ' বাটন দেখান
                </label>
                <input
                  v-if="hfForm.show_live_btn"
                  v-model="hfForm.live_url"
                  type="text"
                  placeholder="/live"
                  class="w-full border bg-white rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-red-500"
                />
              </div>
            </div>
          </div>

          <!-- Section 2: Footer Bio & Contact -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <h4 class="font-bold text-gray-900 text-sm border-b pb-3 flex items-center gap-2">
              <span>🔻</span> ২. ফুটার পরিচিতি ও যোগাযোগের তথ্য
            </h4>

            <div class="space-y-3 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">ফুটার বায়ো / সংক্ষিপ্ত পরিচিতি</label>
                <textarea
                  v-model="hfForm.about_bio"
                  rows="2"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  placeholder="সত্য ও বস্তুনিষ্ঠ সাংবাদিকতার অঙ্গীকার..."
                ></textarea>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">প্রধান কার্যালয়ের ঠিকানা</label>
                  <input
                    v-model="hfForm.office_address"
                    type="text"
                    placeholder="প্রধান কার্যালয়: ঢাকা, বাংলাদেশ"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">যোগাযোগের ইমেইল</label>
                  <input
                    v-model="hfForm.contact_email"
                    type="email"
                    placeholder="newsroom@dainikbangla.com"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-600 mb-1">ফোন নাম্বার</label>
                  <input
                    v-model="hfForm.contact_phone"
                    type="text"
                    placeholder="+৮৮০১৭০০০০০০০০"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Section 3: Social Links & Copyright -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <h4 class="font-bold text-gray-900 text-sm border-b pb-3 flex items-center gap-2">
              <span>🔗</span> ৩. সোশ্যাল মিডিয়া ও কপিরাইট টেক্সট
            </h4>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Facebook URL</label>
                <input
                  v-model="hfForm.facebook_url"
                  type="url"
                  placeholder="https://facebook.com/page"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">X (Twitter) URL</label>
                <input
                  v-model="hfForm.twitter_url"
                  type="url"
                  placeholder="https://x.com/handle"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">YouTube URL</label>
                <input
                  v-model="hfForm.youtube_url"
                  type="url"
                  placeholder="https://youtube.com/channel"
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                />
              </div>
            </div>

            <div class="pt-2 border-t border-gray-100">
              <label class="block text-xs font-semibold text-gray-600 mb-1">কপিরাইট নোটিশ টেক্সট</label>
              <input
                v-model="hfForm.copyright_text"
                type="text"
                placeholder="© ২০২৬ দৈনিক বাংলা নিউজপোর্টাল। সর্বস্বত্ব সংরক্ষিত।"
                class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
              />
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="savingHeaderFooter"
              class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
            >
              <span>💾</span> {{ savingHeaderFooter ? 'সংরক্ষণ হচ্ছে...' : 'হেডার ও ফুটার সংরক্ষণ করুন' }}
            </button>
          </div>
        </form>
      </div>

      <!-- TAB 4: YOUTUBE LIVE & LATEST VIDEOS -->
      <div v-else-if="activeTab === 'youtube'" class="space-y-6 max-w-4xl">
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center justify-between text-xs text-rose-900">
          <div class="flex items-center gap-2">
            <span class="text-base">📺</span>
            <span>
              আপনার YouTube চ্যানেলের লাইভ ব্রডকাস্ট এবং সর্বশেষ ভিডিওগুলো এখানে যুক্ত করুন। এটি হোমপেজে এবং /live পেজে অটোমেটিক প্লেয়ার সহ প্রদর্শিত হবে।
            </span>
          </div>
        </div>

        <form @submit.prevent="saveYouTubeSettings" class="space-y-6">
          <!-- 1. Live Broadcast Toggle & Stream URL -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
              <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-600 animate-ping"></span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">১. YouTube লাইভ সম্প্রচার (Live Streaming)</h4>
                  <p class="text-xs text-gray-400">লাইভ চালু থাকলে হোমপেজে ও লাইভ পেজে সবার আগে বড় স্ক্রিনে শো হবে</p>
                </div>
              </div>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" v-model="ytForm.is_live" class="sr-only peer" />
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
              </label>
            </div>

            <div class="space-y-4 pt-1 text-sm">
              <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                  YouTube লাইভ ভিডিও লিঙ্ক (Video URL / Share Link)
                </label>
                <input
                  v-model="ytForm.live_video_url"
                  type="text"
                  placeholder="https://www.youtube.com/watch?v=... অথবা https://youtu.be/..."
                  class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500 font-mono"
                />
                <p class="text-[11px] text-gray-400 mt-1">
                  যেকোনো ইউটিউব ভিডিও বা লাইভ স্ট্রিমের লিঙ্ক পেস্ট করলেই প্লেয়ার নিজে থেকেই আইডি শনাক্ত করে নিবে।
                </p>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">লাইভের ব্যানার শিরোনাম</label>
                  <input
                    v-model="ytForm.live_title"
                    type="text"
                    placeholder="🔴 সরাসরি সম্প্রচার: বিশেষ সংবাদ ও বিশ্লেষণ"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1">YouTube চ্যানেলের লিঙ্ক</label>
                  <input
                    v-model="ytForm.channel_url"
                    type="url"
                    placeholder="https://youtube.com/@dainikbangla"
                    class="w-full border rounded-xl px-3 py-2 text-xs outline-none focus:border-red-500"
                  />
                </div>
              </div>

              <!-- Display settings -->
              <div class="flex flex-wrap items-center justify-between gap-4 pt-3 border-t border-gray-100 text-xs">
                <div class="flex items-center gap-6">
                  <label class="flex items-center gap-2 cursor-pointer text-gray-700 font-medium">
                    <input type="checkbox" v-model="ytForm.show_on_home" class="rounded text-red-600 focus:ring-red-500" />
                    হোমপেজে ভিডিও গ্যালারি ব্লক দেখান
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer text-gray-700 font-medium">
                    <input type="checkbox" v-model="ytForm.show_on_live_page" class="rounded text-red-600 focus:ring-red-500" />
                    /live পেজে লাইভ প্লেয়ার দেখান
                  </label>
                </div>

                <div v-if="ytForm.show_on_home" class="flex items-center gap-2">
                  <span class="text-gray-600 font-medium">হোমপেজে অবস্থান:</span>
                  <select v-model="ytForm.home_position" class="border border-gray-300 rounded-lg px-2.5 py-1 text-xs bg-white focus:border-red-500 outline-none">
                    <option value="middle">মাঝামাঝি (২য় সেকশনের পর - স্ট্যান্ডার্ড)</option>
                    <option value="top">শীর্ষে (প্রধান সংবাদের পর)</option>
                    <option value="bottom">নিচে (সকল ক্যাটাগরির পর)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. Latest YouTube Videos Grid Manager -->
          <div class="bg-white rounded-2xl border border-gray-200/80 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
              <div class="flex items-center gap-2">
                <span class="text-xl">🎬</span>
                <div>
                  <h4 class="font-bold text-gray-900 text-sm">২. চ্যানেলের সর্বশেষ আলোচিত ভিডিও গ্যালারি</h4>
                  <p class="text-xs text-gray-400">হোমপেজে ভিডিও গ্যালারি সেকশনে প্রদর্শনের জন্য ভিডিও তালিকা</p>
                </div>
              </div>
              <button
                type="button"
                @click="addVideo"
                class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-xl text-xs transition-colors flex items-center gap-1"
              >
                <span>+</span> ভিডিও যোগ করুন
              </button>
            </div>

            <div v-if="ytForm.videos.length === 0" class="py-8 text-center text-xs text-gray-400">
              কোনো ভিডিও যুক্ত করা হয়নি। উপরের বাটন দিয়ে ভিডিও যোগ করুন।
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="(vid, idx) in ytForm.videos"
                :key="idx"
                class="p-4 rounded-xl border border-gray-200 bg-gray-50/50 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-gray-600">ভিডিও #{{ idx + 1 }}</span>
                  <button
                    type="button"
                    @click="removeVideo(idx)"
                    class="text-xs text-red-600 hover:text-red-700 font-semibold"
                  >
                    মুছে ফেলুন ✕
                  </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
                  <div class="sm:col-span-6">
                    <label class="block font-semibold text-gray-700 mb-1">ভিডিও শিরোনাম</label>
                    <input
                      v-model="vid.title"
                      type="text"
                      placeholder="উদা: আজকের বিশেষ সংবাদ..."
                      class="w-full border bg-white rounded-xl px-3 py-2 outline-none focus:border-red-500"
                    />
                  </div>
                  <div class="sm:col-span-4">
                    <label class="block font-semibold text-gray-700 mb-1">YouTube URL</label>
                    <input
                      v-model="vid.video_url"
                      type="text"
                      placeholder="https://www.youtube.com/watch?v=..."
                      class="w-full border bg-white rounded-xl px-3 py-2 outline-none focus:border-red-500 font-mono"
                    />
                  </div>
                  <div class="sm:col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">দৈর্ঘ্য</label>
                    <input
                      v-model="vid.duration"
                      type="text"
                      placeholder="১০:১৫"
                      class="w-full border bg-white rounded-xl px-3 py-2 outline-none focus:border-red-500"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="savingYouTube"
              class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm transition-colors shadow-sm inline-flex items-center gap-2"
            >
              <span>💾</span> {{ savingYouTube ? 'সংরক্ষণ হচ্ছে...' : 'YouTube লাইভ ও ভিডিও সংরক্ষণ করুন' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Add/Edit Section Modal -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-4">
          <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-lg font-bold text-gray-900">
              {{ editItem ? 'হোমপেজ সেকশন সম্পাদনা' : 'নতুন সেকশন যোগ করুন' }}
            </h3>
            <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="submitForm" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">সেকশনের শিরোনাম *</label>
              <input
                v-model="form.title"
                type="text"
                required
                placeholder="উদা: রাজনীতি ও অর্থনীতি বিশেষ"
                class="w-full border rounded-xl px-3 py-2 outline-none focus:border-red-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">সেকশন টাইপ *</label>
                <select v-model="form.section_type" required class="w-full border rounded-xl px-3 py-2 outline-none focus:border-red-500 bg-white">
                  <option value="hero">হিরো / প্রধান লিড (Hero)</option>
                  <option value="grid">ক্যাটাগরি গ্রিড (Grid)</option>
                  <option value="featured">হাইলাইট ব্লক (Featured)</option>
                  <option value="list">লিস্ট / কলাম ভিউ (List)</option>
                  <option value="slider">স্লাইডার / ক্যারোসেল (Slider)</option>
                  <option value="ad">বিজ্ঞাপন স্লট (Banner Ad)</option>
                </select>
              </div>

              <div>
                <label class="block font-medium text-gray-700 mb-1">লেআউট স্টাইল *</label>
                <select v-model="form.layout_type" required class="w-full border rounded-xl px-3 py-2 outline-none focus:border-red-500 bg-white">
                  <option value="grid">৩-কলাম গ্রিড</option>
                  <option value="split">স্প্লিট ২-কলাম</option>
                  <option value="list">এক কলাম লিস্ট</option>
                  <option value="slider">স্লাইডার শো</option>
                  <option value="banner">ফুল-উইডথ ব্যানার</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">সংযুক্ত ক্যাটাগরি (ঐচ্ছিক)</label>
                <select v-model="form.category_id" class="w-full border rounded-xl px-3 py-2 outline-none focus:border-red-500 bg-white">
                  <option :value="null">সকল ক্যাটাগরি / ডিফল্ট</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>

              <div>
                <label class="block font-medium text-gray-700 mb-1">সংবাদের সংখ্যা</label>
                <input
                  v-model.number="form.posts_count"
                  type="number"
                  min="1"
                  max="24"
                  required
                  class="w-full border rounded-xl px-3 py-2 outline-none focus:border-red-500"
                />
              </div>
            </div>

            <div class="pt-2">
              <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                <input v-model="form.is_active" type="checkbox" class="rounded text-red-600 focus:ring-red-500" />
                হোমপেজে সক্রিয় ও দৃশ্যমান রাখুন
              </label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t">
              <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200">
                বাতিল
              </button>
              <button type="submit" class="px-5 py-2 font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700">
                {{ editItem ? 'আপডেট করুন' : 'যোগ করুন' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  sections: {
    type: Array,
    default: () => [],
  },
  categories: {
    type: Array,
    default: () => [],
  },
  sidebarConfig: {
    type: Object,
    default: () => ({}),
  },
  headerFooterConfig: {
    type: Object,
    default: () => ({}),
  },
  youtubeConfig: {
    type: Object,
    default: () => ({}),
  },
})

const activeTab = ref('homepage')

const localSections = ref([...props.sections])
watch(() => props.sections, (newVal) => {
  localSections.value = [...newVal]
})

const saving = ref(false)
const showModal = ref(false)
const editItem = ref(null)

const form = ref({
  title: '',
  section_type: 'grid',
  layout_type: 'grid',
  category_id: null,
  posts_count: 6,
  is_active: true,
})

// Sidebar form
const savingSidebar = ref(false)
const sidebarForm = ref({
  show_tabs: props.sidebarConfig?.show_tabs ?? true,
  tabs_title_1: props.sidebarConfig?.tabs_title_1 ?? 'সর্বশেষ সংবাদ',
  tabs_title_2: props.sidebarConfig?.tabs_title_2 ?? 'সর্বাধিক পঠিত',
  tabs_count: props.sidebarConfig?.tabs_count ?? 5,
  show_related: props.sidebarConfig?.show_related ?? true,
  related_title: props.sidebarConfig?.related_title ?? 'একই বিভাগের আরও খবর',
  related_count: props.sidebarConfig?.related_count ?? 4,
  show_ad: props.sidebarConfig?.show_ad ?? true,
  ad_title: props.sidebarConfig?.ad_title ?? 'বিজ্ঞাপন স্থান (Sidebar Ad)',
  ad_text: props.sidebarConfig?.ad_text ?? 'আপনার ব্র্যান্ড বা পণ্যের বিজ্ঞাপন দিন',
  ad_contact: props.sidebarConfig?.ad_contact ?? 'যোগাযোগ: ads@newsportal.com',
  ad_link: props.sidebarConfig?.ad_link ?? '',
  ad_image: props.sidebarConfig?.ad_image ?? '',
  show_newsletter: props.sidebarConfig?.show_newsletter ?? true,
  newsletter_title: props.sidebarConfig?.newsletter_title ?? 'দৈনিক বাংলা ই-নিউজলেটার',
  newsletter_desc: props.sidebarConfig?.newsletter_desc ?? 'প্রতিদিনের সেরা ও বিশ্লেষণমূলক সংবাদের আপডেট পেতে সাবস্ক্রাইব করুন।',
})

function saveSidebarSettings() {
  savingSidebar.value = true
  router.post(route('dashboard.frontend.sidebar.update'), sidebarForm.value, {
    preserveScroll: true,
    onFinish: () => {
      savingSidebar.value = false
    }
  })
}

// Header & Footer Form
const savingHeaderFooter = ref(false)
const hfForm = ref({
  show_topbar: props.headerFooterConfig?.show_topbar ?? true,
  topbar_location: props.headerFooterConfig?.topbar_location ?? 'ঢাকা, বাংলাদেশ',
  prayer_time: props.headerFooterConfig?.prayer_time ?? '🕌 নামাজের সময়সূচি (অটোমেটিক)',
  weather_text: props.headerFooterConfig?.weather_text ?? '🌦️ ২৮° সে.',
  site_tagline: props.headerFooterConfig?.site_tagline ?? 'সত্য ও ন্যায়ের সাথে সার্বক্ষণিক',
  show_epaper_btn: props.headerFooterConfig?.show_epaper_btn ?? true,
  epaper_url: props.headerFooterConfig?.epaper_url ?? '/epaper',
  show_live_btn: props.headerFooterConfig?.show_live_btn ?? true,
  live_url: props.headerFooterConfig?.live_url ?? '/live',
  about_bio: props.headerFooterConfig?.about_bio ?? 'সত্য ও বস্তুনিষ্ঠ সাংবাদিকতার অঙ্গীকার নিয়ে সর্বাধুনিক হেডলেস প্রযুক্তিতে পরিচালিত ডিজিটাল নিউজ মিডিয়া।',
  office_address: props.headerFooterConfig?.office_address ?? 'প্রধান কার্যালয়: ঢাকা, বাংলাদেশ',
  contact_email: props.headerFooterConfig?.contact_email ?? 'newsroom@dainikbangla.com',
  contact_phone: props.headerFooterConfig?.contact_phone ?? '+৮৮০১৭০০০০০০০০',
  facebook_url: props.headerFooterConfig?.facebook_url ?? 'https://facebook.com',
  twitter_url: props.headerFooterConfig?.twitter_url ?? 'https://twitter.com',
  youtube_url: props.headerFooterConfig?.youtube_url ?? 'https://youtube.com',
  copyright_text: props.headerFooterConfig?.copyright_text ?? '© ২০২৬ দৈনিক বাংলা নিউজপোর্টাল। সর্বস্বত্ব সংরক্ষিত।',
})

function saveHeaderFooterSettings() {
  savingHeaderFooter.value = true
  router.post(route('dashboard.frontend.header-footer.update'), hfForm.value, {
    preserveScroll: true,
    onFinish: () => {
      savingHeaderFooter.value = false
    }
  })
}

// YouTube Form
const savingYouTube = ref(false)
const ytForm = ref({
  is_live: props.youtubeConfig?.is_live ?? false,
  live_video_url: props.youtubeConfig?.live_video_url ?? '',
  live_title: props.youtubeConfig?.live_title ?? '🔴 সরাসরি সম্প্রচার: বিশেষ সংবাদ ও বিশ্লেষণ',
  channel_url: props.youtubeConfig?.channel_url ?? 'https://youtube.com',
  show_on_home: props.youtubeConfig?.show_on_home ?? true,
  home_position: props.youtubeConfig?.home_position ?? 'middle',
  show_on_live_page: props.youtubeConfig?.show_on_live_page ?? true,
  videos: props.youtubeConfig?.videos ? [...props.youtubeConfig.videos] : [],
})

function addVideo() {
  ytForm.value.videos.push({
    title: '',
    video_url: '',
    duration: '',
  })
}

function removeVideo(idx) {
  ytForm.value.videos.splice(idx, 1)
}

function saveYouTubeSettings() {
  savingYouTube.value = true
  router.post(route('dashboard.frontend.youtube.update'), ytForm.value, {
    preserveScroll: true,
    onFinish: () => {
      savingYouTube.value = false
    }
  })
}

function getIcon(type) {
  switch (type) {
    case 'hero': return '⭐'
    case 'grid': return '▦'
    case 'featured': return '🔥'
    case 'list': return '☰'
    case 'slider': return '🎞️'
    case 'ad': return '📢'
    default: return '📰'
  }
}

function getBadgeBg(type) {
  switch (type) {
    case 'hero': return 'bg-purple-50 text-purple-700'
    case 'grid': return 'bg-blue-50 text-blue-700'
    case 'featured': return 'bg-amber-50 text-amber-700'
    case 'list': return 'bg-emerald-50 text-emerald-700'
    case 'slider': return 'bg-indigo-50 text-indigo-700'
    case 'ad': return 'bg-rose-50 text-rose-700'
    default: return 'bg-gray-100 text-gray-600'
  }
}

function moveUp(idx) {
  if (idx > 0) {
    const item = localSections.value.splice(idx, 1)[0]
    localSections.value.splice(idx - 1, 0, item)
  }
}

function moveDown(idx) {
  if (idx < localSections.value.length - 1) {
    const item = localSections.value.splice(idx, 1)[0]
    localSections.value.splice(idx + 1, 0, item)
  }
}

function toggleActive(sec) {
  sec.is_active = !sec.is_active
  router.patch(route('dashboard.frontend.sections.toggle', sec.id), {}, {
    preserveScroll: true,
  })
}

function openModal(item = null) {
  editItem.value = item
  if (item) {
    form.value = {
      title: item.title,
      section_type: item.section_type || 'grid',
      layout_type: item.layout_type || 'grid',
      category_id: item.category_id || null,
      posts_count: item.posts_count || 6,
      is_active: Boolean(item.is_active),
    }
  } else {
    form.value = {
      title: '',
      section_type: 'grid',
      layout_type: 'grid',
      category_id: null,
      posts_count: 6,
      is_active: true,
    }
  }
  showModal.value = true
}

function submitForm() {
  if (editItem.value) {
    router.put(route('dashboard.frontend.sections.update', editItem.value.id), form.value, {
      onSuccess: () => {
        showModal.value = false
      }
    })
  } else {
    router.post(route('dashboard.frontend.sections.store'), form.value, {
      onSuccess: () => {
        showModal.value = false
      }
    })
  }
}

function saveOrder() {
  saving.value = true
  const payload = localSections.value.map((sec, index) => ({
    id: sec.id,
    order: index + 1,
    is_active: sec.is_active,
  }))

  router.post(route('dashboard.frontend.sections.reorder'), { sections: payload }, {
    preserveScroll: true,
    onFinish: () => {
      saving.value = false
    }
  })
}

function deleteSection(sec) {
  if (confirm(`'${sec.title}' সেকশনটি কি মুছে ফেলতে চান?`)) {
    router.delete(route('dashboard.frontend.sections.destroy', sec.id))
  }
}
</script>
