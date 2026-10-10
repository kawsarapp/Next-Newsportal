<template>
  <div class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm focus-within:border-red-500 focus-within:ring-1 focus-within:ring-red-500 transition-all">
    <!-- Toolbar -->
    <div v-if="editor" class="flex flex-wrap items-center gap-1 p-2 bg-gray-50 border-b border-gray-200">
      <button
        type="button"
        @click="editor.chain().focus().toggleBold().run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('bold') }]"
        title="Bold"
      >
        <strong class="font-bold">B</strong>
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleItalic().run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('italic') }]"
        title="Italic"
      >
        <span class="italic font-serif">I</span>
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleUnderline().run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('underline') }]"
        title="Underline"
      >
        <span class="underline">U</span>
      </button>

      <span class="w-[1px] h-5 bg-gray-300 mx-1"></span>

      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('heading', { level: 2 }) }]"
        title="Heading 2"
      >
        H2
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('heading', { level: 3 }) }]"
        title="Heading 3"
      >
        H3
      </button>

      <span class="w-[1px] h-5 bg-gray-300 mx-1"></span>

      <button
        type="button"
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('bulletList') }]"
        title="Bullet List"
      >
        • List
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleOrderedList().run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('orderedList') }]"
        title="Numbered List"
      >
        1. List
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleBlockquote().run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive('blockquote') }]"
        title="Quote"
      >
        ❝
      </button>

      <span class="w-[1px] h-5 bg-gray-300 mx-1"></span>

      <button
        type="button"
        @click="editor.chain().focus().setTextAlign('left').run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive({ textAlign: 'left' }) }]"
        title="Align Left"
      >
        ⇤
      </button>
      <button
        type="button"
        @click="editor.chain().focus().setTextAlign('center').run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive({ textAlign: 'center' }) }]"
        title="Align Center"
      >
        ≡
      </button>
      <button
        type="button"
        @click="editor.chain().focus().setTextAlign('right').run()"
        :class="['toolbar-btn', { 'is-active': editor.isActive({ textAlign: 'right' }) }]"
        title="Align Right"
      >
        ⇥
      </button>

      <span class="w-[1px] h-5 bg-gray-300 mx-1"></span>

      <button
        type="button"
        @click="setLink"
        :class="['toolbar-btn', { 'is-active': editor.isActive('link') }]"
        title="Add Link"
      >
        🔗
      </button>
      <label
        :class="['toolbar-btn cursor-pointer relative', { 'pointer-events-none opacity-50': isUploading }]"
        title="কম্পিউটার থেকে ছবি আপলোড (এক বা একাধিক ছবি)"
      >
        <span v-if="isUploading" class="w-3.5 h-3.5 border-2 border-red-500/30 border-t-red-600 rounded-full animate-spin"></span>
        <span v-else>🖼️</span>
        <input
          type="file"
          @change="uploadInlineImage"
          accept="image/*"
          multiple
          :disabled="isUploading"
          class="hidden"
        />
      </label>
      <button
        type="button"
        @click="showMediaPicker = true"
        class="toolbar-btn text-xs font-semibold"
        title="মিডিয়া লাইব্রেরি থেকে সংরক্ষিত ছবি বাছুন"
      >
        🗂️
      </button>
      <button
        type="button"
        @click="addImageUrl"
        class="toolbar-btn"
        title="ছবির লিঙ্ক (URL) দিন"
      >
        🔗🖼️
      </button>

      <span class="w-[1px] h-5 bg-gray-300 mx-1"></span>

      <button
        type="button"
        @click="showEmbedModal = true"
        class="toolbar-btn text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 flex items-center gap-1.5 px-2.5 h-[1.85rem] rounded-md transition-colors"
        title="সোশ্যাল মিডিয়া ও ভিডিও এম্বেড (ইউটিউব, ফেসবুক, টুইটার/X, ইত্যাদি)"
      >
        <span>📹</span>
        <span class="hidden sm:inline">সোশ্যাল / ভিডিও এম্বেড</span>
      </button>


      <span class="w-[1px] h-5 bg-gray-300 mx-1"></span>

      <button
        type="button"
        @click="editor.chain().focus().undo().run()"
        :disabled="!editor.can().undo()"
        class="toolbar-btn disabled:opacity-40"
        title="Undo"
      >
        ↩
      </button>
      <button
        type="button"
        @click="editor.chain().focus().redo().run()"
        :disabled="!editor.can().redo()"
        class="toolbar-btn disabled:opacity-40"
        title="Redo"
      >
        ↪
      </button>
    </div>

    <!-- Editor Surface -->
    <EditorContent :editor="editor" class="prose max-w-none p-4 min-h-[350px] focus:outline-none" />

    <!-- Word/Char Counter Footer -->
    <div v-if="editor" class="px-4 py-2 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
      <div class="flex items-center gap-4">
        <span>শব্দ: <strong>{{ wordCount }}</strong></span>
        <span>অক্ষর: <strong>{{ characterCount }}</strong></span>
      </div>
      <div>
        <span class="text-gray-400">অটোসেভ সমর্থিত</span>
      </div>
    </div>

    <!-- Media Picker Modal -->
    <!-- Media Picker Modal -->
    <MediaPickerModal
      :show="showMediaPicker"
      :multiple="true"
      title="মিডিয়া লাইব্রেরি থেকে আর্টিকেলে ছবি যোগ করুন"
      @close="showMediaPicker = false"
      @select="handleMediaPickerSelect"
    />

    <!-- Media Embed Modal (YouTube, Facebook, Twitter, Custom) -->
    <MediaEmbedModal
      :show="showEmbedModal"
      @close="showEmbedModal = false"
      @insert="handleEmbedInsert"
    />
  </div>
