<template>
  <AppLayout>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
      <!-- Breadcrumb & Header -->
      <div class="flex items-center justify-between">
        <div>
          <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
            <Link :href="route('dashboard.posts.index')" class="hover:text-red-600 transition-colors">নিউজ</Link>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ isEdit ? 'সম্পাদনা' : 'নতুন সংবাদ লিখুন' }}</span>
          </div>
          <h2 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'সংবাদ সম্পাদনা করুন' : 'নতুন সংবাদ তৈরি' }}</h2>
        </div>

        <div class="flex items-center gap-3">
          <Link
            :href="route('dashboard.posts.index')"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
          >
            বাতিল
          </Link>
          <button
            type="button"
            @click="submit('draft')"
            :disabled="form.processing"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-sm font-medium transition-colors"
          >
            খসড়া সংরক্ষণ
          </button>
          <button
            type="button"
            @click="submit('published')"
            :disabled="form.processing"
            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-2"
          >
            <span v-if="form.processing" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ isEdit ? 'আপডেট করুন' : 'সংবাদ প্রকাশ' }}
          </button>
        </div>
      </div>

      <!-- Form Grid -->
      <form @submit.prevent="submit('draft')" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left 2 cols) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Title & Slug -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">শিরোনাম <span class="text-red-500">*</span></label>
              <input
                v-model="form.title"
                type="text"
                placeholder="সংবাদের আকর্ষণীয় শিরোনাম লিখুন..."
                class="w-full text-lg px-4 py-2.5 border border-gray-200 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
              />
              <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
            </div>

            <div>
              <label class="block text-xs font-medium text-gray-500 mb-1">স্লাগ (URL)</label>
              <div class="flex items-center text-sm text-gray-500 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                <span class="text-gray-400 select-none">news/</span>
                <input
                  v-model="form.slug"
                  type="text"
                  placeholder="auto-generated-slug"
                  class="bg-transparent border-none p-0 focus:ring-0 text-sm text-gray-700 w-full ml-1"
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">সারসংক্ষেপ (Excerpt)</label>
              <textarea
                v-model="form.excerpt"
                rows="2"
                placeholder="সংবাদের মূল সারসংক্ষেপ এক বা দুই বাক্যে..."
                class="w-full text-sm px-4 py-2 border border-gray-200 rounded-xl focus:border-red-500 focus:ring-1 focus:ring-red-500 outline-none"
              ></textarea>
            </div>
          </div>

          <!-- Body / TipTap Rich Editor -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-2">
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-semibold text-gray-700">সংবাদের মূল বিবরণ <span class="text-red-500">*</span></label>
              <span class="text-xs text-gray-400">Bangla & English Supported</span>
            </div>
            <RichTipTapEditor v-model="form.body" placeholder="এখানে বিস্তারিত সংবাদ তৈরি করুন..." />
            <p v-if="form.errors.body" class="text-red-500 text-xs mt-1">{{ form.errors.body }}</p>
          </div>

          <!-- SEO Meta Details -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
              <span>🔍</span> সার্চ ইঞ্জিন অপ্টিমাইজেশন (SEO)
            </h3>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">SEO Title</label>
              <input
                v-model="form.seo_title"
                type="text"
                placeholder="গুগল সার্চে প্রদর্শিত শিরোনাম (ডিফল্ট মূল শিরোনাম)"
                class="w-full text-sm px-4 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
              <textarea
                v-model="form.seo_description"
                rows="2"
                placeholder="সার্চ রেজাল্টে প্রদর্শিত মেটা ডেসক্রিপশন..."
                class="w-full text-sm px-4 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Sidebar Options (Right 1 col) -->
        <div class="space-y-6">
          <!-- Publishing & Flags -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">সংবাদ বৈশিষ্ট্য ও ধরন</h3>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">ক্যাটাগরি <span class="text-red-500">*</span></label>
              <select
                v-model="form.category_id"
                class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none bg-white"
              >
                <option value="">ক্যাটাগরি নির্বাচন করুন</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
              <p v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">সংবাদের ধরন</label>
              <select
                v-model="form.type"
                class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none bg-white"
              >
                <option value="article">সাধারণ সংবাদ (Article)</option>
                <option value="video">ভিডিও প্রতিবেদন (Video)</option>
                <option value="gallery">ছবি গ্যালারি (Gallery)</option>
                <option value="live">লাইভ আপডেট (Live Blog)</option>
                <option value="opinion">মতামত ও কলাম (Opinion)</option>
                <option value="analysis">বিশ্লেষণধর্মী (Analysis)</option>
              </select>
            </div>

            <div class="pt-3 border-t border-gray-100 space-y-3">
              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_breaking" type="checkbox" class="w-4 h-4 text-red-600 rounded border-gray-300 focus:ring-red-500">
                <div>
                  <span class="text-sm font-medium text-gray-800">ব্রেকিং নিউজ</span>
                  <p class="text-xs text-gray-400">হেডলাইন টিকারে তৎক্ষণাৎ প্রকাশ পাবে</p>
                </div>
              </label>

              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                <div>
                  <span class="text-sm font-medium text-gray-800">প্রধান / ফিচার্ড সংবাদ</span>
                  <p class="text-xs text-gray-400">হোমপেজের মূল লিড নিউজ হিসেবে প্রাধান্য পাবে</p>
                </div>
              </label>

              <label class="flex items-center gap-3 cursor-pointer">
                <input v-model="form.is_exclusive" type="checkbox" class="w-4 h-4 text-purple-600 rounded border-gray-300 focus:ring-purple-500">
                <div>
                  <span class="text-sm font-medium text-gray-800">এক্সক্লুসিভ নিউজ</span>
                  <p class="text-xs text-gray-400">বিশেষ এক্সক্লুসিভ ট্যাগ সহ চিহ্নিত হবে</p>
                </div>
              </label>
            </div>
          </div>

          <!-- Featured Image -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-3">
            <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-3">ফিচার্ড প্রধান ছবি</h3>

            <div v-if="imageUrl" class="relative rounded-xl overflow-hidden border border-gray-200 mb-3 group">
              <img :src="imageUrl" alt="Preview" class="w-full h-44 object-cover" />
              <button
                type="button"
                @click="removeImage"
                class="absolute top-2 right-2 bg-red-600 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                title="ছবি সরান"
              >
                ✕
              </button>
            </div>

            <div class="space-y-3">
              <!-- Featured image action buttons -->
              <div class="grid grid-cols-2 gap-2">
                <label
                  :class="[
                    'flex items-center justify-center gap-1.5 px-3 py-2.5 border-2 border-dashed border-gray-300 hover:border-red-500 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-gray-50 cursor-pointer transition-colors',
                    { 'opacity-50 pointer-events-none': isUploadingFeatured }
                  ]"
                >
                  <span v-if="isUploadingFeatured" class="w-3.5 h-3.5 border-2 border-red-500/30 border-t-red-600 rounded-full animate-spin"></span>
                  <span v-else>📁</span>
                  <span>{{ isUploadingFeatured ? 'আপলোড...' : 'নতুন ছবি' }}</span>
                  <input
                    type="file"
                    @change="uploadFeaturedFile"
                    accept="image/*"
                    :disabled="isUploadingFeatured"
                    class="hidden"
                  />
                </label>

                <button
                  type="button"
                  @click="openFeaturedMediaPicker"
                  class="flex items-center justify-center gap-1.5 px-3 py-2.5 border border-gray-300 hover:border-red-500 hover:bg-red-50/40 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-white transition-colors"
                >
                  <span>🗂️</span> মিডিয়া লাইব্রেরি
                </button>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">অথবা ওয়েব লিংক (URL)</label>
                <input
                  v-model="imageUrl"
                  type="url"
                  placeholder="https://..."
                  class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">ছবির ক্যাপশন / বিবরণ</label>
                <input
                  v-model="imageCaption"
                  type="text"
                  placeholder="ছবি: রয়টার্স / সংগৃহীত..."
                  class="w-full text-sm px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
                />
              </div>
            </div>
          </div>

          <!-- Multi-Image Gallery Card -->
          <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
              <div>
                <h3 class="text-base font-bold text-gray-900">ছবি গ্যালারি (মাল্টি-ইমেজ)</h3>
                <p class="text-xs text-gray-400">সংবাদের অতিরিক্ত ছবি বা ফটো অ্যালবাম যোগ করুন</p>
              </div>
              <span class="text-xs px-2.5 py-1 bg-red-50 text-red-600 rounded-full font-bold">
                {{ galleryImages.length }} টি
              </span>
            </div>

            <!-- Gallery action buttons -->
            <div class="grid grid-cols-2 gap-2">
              <label
                :class="[
                  'flex items-center justify-center gap-1.5 px-3 py-2.5 border-2 border-dashed border-gray-300 hover:border-red-500 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-gray-50 cursor-pointer transition-colors text-center',
                  { 'opacity-50 pointer-events-none': isUploadingGallery }
                ]"
              >
                <span v-if="isUploadingGallery" class="w-3.5 h-3.5 border-2 border-red-500/30 border-t-red-600 rounded-full animate-spin"></span>
                <span v-else>📸</span>
                <span>{{ isUploadingGallery ? 'আপলোড...' : 'নতুন ছবি আপলোড' }}</span>
                <input
                  type="file"
                  multiple
                  accept="image/*"
                  @change="uploadGalleryFiles"
                  :disabled="isUploadingGallery"
                  class="hidden"
                />
              </label>

              <button
                type="button"
                @click="openGalleryMediaPicker"
                class="flex items-center justify-center gap-1.5 px-3 py-2.5 border border-gray-300 hover:border-red-500 hover:bg-red-50/40 rounded-xl text-xs font-semibold text-gray-700 hover:text-red-600 bg-white transition-colors"
              >
                <span>🗂️</span> মিডিয়া লাইব্রেরি
              </button>
            </div>

            <!-- Gallery Previews Grid -->
            <div v-if="galleryImages.length > 0" class="grid grid-cols-2 gap-3 pt-1">
              <div
                v-for="(img, idx) in galleryImages"
                :key="idx"
                class="relative rounded-xl overflow-hidden border border-gray-200 group bg-gray-50 flex flex-col justify-between"
              >
                <div class="relative h-24 bg-gray-200">
                  <img :src="img.url" alt="Gallery item" class="w-full h-full object-cover" />
                  <button
                    type="button"
                    @click="removeGalleryImage(idx)"
                    class="absolute top-1 right-1 bg-red-600 text-white w-5 h-5 flex items-center justify-center rounded-full text-xs shadow hover:bg-red-700 transition-colors"
                    title="মুছে ফেলুন"
                  >
                    ✕
                  </button>
                </div>
                <input
                  v-model="img.caption"
                  type="text"
                  placeholder="ক্যাপশন..."
                  class="w-full text-[11px] px-2 py-1 bg-white border-t border-gray-100 outline-none focus:bg-red-50/20"
                />
              </div>
            </div>

            <!-- Add by URL input -->
            <div class="flex items-center gap-2 pt-1">
              <input
                v-model="newGalleryUrl"
                type="url"
                placeholder="ওয়েব ছবির লিঙ্ক (URL) দিন..."
                class="w-full text-xs px-3 py-2 border border-gray-200 rounded-lg focus:border-red-500 outline-none"
                @keydown.enter.prevent="addGalleryUrl"
              />
              <button
                type="button"
                @click="addGalleryUrl"
                class="px-3 py-2 bg-gray-800 hover:bg-black text-white text-xs font-semibold rounded-lg shrink-0 transition-colors"
              >
                যুক্ত
              </button>
            </div>
          </div>

        </div>
      </form>

      <!-- Media Picker Modal -->
      <MediaPickerModal
        :show="showMediaPicker"
        :multiple="mediaPickerMultiple"
        :title="mediaPickerTitle"
        @close="showMediaPicker = false"
        @select="handleMediaPickerSelect"
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import RichTipTapEditor from '@/Components/UI/RichTipTapEditor.vue'
import MediaPickerModal from '@/Components/UI/MediaPickerModal.vue'

