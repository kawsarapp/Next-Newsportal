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
        @click="openLinkModal"
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

      <!-- Delete Selected Media Button (Appears when any Image or Iframe is selected) -->
      <button
        v-if="isMediaSelected"
        type="button"
        @click="deleteSelectedMedia"
        class="toolbar-btn text-xs font-semibold text-white bg-red-600 hover:bg-red-700 flex items-center gap-1.5 px-3 h-[1.85rem] rounded-md transition-colors shadow-sm ml-auto animate-pulse"
        :title="`নির্বাচিত ${selectedMediaLabel} মুছে ফেলুন (Backspace / Delete)`"
      >
        <span>🗑️</span>
        <span>{{ selectedMediaLabel }} মুছে ফেলুন</span>
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

    <!-- Link Insertion Modal -->
    <LinkModal
      :show="showLinkModal"
      :selected-text="linkModalSelectedText"
      :initial-url="linkModalInitialUrl"
      :is-linked="isCurrentLinkActive"
      @close="showLinkModal = false"
      @insert="handleLinkInsert"
      @unlink="handleUnlink"
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
import LinkModal from '@/Components/UI/LinkModal.vue'
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
const showLinkModal = ref(false)
const linkModalSelectedText = ref('')
const linkModalInitialUrl = ref('')
const isCurrentLinkActive = ref(false)
const isMediaSelected = ref(false)
const selectedMediaLabel = ref('')

const CustomImage = Image.extend({
  selectable: true,
  draggable: true,
  addNodeView() {
    return ({ node, HTMLAttributes, getPos, editor }) => {
      const dom = document.createElement('div')
      dom.className = 'relative my-4 inline-block max-w-full group tiptap-image-container select-none'
      dom.setAttribute('data-tiptap-image', 'true')

      const img = document.createElement('img')
      const mergedAttrs = { ...this.options.HTMLAttributes, ...HTMLAttributes }
      Object.entries(mergedAttrs).forEach(([key, val]) => {
        if (val !== null && val !== undefined) {
          img.setAttribute(key, val)
        }
      })
      img.className = (mergedAttrs.class || '') + ' block cursor-pointer transition-all'

      // Floating control bar / delete button
      const controlBar = document.createElement('div')
      controlBar.className = 'absolute top-3 right-3 z-20 flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition-opacity'

      const deleteBtn = document.createElement('button')
      deleteBtn.type = 'button'
      deleteBtn.className = 'flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-md transition-colors cursor-pointer'
      deleteBtn.innerHTML = '<span>🗑️</span><span>মুছে ফেলুন</span>'
      deleteBtn.title = 'ছবিটি মুছে ফেলুন (Delete)'

      const handleDelete = (e) => {
        e.preventDefault()
        e.stopPropagation()
        if (typeof getPos === 'function') {
          const pos = getPos()
          editor.chain().focus().deleteRange({ from: pos, to: pos + node.nodeSize }).run()
        }
      }

      deleteBtn.addEventListener('click', handleDelete)
      deleteBtn.addEventListener('mousedown', (e) => {
        e.preventDefault()
        e.stopPropagation()
      })

      img.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()
        if (typeof getPos === 'function') {
          const pos = getPos()
          editor.commands.setNodeSelection(pos)
        }
      })

      controlBar.appendChild(deleteBtn)
      dom.appendChild(img)
      dom.appendChild(controlBar)

      return {
        dom,
        selectNode: () => {
          dom.classList.add('ProseMirror-selectednode')
          img.classList.add('ring-4', 'ring-red-500', 'border-red-500')
          controlBar.classList.remove('opacity-0')
          controlBar.classList.add('opacity-100')
        },
        deselectNode: () => {
          dom.classList.remove('ProseMirror-selectednode')
          img.classList.remove('ring-4', 'ring-red-500', 'border-red-500')
        },
        stopEvent: (event) => {
          return controlBar.contains(event.target)
        },
        ignoreMutation: () => true,
        destroy: () => {
          deleteBtn.removeEventListener('click', handleDelete)
        },
      }
    }
  },
})

