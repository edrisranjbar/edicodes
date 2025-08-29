<template>
  <div class="bg-black/30 border border-white/10 rounded-lg">
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-2 p-3 border-b border-white/10" dir="rtl">
      <button type="button" class="btn" :class="{ active: isActive('bold') }" @click="exec('toggleBold')">
        <font-awesome-icon icon="bold" />
      </button>
      <button type="button" class="btn" :class="{ active: isActive('italic') }" @click="exec('toggleItalic')">
        <font-awesome-icon icon="italic" />
      </button>
      <button type="button" class="btn" :class="{ active: isActive('underline') }" @click="exec('toggleUnderline')">
        <font-awesome-icon icon="underline" />
      </button>
      <span class="h-6 w-px bg-white/10"></span>
      <button type="button" class="btn" :class="{ active: isActive('heading', { level: 2 }) }" @click="exec('toggleHeading', { level: 2 })">H2</button>
      <button type="button" class="btn" :class="{ active: isActive('heading', { level: 3 }) }" @click="exec('toggleHeading', { level: 3 })">H3</button>
      <button type="button" class="btn" :class="{ active: isActive('paragraph') }" @click="exec('setParagraph')">
        <font-awesome-icon icon="paragraph" />
      </button>
      <span class="h-6 w-px bg-white/10"></span>
      <button type="button" class="btn" :class="{ active: isActive('bulletList') }" @click="exec('toggleBulletList')">
        <font-awesome-icon icon="list-ul" />
      </button>
      <button type="button" class="btn" :class="{ active: isActive('orderedList') }" @click="exec('toggleOrderedList')">
        <font-awesome-icon icon="list-ol" />
      </button>
      <button type="button" class="btn" :class="{ active: isActive('blockquote') }" @click="exec('toggleBlockquote')">
        <font-awesome-icon icon="quote-right" />
      </button>
      <button type="button" class="btn" :class="{ active: isActive('codeBlock') }" @click="exec('toggleCodeBlock')">
        <font-awesome-icon icon="code" />
      </button>
      <span class="h-6 w-px bg-white/10"></span>
      <button type="button" class="btn" @click="setLink">
        <font-awesome-icon icon="link" />
      </button>
      <button type="button" class="btn" @click="unsetLink" :disabled="!isActive('link')">
        <font-awesome-icon icon="unlink" />
      </button>
      <span class="h-6 w-px bg-white/10"></span>
      <button type="button" class="btn" @click="triggerImageUpload">
        <font-awesome-icon icon="image" />
      </button>
      <button type="button" class="btn" @click="embedYouTube">
        <font-awesome-icon icon="video" />
      </button>
      <button type="button" class="btn" @click="embedVideoUrl" title="Embed video by URL">
        <font-awesome-icon icon="music" />
      </button>
      <span class="ml-auto"></span>
      <button type="button" class="btn" @click="exec('undo')">
        <font-awesome-icon icon="undo" />
      </button>
      <button type="button" class="btn" @click="exec('redo')">
        <font-awesome-icon icon="redo" />
      </button>
      <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleImageFile" />
    </div>

    

    <!-- Editor -->
    <div class="p-4">
      <EditorContent :editor="editor" class="tiptap font-vazir" />
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Youtube from '@tiptap/extension-youtube'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
import { createLowlight, common } from 'lowlight'
import Placeholder from '@tiptap/extension-placeholder'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'
import Underline from '@tiptap/extension-underline'
import Link from '@tiptap/extension-link'

const props = defineProps({
  modelValue: { type: String, default: '' },
  uploadUrl: { type: String, default: '' },
  authToken: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue'])

const editor = ref(null)
const fileInput = ref(null)
const isUploadingImage = ref(false)

const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms))

async function ensureEditorReady(timeoutMs = 5000) {
  const start = Date.now()
  while (Date.now() - start < timeoutMs) {
    const ed = editor.value
    if (ed && !ed.isDestroyed && ed.view) return ed
    await sleep(50)
  }
  return null
}