const props = defineProps({
  post: {
    type: Object,
    default: null,
  },
  categories: {
    type: Array,
    default: () => [],
  },
})

const isEdit = computed(() => !!props.post?.id)

// Featured image states
const imageUrl = ref(props.post?.featured_image?.original || (typeof props.post?.featured_image === 'string' ? props.post?.featured_image : ''))
const imageCaption = ref(props.post?.featured_image?.caption || props.post?.featured_image?.alt || '')
const isUploadingFeatured = ref(false)

// Gallery images states
const initialGallery = computed(() => {
  const g = props.post?.featured_image?.gallery
  if (Array.isArray(g)) {
    return g.map(item => (typeof item === 'string' ? { url: item, caption: '' } : item))
  }
  return []
})
const galleryImages = ref([...initialGallery.value])
const isUploadingGallery = ref(false)
const newGalleryUrl = ref('')

// Media picker states
const showMediaPicker = ref(false)
const mediaPickerMultiple = ref(false)
const mediaPickerTarget = ref('featured') // 'featured' | 'gallery'
const mediaPickerTitle = computed(() => {
  return mediaPickerTarget.value === 'featured'
    ? 'মিডিয়া লাইব্রেরি থেকে প্রধান ছবি নির্বাচন করুন'
    : 'মিডিয়া লাইব্রেরি থেকে গ্যালারিতে ছবি যোগ করুন'
})

