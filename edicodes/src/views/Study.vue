<template>
  <div class="study-page" lang="fa" dir="rtl" :aria-busy="loading">
    <div class="study-progress" aria-hidden="true">
      <span v-if="loading && hasContent" class="study-progress-loading" />
      <span v-else class="study-progress-reading" :style="{ transform: `scaleX(${readingRatio})` }" />
    </div>
    <div v-if="loading && !hasContent" class="study-skeleton" role="status">
      <span class="sr-only">در حال آماده‌سازی صفحهٔ مطالعه…</span>
      <div class="skeleton-hero" />
      <div class="skeleton-line" />
      <div class="skeleton-line short" />
      <div class="skeleton-card" />
    </div>
    <div v-if="error" class="study-message" role="alert">
      <h1>صفحهٔ مطالعه باز نشد</h1>
      <p>{{ error }}</p>
      <button type="button" @click="loadPage">تلاش دوباره</button>
      <RouterLink to="/study">برنامهٔ مطالعه</RouterLink>
      <RouterLink to="/">بازگشت به EdiCodes</RouterLink>
    </div>
    <div ref="host" class="study-root" :hidden="!hasContent || !!error" @click="navigate" />
    <button
      v-show="showTop && hasContent && !error"
      type="button"
      class="study-top"
      aria-label="بازگشت به بالای صفحه"
      @click="scrollToTop"
    >
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M12 19V5M5 12l7-7 7 7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" /></svg>
    </button>
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
const hasContent = ref(false)
const readingRatio = ref(0)
const showTop = ref(false)
let scrollFrame = 0
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

function placeView(firstLoad) {
  if (route.hash) scrollToSection()
  // Vue Router stores the position of an entry when leaving it; restore it once the content exists.
  else if (window.history.state?.scroll) window.scrollTo({ ...window.history.state.scroll, behavior: 'instant' })
  else if (!firstLoad) window.scrollTo({ top: 0, behavior: 'instant' })
  const heading = host.value?.querySelector('h1')
  if (heading && !firstLoad) {
    // Move screen-reader and keyboard focus to the new page instead of leaving it on the old link.
    heading.setAttribute('tabindex', '-1')
    heading.focus({ preventScroll: true })
  }
  updateScroll()
}

function updateScroll() {
  scrollFrame = 0
  const max = document.documentElement.scrollHeight - window.innerHeight
  readingRatio.value = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0
  showTop.value = window.scrollY > 900
}

function onScroll() {
  if (!scrollFrame) scrollFrame = requestAnimationFrame(updateScroll)
}

function scrollToTop() {
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' })
  const heading = host.value?.querySelector('h1')
  heading?.setAttribute('tabindex', '-1')
  heading?.focus({ preventScroll: true })
}

async function loadPage() {
  const current = ++sequence
  controller?.abort()
  controller = new AbortController()
  const signal = controller.signal
  const firstLoad = !hasContent.value
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
    // An absolute URL keeps the Vite dev server from treating this public file as a source import.
    const runtime = page.module ? await import(/* @vite-ignore */ new URL(page.module, window.location.origin).href) : null
    await styleReady
    if (current !== sequence || !host.value) return
    // The previous page stays visible until this point, so its window listeners are only removed now.
    reset()
    source = page.source
    document.title = page.title
    // Only build-generated, repository-owned HTML is loaded into this mount point.
    host.value.innerHTML = page.html
    hasContent.value = true
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
    requestAnimationFrame(() => placeView(firstLoad))
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

onMounted(() => {
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('resize', onScroll, { passive: true })
  loadPage()
})
watch(() => route.path, loadPage)
watch(() => route.hash, () => requestAnimationFrame(scrollToSection))
onBeforeUnmount(() => {
  sequence++
  controller?.abort()
  reset()
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', onScroll)
  cancelAnimationFrame(scrollFrame)
  document.title = previousTitle
})
</script>

<style scoped>
.study-progress { position: fixed; inset-block-start: 0; inset-inline: 0; height: 3px; z-index: 60; pointer-events: none; overflow: hidden; }
.study-progress span { position: absolute; inset: 0; background: #087e83; }
.study-progress-reading { transform-origin: right; transition: transform 0.08s linear; }
.study-progress-loading { width: 35%; inset-inline-end: auto !important; animation: study-loading 1s ease-in-out infinite; }
@keyframes study-loading { from { transform: translateX(100%); } to { transform: translateX(-300%); } }
.study-skeleton { max-width: 960px; padding: 64px 28px; margin-inline-start: 300px; }
.study-skeleton > div { background: linear-gradient(90deg, #e1ebef 25%, #eef4f6 50%, #e1ebef 75%); background-size: 200% 100%; animation: study-shimmer 1.4s ease-in-out infinite; border-radius: 14px; margin-bottom: 22px; }
.skeleton-hero { height: 230px; border-radius: 26px !important; }
.skeleton-line { height: 18px; width: 70%; }
.skeleton-line.short { width: 45%; }
.skeleton-card { height: 320px; border-radius: 22px !important; }
@keyframes study-shimmer { from { background-position: 100% 0; } to { background-position: -100% 0; } }
.study-top { position: fixed; inset-block-end: 24px; inset-inline-end: 24px; z-index: 40; width: 48px; height: 48px; display: grid; place-items: center; border-radius: 50%; background: #065f63; color: #fff; box-shadow: 0 8px 20px #123b4833; transition: background 0.2s, transform 0.2s; }
.study-top:hover { background: #123b48; transform: translateY(-2px); }
.study-top:focus-visible { outline: 3px solid #123b48; outline-offset: 3px; }
.sr-only { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
@media (max-width: 1100px) { .study-skeleton { margin-inline-start: 0; padding: 32px 16px; } }
@media (max-width: 600px) { .study-top { inset-block-end: 16px; inset-inline-end: 16px; } }
@media (prefers-reduced-motion: reduce) {
  .study-progress-loading, .study-skeleton > div { animation: none; }
  .study-progress-reading, .study-top { transition: none; }
}
.study-page { min-height: 100vh; background: #eff5f7; color: #152f42; }
.study-message { max-width: 660px; padding: 48px 24px; margin: auto; line-height: 2; }
.study-message h1 { font-size: 26px; font-weight: bold; margin-bottom: 20px; }
.study-message a, .study-message button { display: inline-block; color: #065f63; border: 1px solid #52677a; background: white; border-radius: 10px; padding: 10px 18px; margin: 16px 0 0 16px; }
.study-message a:focus-visible, .study-message button:focus-visible { outline: 3px solid #123b48; outline-offset: 4px; }
</style>