onMounted(() => {
  const lowlightInstance = createLowlight(common)
  editor.value = new Editor({
    content: props.modelValue || '',
    extensions: [
      StarterKit.configure({ codeBlock: false }),
      Image.configure({ inline: false }),
      CodeBlockLowlight.configure({ lowlight: lowlightInstance }),
      Underline,
      Link.configure({ openOnClick: true, autolink: true, linkOnPaste: true }),
      Placeholder.configure({
        placeholder: 'برای شروع نوشتن، اینجا کلیک کنید… / برای درج بلوک‌ها از منوی شناور استفاده کنید',
        includeChildren: true
      }),
      TaskList,
      TaskItem.configure({ nested: true }),
      Table.configure({ resizable: true }),
      TableRow,
      TableHeader,
      TableCell,
      Youtube.configure({ controls: true, nocookie: true })
    ],
    editorProps: {
      attributes: {
        dir: 'rtl',
        class: 'prose prose-invert max-w-none min-h-[16rem] focus:outline-none'
      }
    },
    onUpdate: ({ editor }) => {
      // Ensure HTML inside <pre><code> is escaped so browser doesn't parse and strip it
      const html = editor.getHTML()
      const escaped = escapeHtmlInsideCodeBlocks(html)
      emit('update:modelValue', escaped)
    }
  })
})

onBeforeUnmount(() => {
  if (editor.value) editor.value.destroy()
})

watch(() => props.modelValue, (html) => {
  if (!editor.value) return
  const current = editor.value.getHTML()
  if (html !== current) {
    editor.value.commands.setContent(html || '', false)
  }
})

function isActive(name, attrs) {
  return editor.value ? editor.value.isActive(name, attrs) : false
}

function exec(command, args) {
  if (!editor.value) return
  const chain = editor.value.chain().focus()
  if (args) chain[command](args).run()
  else chain[command]().run()
}

function setLink() {
  if (!editor.value) return
  const prev = editor.value.getAttributes('link').href || ''
  const url = window.prompt('نشانی لینک را وارد کنید:', prev)
  if (url === null) return
  if (url === '') {
    return unsetLink()
  }
  editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
}

function unsetLink() {
  if (!editor.value) return
  editor.value.chain().focus().unsetLink().run()
}

function triggerImageUpload() {
  if (fileInput.value) fileInput.value.value = ''
  fileInput.value?.click()
}

async function handleImageFile(event) {
  const file = event.target.files?.[0]
  if (!file) return
  const ed = await ensureEditorReady()
  try {
    isUploadingImage.value = true
    // Insert a temporary local preview immediately (block image)
    const previewUrl = URL.createObjectURL(file)
    if (ed) {
      ed.chain().focus().insertContent({ type: 'image', attrs: { src: previewUrl } }).run()
    }
    const form = new FormData()
    form.append('file', file)
    const res = await fetch(props.uploadUrl || `${import.meta.env.VITE_API_BASE_URL}/admin/upload/image`, {
      method: 'POST',
      headers: {
        ...(props.authToken ? { 'Authorization': `Bearer ${props.authToken}` } : {})
      },
      body: form
    })
    const data = await res.json()
    if (!res.ok || !data.url) throw new Error(data.message || 'Upload failed')
    if (!ed) {
      // Fallback: update model so content includes the image when editor mounts (block image)
      emit('update:modelValue', (props.modelValue || '') + `\n<img src="${data.url}" alt="" />`)
      return
    }
    // Replace the preview image with the final URL
    const { state, dispatch } = ed.view
    let replaced = false
    state.doc.descendants((node, pos) => {
      if (node.type.name === 'image' && node.attrs.src === previewUrl) {
        const tr = state.tr.setNodeMarkup(pos, undefined, { ...node.attrs, src: data.url })
        dispatch(tr)
        replaced = true
        return false
      }
    })
    if (!replaced) {
      // Fallback insert if preview wasn't found (block image)
      ed.chain().focus().insertContent({ type: 'image', attrs: { src: data.url } }).run()
    }
    URL.revokeObjectURL(previewUrl)
  } catch (e) {
    console.error('Image upload error:', e)
    window.alert('خطا در آپلود تصویر')
  } finally {
    if (event?.target) event.target.value = ''
    isUploadingImage.value = false
  }
}