const openFeaturedMediaPicker = () => {
  mediaPickerTarget.value = 'featured'
  mediaPickerMultiple.value = false
  showMediaPicker.value = true
}

const openGalleryMediaPicker = () => {
  mediaPickerTarget.value = 'gallery'
  mediaPickerMultiple.value = true
  showMediaPicker.value = true
}

const handleMediaPickerSelect = (selected) => {
  if (mediaPickerTarget.value === 'featured') {
    const item = Array.isArray(selected) ? selected[0] : selected
    if (item?.url) {
      imageUrl.value = item.url
    }
  } else {
    const items = Array.isArray(selected) ? selected : [selected]
    items.forEach(item => {
      if (item?.url) {
        galleryImages.value.push({
          url: item.url,
          caption: item.name ? item.name.replace(/\.[^/.]+$/, '') : '',
        })
      }
    })
  }
}

const form = useForm({
  title: props.post?.title || '',
  slug: props.post?.slug || '',
  excerpt: props.post?.excerpt || '',
  body: props.post?.body || '',
  category_id: props.post?.category_id ?? (props.categories[0]?.id ?? ''),
  type: props.post?.type?.value || props.post?.type || 'article',
  status: props.post?.status?.value || props.post?.status || 'draft',
  is_breaking: Boolean(props.post?.is_breaking),
  is_featured: Boolean(props.post?.is_featured),
  is_exclusive: Boolean(props.post?.is_exclusive),
  seo_title: props.post?.seo_title || '',
  seo_description: props.post?.seo_description || '',
  featured_image: props.post?.featured_image || null,
})