const updateMediaSelection = (ed) => {
  if (!ed) {
    isMediaSelected.value = false
    selectedMediaLabel.value = ''
    return
  }
  const { selection } = ed.state
  if (selection && selection.node) {
    const nodeName = selection.node.type.name
    if (nodeName === 'image' || nodeName === 'iframe') {
      isMediaSelected.value = true
      selectedMediaLabel.value = nodeName === 'iframe' ? 'ভিডিও' : 'ছবি'
      return
    }
  }
  isMediaSelected.value = false
  selectedMediaLabel.value = ''
}

const deleteSelectedMedia = () => {
  if (editor.value) {
    editor.value.chain().focus().deleteSelection().run()
    isMediaSelected.value = false
  }
}

const editor = useEditor({
  content: props.modelValue,
  extensions: [
    StarterKit,
    Underline,
    Iframe,
    Link.configure({
      autolink: true,
      linkOnPaste: true,
      openOnClick: false,
      HTMLAttributes: {
        class: 'text-red-600 underline font-semibold',
      }
    }),
    CustomImage.configure({
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
  onSelectionUpdate: ({ editor }) => {
    updateMediaSelection(editor)
  },
  onUpdate: ({ editor }) => {
    emit('update:modelValue', editor.getHTML())
    updateMediaSelection(editor)
  }
})

watch(() => props.modelValue, (val) => {
  if (editor.value && editor.value.getHTML() !== val) {
    editor.value.commands.setContent(val || '', false)
  }
})

const wordCount = computed(() => editor.value?.storage.characterCount.words() ?? 0)
const characterCount = computed(() => editor.value?.storage.characterCount.characters() ?? 0)

const openLinkModal = () => {
  if (!editor.value) return
  const { from, to } = editor.value.state.selection
  const text = editor.value.state.doc.textBetween(from, to, ' ')
  linkModalSelectedText.value = text.trim()
  linkModalInitialUrl.value = editor.value.getAttributes('link').href || ''
  isCurrentLinkActive.value = editor.value.isActive('link')
  showLinkModal.value = true
}

const handleLinkInsert = ({ url, text, target }) => {
  if (!editor.value) return

  const { from, to } = editor.value.state.selection
  const hasSelection = from !== to

  const attrs = {
    href: url,
    target: target || null,
    class: 'text-red-600 underline font-semibold',
  }

  if (hasSelection) {
    editor.value
      .chain()
      .focus()
      .extendMarkRange('link')
      .setLink(attrs)
      .run()
  } else {
    const targetAttr = target ? ` target="${target}" rel="noopener noreferrer"` : ''
    const linkHtml = `<a href="${url}" class="text-red-600 underline font-semibold"${targetAttr}>${text || url}</a> `
    editor.value
      .chain()
      .focus()
      .insertContent(linkHtml)
      .run()
  }
}

const handleUnlink = () => {
  if (!editor.value) return
  editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
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
      headers: {
        'Content-Type': 'multipart/form-data',
        Accept: 'application/json',
      },
    })

    if (res.data?.files && Array.isArray(res.data.files)) {
      res.data.files.forEach(item => {
        if (item.url) {
          editor.value.chain().focus().setImage({ src: item.url }).createParagraphNear().run()
        }
      })
    } else if (res.data?.url) {
      editor.value.chain().focus().setImage({ src: res.data.url }).createParagraphNear().run()
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
      editor.value?.chain().focus().setImage({ src: item.url }).createParagraphNear().run()
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
/* 1. Landscape Videos (16:9) */
:deep(.tiptap iframe.facebook-video-landscape),
:deep(.tiptap iframe[data-embed-type="facebook-video"]),
:deep(.tiptap iframe[data-orientation="landscape"]),
:deep(.tiptap iframe[data-embed-type="youtube"]),
:deep(.tiptap iframe[src*="facebook.com/plugins/video"]:not([data-orientation="vertical"]):not([data-orientation="square"]):not(.facebook-video-vertical):not(.facebook-video-square)),
:deep(.tiptap iframe[src*="plugins/video.php"]:not([data-orientation="vertical"]):not([data-orientation="square"]):not(.facebook-video-vertical):not(.facebook-video-square)),
:deep(.tiptap iframe.facebook-video:not(.facebook-video-vertical):not(.facebook-video-square):not([data-orientation="vertical"]):not([data-orientation="square"])) {
  aspect-ratio: 16 / 9 !important;
  width: 100% !important;
  max-width: 680px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  border: none !important;
  overflow: hidden !important;
}

/* 2. Facebook Vertical Videos / Reels (9:16) */
:deep(.tiptap iframe.facebook-video-vertical),
:deep(.tiptap iframe[data-embed-type="facebook-reel"]),
:deep(.tiptap iframe[data-orientation="vertical"]) {
  aspect-ratio: 9 / 16 !important;
  width: 100% !important;
  max-width: 380px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  border: none !important;
  overflow: hidden !important;
  background-color: #000 !important;
}

/* 3. Facebook Square Videos (1:1) */
:deep(.tiptap iframe.facebook-video-square),
:deep(.tiptap iframe[data-embed-type="facebook-square"]),
:deep(.tiptap iframe[data-orientation="square"]) {
  aspect-ratio: 1 / 1 !important;
  width: 100% !important;
  max-width: 520px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  border: none !important;
  overflow: hidden !important;
}

/* 4. YouTube Shorts (Kept untouched and separate) */
:deep(.tiptap iframe[src*="youtube.com/shorts/"]),
:deep(.tiptap iframe[data-embed-type="youtube-shorts"]) {
  aspect-ratio: 9 / 16 !important;
  width: 100% !important;
  max-width: 380px !important;
  min-height: 560px !important;
  height: auto !important;
  display: block !important;
  margin: 1.5rem auto !important;
  border-radius: 0.75rem !important;
  overflow: hidden !important;
}

/* Mobile Responsive Adjustments */
@media (max-width: 640px) {
  :deep(.tiptap iframe.facebook-video-vertical),
  :deep(.tiptap iframe[data-embed-type="facebook-reel"]),
  :deep(.tiptap iframe[data-orientation="vertical"]) {
    max-width: min(100%, 340px) !important;
    width: 100% !important;
    aspect-ratio: 9 / 16 !important;
    height: auto !important;
  }

  :deep(.tiptap iframe.facebook-video-square),
  :deep(.tiptap iframe[data-orientation="square"]) {
    max-width: 100% !important;
    width: 100% !important;
    aspect-ratio: 1 / 1 !important;
    height: auto !important;
  }

  :deep(.tiptap iframe.facebook-video-landscape),
  :deep(.tiptap iframe[data-orientation="landscape"]) {
    max-width: 100% !important;
    width: 100% !important;
    aspect-ratio: 16 / 9 !important;
    height: auto !important;
  }

  :deep(.tiptap iframe[src*="youtube.com/shorts/"]),
  :deep(.tiptap iframe[data-embed-type="youtube-shorts"]) {
    max-width: 340px !important;
    min-height: 500px !important;
  }
}
:deep(.tiptap .ProseMirror-selectednode),
:deep(.tiptap img.ProseMirror-selectednode) {
  outline: 3px solid #ef4444 !important;
  outline-offset: 3px;
  border-radius: 0.75rem;
}
:deep(.tiptap .tiptap-iframe-container.ProseMirror-selectednode),
:deep(.tiptap .tiptap-image-container.ProseMirror-selectednode) {
  outline: 3px solid #ef4444 !important;
  outline-offset: 3px;
  border-radius: 0.75rem;
}
</style>