function embedYouTube() {
  const url = window.prompt('نشانی ویدیو یوتیوب را وارد کنید:')
  if (!url) return
  editor.value?.commands.setYoutubeVideo({ src: url, width: 640, height: 360 })
}

function embedVideoUrl() {
  const url = window.prompt('نشانی ویدیو (mp4/webm) را وارد کنید:')
  if (!url) return
  const html = `<video controls style="max-width: 100%; border-radius: 0.5rem"><source src="${url}" /></video>`
  editor.value?.commands.insertContent(html)
}

function insertTable() {
  editor.value?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()
}

// Escapes inner HTML of code blocks to preserve raw code (e.g., <div> becomes &lt;div&gt;)
function escapeHtmlInsideCodeBlocks(html) {
  try {
    const parser = new DOMParser()
    const doc = parser.parseFromString(html, 'text/html')
    doc.querySelectorAll('pre code').forEach((codeEl) => {
      // If code element contains any element children (e.g., spans, divs),
      // convert its innerHTML to text so tags are escaped.
      const hasElementChild = Array.from(codeEl.childNodes).some((n) => n.nodeType === 1)
      if (hasElementChild) {
        const raw = codeEl.innerHTML
        codeEl.textContent = raw
      }
    })
    return doc.body.innerHTML
  } catch (_) {
    // Fallback: return original if DOMParser is not available
    return html
  }
}
</script>

<style scoped>
.btn {
  @apply px-2.5 py-1.5 text-white/80 hover:text-white rounded border border-white/10 hover:border-white/20 bg-black/20 transition-colors text-sm;
}
.btn.active {
  @apply text-primary border-primary/40 bg-primary/10;
}

.menu-item {
  @apply text-white/80 hover:text-white text-sm text-right px-3 py-1.5 rounded hover:bg-white/10 transition-colors;
}

/* Basic TipTap content styling to align with existing .prose */
.tiptap :deep(p) {
  @apply mb-4 leading-relaxed;
}
.tiptap :deep(h2) {
  @apply text-2xl font-bold mt-8 mb-4 text-primary;
}
.tiptap :deep(h3) {
  @apply text-xl font-bold mt-6 mb-3 text-primary/90;
}
.tiptap :deep(ul) {
  @apply list-disc pr-6 mb-4;
}
.tiptap :deep(ol) {
  @apply list-decimal pr-6 mb-4;
}
.tiptap :deep(blockquote) {
  @apply pr-4 border-r-4 border-primary/40 text-white/70 italic my-4;
}
.tiptap :deep(pre) {
  @apply bg-black/60 border border-white/10 rounded-lg p-4 overflow-x-auto my-4;
}
.tiptap :deep(code) {
  @apply font-mono text-primary-light;
}
.tiptap :deep(img) {
  @apply rounded-lg my-4 max-w-full h-auto;
}
.tiptap :deep(iframe) {
  @apply rounded-lg my-4 w-full;
}

/* Task list styling */
.tiptap :deep(ul[data-type="taskList"]) {
  @apply list-none pr-0;
}
.tiptap :deep(ul[data-type="taskList"] li) {
  @apply flex items-start gap-2 mb-2;
}
.tiptap :deep(ul[data-type="taskList"] li > label) {
  @apply mt-1;
}
.tiptap :deep(ul[data-type="taskList"] input[type="checkbox"]) {
  @apply w-4 h-4 accent-primary;
}
</style>