const removeImage = () => {
  imageUrl.value = ''
  imageCaption.value = ''
  form.featured_image = null
}

const uploadFeaturedFile = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  isUploadingFeatured.value = true
  const formData = new FormData()
  formData.append('file', file)

  try {
    const res = await window.axios.post(route('dashboard.media.upload'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (res.data?.url) {
      imageUrl.value = res.data.url
    }
  } catch (err) {
    console.error('Featured image upload failed:', err)
    alert('ছবি আপলোড করতে ব্যর্থ হয়েছে। সর্বোচ্চ ফাইল সাইজ ১০MB-এর মধ্যে হতে হবে।')
  } finally {
    isUploadingFeatured.value = false
    e.target.value = ''
  }
}

const uploadGalleryFiles = async (e) => {
  const fileList = Array.from(e.target.files || [])
  if (fileList.length === 0) return

  isUploadingGallery.value = true
  const formData = new FormData()

  if (fileList.length === 1) {
    formData.append('file', fileList[0])
  } else {
    fileList.forEach(file => {
      formData.append('files[]', file)
    })
  }

  try {
    const res = await window.axios.post(route('dashboard.media.upload'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (res.data?.files && Array.isArray(res.data.files)) {
      res.data.files.forEach(item => {
        if (item.url) {
          galleryImages.value.push({ url: item.url, caption: '' })
        }
      })
    } else if (res.data?.url) {
      galleryImages.value.push({ url: res.data.url, caption: '' })
    }
  } catch (err) {
    console.error('Gallery images upload failed:', err)
    alert('গ্যালারির ছবি আপলোড করতে ব্যর্থ হয়েছে। সর্বোচ্চ ফাইল সাইজ ১০MB-এর মধ্যে হতে হবে।')
  } finally {
    isUploadingGallery.value = false
    e.target.value = ''
  }
}

const addGalleryUrl = () => {
  if (!newGalleryUrl.value.trim()) return
  galleryImages.value.push({
    url: newGalleryUrl.value.trim(),
    caption: '',
  })
  newGalleryUrl.value = ''
}

const removeGalleryImage = (index) => {
  galleryImages.value.splice(index, 1)
}

const submit = (targetStatus) => {
  if (imageUrl.value || galleryImages.value.length > 0) {
    form.featured_image = {
      original: imageUrl.value || (galleryImages.value[0]?.url ?? ''),
      sm: imageUrl.value || (galleryImages.value[0]?.url ?? ''),
      md: imageUrl.value || (galleryImages.value[0]?.url ?? ''),
      caption: imageCaption.value,
      alt: imageCaption.value,
      gallery: galleryImages.value,
    }
  }

  form.status = targetStatus

  if (isEdit.value) {
    form.put(route('dashboard.posts.update', props.post.id))
  } else {
    form.post(route('dashboard.posts.store'))
  }
}
</script>
