<template>
  <div class="block-editor-canvas rounded-lg border border-white/10 bg-black/15">
    <div class="p-4 sm:p-6 relative">
      <EditorContent :editor="editor" class="tiptap font-vazir" />

      <BubbleMenu
        v-if="editor"
        :editor="editor"
        :options="bubbleMenuOptions"
        :should-show="bubbleShouldShow"
      >
        <div class="editor-bubble-menu flex flex-wrap items-center gap-0.5 rounded-lg border border-white/15 bg-zinc-900/95 px-1 py-1 shadow-xl backdrop-blur-sm" dir="rtl">
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('bold') }" title="پررنگ" @mousedown.prevent @click="exec('toggleBold')">
            <font-awesome-icon icon="bold" />
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('italic') }" title="کج" @mousedown.prevent @click="exec('toggleItalic')">
            <font-awesome-icon icon="italic" />
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('underline') }" title="زیرخط" @mousedown.prevent @click="exec('toggleUnderline')">
            <font-awesome-icon icon="underline" />
          </button>
          <span class="editor-menu-sep" aria-hidden="true" />
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('heading', { level: 2 }) }" title="عنوان ۲" @mousedown.prevent @click="exec('toggleHeading', { level: 2 })">
            H2
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('heading', { level: 3 }) }" title="عنوان ۳" @mousedown.prevent @click="exec('toggleHeading', { level: 3 })">
            H3
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('paragraph') }" title="پاراگراف" @mousedown.prevent @click="exec('setParagraph')">
            <font-awesome-icon icon="paragraph" />
          </button>
          <span class="editor-menu-sep" aria-hidden="true" />
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('bulletList') }" title="لیست نقطه‌ای" @mousedown.prevent @click="exec('toggleBulletList')">
            <font-awesome-icon icon="list-ul" />
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('orderedList') }" title="لیست شماره‌دار" @mousedown.prevent @click="exec('toggleOrderedList')">
            <font-awesome-icon icon="list-ol" />
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('blockquote') }" title="نقل‌قول" @mousedown.prevent @click="exec('toggleBlockquote')">
            <font-awesome-icon icon="quote-right" />
          </button>
          <button type="button" class="editor-menu-btn" :class="{ active: isActive('codeBlock') }" title="بلوک کد" @mousedown.prevent @click="exec('toggleCodeBlock')">
            <font-awesome-icon icon="code" />
          </button>
          <span class="editor-menu-sep" aria-hidden="true" />
          <button type="button" class="editor-menu-btn" title="لینک" @mousedown.prevent @click="setLink">
            <font-awesome-icon icon="link" />
          </button>
          <button type="button" class="editor-menu-btn" :disabled="!isActive('link')" title="حذف لینک" @mousedown.prevent @click="unsetLink">
            <font-awesome-icon icon="unlink" />
          </button>
        </div>
      </BubbleMenu>

      <FloatingMenu
        v-if="editor"
        :editor="editor"
        :options="floatingMenuOptions"
      >
        <div class="editor-float-menu flex flex-wrap items-center gap-0.5 rounded-lg border border-white/15 bg-zinc-900/95 px-1 py-1 shadow-xl backdrop-blur-sm" dir="rtl">
          <button type="button" class="editor-menu-btn" title="عنوان ۲" @mousedown.prevent @click="insertHeading2">
            H2
          </button>
          <button type="button" class="editor-menu-btn" title="عنوان ۳" @mousedown.prevent @click="insertHeading3">
            H3
          </button>
          <span class="editor-menu-sep" aria-hidden="true" />
          <button type="button" class="editor-menu-btn" title="لیست" @mousedown.prevent @click="exec('toggleBulletList')">
            <font-awesome-icon icon="list-ul" />
          </button>
          <button type="button" class="editor-menu-btn" title="لیست شماره‌دار" @mousedown.prevent @click="exec('toggleOrderedList')">
            <font-awesome-icon icon="list-ol" />
          </button>
          <button type="button" class="editor-menu-btn" title="نقل‌قول" @mousedown.prevent @click="exec('toggleBlockquote')">
            <font-awesome-icon icon="quote-right" />
          </button>
          <button type="button" class="editor-menu-btn" title="جدول" @mousedown.prevent @click="insertTable">
            <font-awesome-icon icon="table" />
          </button>
          <span class="editor-menu-sep" aria-hidden="true" />
          <button type="button" class="editor-menu-btn" title="تصویر" @mousedown.prevent @click="triggerImageUpload">
            <font-awesome-icon icon="image" />
          </button>
          <button type="button" class="editor-menu-btn" title="یوتیوب" @mousedown.prevent @click="embedYouTube">
            <font-awesome-icon icon="video" />
          </button>
          <button type="button" class="editor-menu-btn" title="ویدیو با آدرس" @mousedown.prevent @click="embedVideoUrl">
            <font-awesome-icon icon="music" />
          </button>
        </div>
      </FloatingMenu>
    </div>

    <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="handleImageFile" />
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { Editor, EditorContent } from '@tiptap/vue-3'
import { BubbleMenu, FloatingMenu } from '@tiptap/vue-3/menus'
import StarterKit from '@tiptap/starter-kit'
import Image from '@tiptap/extension-image'
import Youtube from '@tiptap/extension-youtube'
import Placeholder from '@tiptap/extension-placeholder'
import TaskList from '@tiptap/extension-task-list'
import TaskItem from '@tiptap/extension-task-item'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'