</template>

<script setup>
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'
import Image from '@tiptap/extension-image'
import TextAlign from '@tiptap/extension-text-align'
import Placeholder from '@tiptap/extension-placeholder'
import CharacterCount from '@tiptap/extension-character-count'
import MediaPickerModal from '@/Components/UI/MediaPickerModal.vue'
import MediaEmbedModal from '@/Components/UI/MediaEmbedModal.vue'
import Iframe from '@/Components/UI/TiptapExtensions/Iframe.js'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },
  placeholder: {
    type: String,
    default: 'এখানে বিস্তারিত সংবাদ লিখুন...',
  }
})

const emit = defineEmits(['update:modelValue'])

const showEmbedModal = ref(false)

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Underline,
    Iframe,
    Link.configure({
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-red-600 underline font-medium',
      }
    }),
    Image.configure({
      HTMLAttributes: {
        class: 'rounded-xl max-w-full my-4 border border-gray-100 shadow-sm',
      }
    }),
    TextAlign.configure({
      types: ['heading', 'paragraph'],
    }),
    Placeholder.configure({
      placeholder: props.placeholder,
    }),
    CharacterCount,
  ],
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
  }
})

watch(() => props.modelValue, (val) => {
  if (editor.value && editor.value.getHTML() !== val) {
    editor.value.commands.setContent(val || '', false)
  }
})

const wordCount = computed(() => editor.value?.storage.characterCount.words() ?? 0)
const characterCount = computed(() => editor.value?.storage.characterCount.characters() ?? 0)

const setLink = () => {
  const previousUrl = editor.value.getAttributes('link').href
  const url = window.prompt('URL প্রবেশ করান:', previousUrl)

  if (url === null) return
  if (url === '') {
    editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
    return
  }
  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

const addImageUrl = () => {
  const url = window.prompt('ছবির সরাসরি লিংক (URL) দিন:')
  if (url) {
    editor.value.chain().focus().setImage({ src: url }).run()
  }
}

const isUploading = ref(false)

const uploadInlineImage = async (e) => {
  const fileList = Array.from(e.target.files || [])
  if (fileList.length === 0) return

  isUploading.value = true
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
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (res.data?.files && Array.isArray(res.data.files)) {
      res.data.files.forEach(item => {
        if (item.url) {
          editor.value.chain().focus().setImage({ src: item.url }).run()
        }
      })
    } else if (res.data?.url) {
      editor.value.chain().focus().setImage({ src: res.data.url }).run()
    }
  } catch (err) {
    console.error('Image upload failed:', err)
    alert('ছবি আপলোড করতে ব্যর্থ হয়েছে। সর্বোচ্চ ফাইল সাইজ ১০MB-এর মধ্যে হতে হবে।')
  } finally {
    isUploading.value = false
    e.target.value = ''
  }
}


const showMediaPicker = ref(false)

const handleMediaPickerSelect = (selected) => {
  const items = Array.isArray(selected) ? selected : [selected]
  items.forEach(item => {
    if (item.url) {
      editor.value?.chain().focus().setImage({ src: item.url }).run()
    }
  })
}

const handleEmbedInsert = (embedData) => {
  if (editor.value) {
    editor.value.chain().focus().setIframe(embedData).createParagraphNear().run()
  }
}

onBeforeUnmount(() => {
  editor.value?.destroy()
})
</script>

<style scoped>
.toolbar-btn {
  padding: 0.35rem 0.55rem;
  font-size: 0.825rem;
  border-radius: 0.375rem;
  color: #4b5563;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
  min-width: 1.85rem;
  height: 1.85rem;
}
.toolbar-btn:hover {
  background-color: #e5e7eb;
  color: #111827;
}
.toolbar-btn.is-active {
  background-color: #fee2e2;
  color: #dc2626;
  font-weight: 600;
}
:deep(.tiptap p.is-editor-empty:first-child::before) {
  content: attr(data-placeholder);
  float: left;
  color: #9ca3af;
  pointer-events: none;
  height: 0;
}
:deep(.tiptap) {
  outline: none;
  min-height: 280px;
}
:deep(.tiptap blockquote) {
  border-left: 3px solid #dc2626;
  padding-left: 1rem;
  font-style: italic;
  color: #4b5563;
  margin: 1rem 0;
}
:deep(.tiptap iframe) {
  border-radius: 0.75rem;
  border: 1px solid #e5e7eb;
  background-color: #f3f4f6;
  max-width: 100%;
}
:deep(.tiptap iframe.ProseMirror-selectednode) {
  outline: 3px solid #ef4444;
}
</style>
