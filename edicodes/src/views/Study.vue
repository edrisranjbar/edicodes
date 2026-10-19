<template>
  <div class="study-page" lang="fa" dir="rtl">
    <div v-if="loading" class="study-message" role="status">در حال آماده‌سازی صفحهٔ مطالعه…</div>
    <div v-if="error" class="study-message" role="alert">
      <h1>صفحهٔ مطالعه باز نشد</h1>
      <p>{{ error }}</p>
      <button type="button" @click="loadPage">تلاش دوباره</button>
      <RouterLink to="/study">برنامهٔ مطالعه</RouterLink>
      <RouterLink to="/">بازگشت به EdiCodes</RouterLink>
    </div>
    <div ref="host" class="study-root" :hidden="loading || !!error" @click="navigate" />
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { normalizeStudyPath, resolveStudyLink } from '@/utils/studyNavigation.js'
defineOptions({ name: 'StudyPage' })

const route = useRoute()
const router = useRouter()
const host = ref(null)
const loading = ref(true)
const error = ref('')
let manifestPromise
let sequence = 0
let controller
let source = 'index.html'
let cleanup = []
const previousTitle = document.title
const storage = { getItem: key => window.localStorage.getItem(key), setItem: (key, value) => window.localStorage.setItem(key, value) }

async function getManifest() {
  if (!manifestPromise) manifestPromise = fetch('/study-content/manifest.json', { cache: 'no-cache' }).then(async response => {
    if (!response.ok) throw new Error('manifest')
    const value = await response.json()
    if (value.version !== 1 || !value.pages) throw new Error('manifest')
    return value
  }).catch(reason => { manifestPromise = null; throw reason })
  return manifestPromise
}

function reset() {
  cleanup.forEach(fn => fn())
  cleanup = []
}

function normalizeLinks() {
  host.value?.querySelectorAll('a[href]').forEach(anchor => {
    const destination = resolveStudyLink(anchor.getAttribute('href'), source, window.location.origin)
    if (destination) anchor.setAttribute('href', destination)
  })
}

function scrollToSection() {
  if (!route.hash || !host.value) return
  let id
  try { id = decodeURIComponent(route.hash.slice(1)) } catch { return }
  const element = [...host.value.querySelectorAll('[id]')].find(node => node.id === id)
  element?.scrollIntoView({ block: 'start' })
}

async function loadPage() {
  const current = ++sequence
  controller?.abort()
  controller = new AbortController()
  const signal = controller.signal
  reset()
  loading.value = true
  error.value = ''
  try {
    const manifest = await getManifest()
    if (current !== sequence) return
    const pageUrl = manifest.pages[normalizeStudyPath(route.path)]
    if (!pageUrl) {
      error.value = 'این درس هنوز در برنامهٔ منتشرشده وجود ندارد. از فهرست فصل‌ها یا برنامهٔ مطالعه وارد شو.'
      return
    }
    let stylesheet = document.querySelector('link[data-study-styles]')
    if (!stylesheet) {
      stylesheet = document.createElement('link')
      stylesheet.rel = 'stylesheet'
      stylesheet.dataset.studyStyles = ''
      document.head.appendChild(stylesheet)
    }
    const styleReady = new Promise((resolve, reject) => {
      if (stylesheet.getAttribute('href') === manifest.stylesheet && stylesheet.sheet) return resolve()
      stylesheet.onload = resolve
      stylesheet.onerror = () => reject(new Error('stylesheet'))
      stylesheet.href = manifest.stylesheet
    })
    const response = await fetch(pageUrl, { signal })
    if (!response.ok) throw new Error('page')
    const page = await response.json()
    const runtime = page.module ? await import(/* @vite-ignore */ page.module) : null
    await styleReady
    if (current !== sequence || !host.value) return
    source = page.source
    document.title = page.title
    // Only build-generated, repository-owned HTML is loaded into this mount point.
    host.value.innerHTML = page.html
    const scopedDocument = { querySelector: selector => host.value.querySelector(selector), querySelectorAll: selector => host.value.querySelectorAll(selector) }
    const scopedWindow = {
      addEventListener(name, callback, options) {
        const listener = (...args) => { callback(...args); normalizeLinks() }
        window.addEventListener(name, listener, options)
        cleanup.push(() => window.removeEventListener(name, listener, options))
      },
    }
    runtime?.mount(scopedDocument, scopedWindow, storage, message => window.confirm(message))
    normalizeLinks()
    requestAnimationFrame(scrollToSection)
  } catch (reason) {
    if (reason.name !== 'AbortError' && current === sequence) error.value = 'دریافت محتوای درس ممکن نشد. اتصال را بررسی کن و دوباره تلاش کن.'
  } finally {
    if (current === sequence) loading.value = false
  }
}

function navigate(event) {
  if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return
  const anchor = event.target.closest?.('a[href]')
  if (!anchor || !host.value.contains(anchor) || anchor.hasAttribute('download') || anchor.target === '_blank') return
  const destination = resolveStudyLink(anchor.getAttribute('href'), source, window.location.origin)
  if (!destination) return
  event.preventDefault()
  router.push(destination)
}

onMounted(loadPage)
watch(() => route.path, loadPage)
watch(() => route.hash, () => requestAnimationFrame(scrollToSection))
onBeforeUnmount(() => {
  sequence++
  controller?.abort()
  reset()
  document.title = previousTitle
})
</script>

<style scoped>
.study-page { min-height: 100vh; background: #eff5f7; color: #152f42; }
.study-message { max-width: 660px; padding: 48px 24px; margin: auto; line-height: 2; }
.study-message h1 { font-size: 26px; font-weight: bold; margin-bottom: 20px; }
.study-message a, .study-message button { display: inline-block; color: #065f63; border: 1px solid #52677a; background: white; border-radius: 10px; padding: 10px 18px; margin: 16px 0 0 16px; }
.study-message a:focus-visible, .study-message button:focus-visible { outline: 3px solid #123b48; outline-offset: 4px; }
</style>