const props = defineProps({
  modelValue: { type: String, default: '' },
  uploadUrl: { type: String, default: '' },
  authToken: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue'])

const editor = ref(null)
const fileInput = ref(null)
const isUploadingImage = ref(false)

const bubbleMenuOptions = {
  placement: 'top',
  offset: 10,
  flip: true,
  shift: true
}

const floatingMenuOptions = {
  placement: 'left-start',
  offset: 12,
  flip: true,
  shift: true
}

function bubbleShouldShow({ editor: ed, from, to }) {
  if (!ed.isEditable) return false
  if (ed.isActive('codeBlock')) return false
  if (ed.isActive('image')) return false
  return from !== to
}

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
  editor.value = new Editor({
    content: props.modelValue || '',
    extensions: [
      // Default StarterKit codeBlock (not CodeBlockLowlight): lowlight’s node views + decorations crash createView
      // on legacy HTML with <pre><code> under SES / some builds (DecorationGroup.localsInner).
      StarterKit.configure({
        link: {
          openOnClick: true,
          autolink: true,
          linkOnPaste: true
        }
      }),
      // BubbleMenu / FloatingMenu Vue components call editor.registerPlugin() — do not add duplicate extensions.
      Image.configure({ inline: false }),
      Placeholder.configure({
        placeholder:
          'برای نوشتن اینجا کلیک کنید… در خط خالی، دکمه‌های کنار خط برای افزودن بلوک؛ با انتخاب متن، ابزار قالب‌بندی ظاهر می‌شود.',
        includeChildren: false
      }),
      TaskList,
      TaskItem.configure({ nested: true }),
      // resizable columns add ProseMirror decorations that break on large setContent (e.g. loading old posts)
      Table.configure({ resizable: false }),
      TableRow,
      TableHeader,
      TableCell,
      Youtube.configure({ controls: true, nocookie: true })
    ],
    editorProps: {
      attributes: {
        dir: 'rtl',
        class: 'prose prose-invert max-w-none min-h-[18rem] focus:outline-none'
      }
    },
    onUpdate: ({ editor: ed }) => {
      const html = ed.getHTML()
      const escaped = escapeHtmlInsideCodeBlocks(html)
      emit('update:modelValue', escaped)
    }
  })
})

onBeforeUnmount(() => {
  if (editor.value) editor.value.destroy()
})

watch(
  () => props.modelValue,
  (html) => {
    const run = () => {
      const ed = editor.value
      if (!ed || ed.isDestroyed) return
      const current = ed.getHTML()
      if (html === current) return
      try {
        ed.commands.setContent(html || '', false)
      } catch (err) {
        console.warn('[BlockEditor] setContent failed:', err)
      }
    }
    void nextTick(run)
  }
)

function isActive(name, attrs) {
  return editor.value ? editor.value.isActive(name, attrs) : false
}

function exec(command, args) {
  if (!editor.value) return
  const chain = editor.value.chain().focus()
  if (args) chain[command](args).run()
  else chain[command]().run()
}

function insertHeading2() {
  exec('toggleHeading', { level: 2 })
}

function insertHeading3() {
  exec('toggleHeading', { level: 3 })
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
    const previewUrl = URL.createObjectURL(file)
    if (ed) {
      ed.chain().focus().insertContent({ type: 'image', attrs: { src: previewUrl } }).run()
    }
    const form = new FormData()
    form.append('file', file)
    const res = await fetch(props.uploadUrl || `${import.meta.env.VITE_API_BASE_URL}/admin/upload/image`, {
      method: 'POST',
      headers: {
        ...(props.authToken ? { Authorization: `Bearer ${props.authToken}` } : {})
      },
      body: form
    })
    const data = await res.json()
    if (!res.ok || !data.url) throw new Error(data.message || 'Upload failed')
    if (!ed) {
      emit('update:modelValue', (props.modelValue || '') + `\n<img src="${data.url}" alt="" />`)
      return
    }
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

function escapeHtmlInsideCodeBlocks(html) {
  try {
    const parser = new DOMParser()
    const doc = parser.parseFromString(html, 'text/html')
    doc.querySelectorAll('pre code').forEach((codeEl) => {
      const hasElementChild = Array.from(codeEl.childNodes).some((n) => n.nodeType === 1)
      if (hasElementChild) {
        const raw = codeEl.innerHTML
        codeEl.textContent = raw
      }
    })
    return doc.body.innerHTML
  } catch {
    return html
  }
}
</script>

<style scoped>
.editor-menu-btn {
  @apply px-2 py-1.5 text-white/75 hover:text-white rounded-md border border-transparent hover:border-white/15 hover:bg-white/5 transition-colors text-xs;
}
.editor-menu-btn.active {
  @apply text-primary border-primary/30 bg-primary/10;
}
.editor-menu-btn:disabled {
  @apply opacity-40 pointer-events-none;
}
.editor-menu-sep {
  @apply inline-block h-5 w-px bg-white/15 mx-0.5 shrink-0 self-center;
}

/* TipTap content */
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
  direction: ltr;
  unicode-bidi: isolate;
  text-align: left;
}
.tiptap :deep(pre code) {
  direction: ltr;
  unicode-bidi: isolate;
  text-align: left;
}
.tiptap :deep(code) {
  @apply font-mono text-primary-light;
  direction: ltr;
  unicode-bidi: isolate;
}
.tiptap :deep(img) {
  @apply rounded-lg my-4 max-w-full h-auto;
}
.tiptap :deep(iframe) {
  @apply rounded-lg my-4 w-full;
}

.tiptap :deep(ul[data-type='taskList']) {
  @apply list-none pr-0;
}
.tiptap :deep(ul[data-type='taskList'] li) {
  @apply flex items-start gap-2 mb-2;
}
.tiptap :deep(ul[data-type='taskList'] li > label) {
  @apply mt-1;
}
.tiptap :deep(ul[data-type='taskList'] input[type='checkbox']) {
  @apply w-4 h-4 accent-primary;
}
</style>
