<template>
  <div class="lesson-page course-reader min-h-screen pt-24 pb-16" dir="rtl" lang="fa">
    <div class="course-reader-layout">
      <aside v-if="course && lessons.length" class="course-reader-sidebar" aria-label="فهرست و پیشرفت دوره">
        <div class="course-reader-sidebar-header">
          <RouterLink :to="{ name: 'course-detail', params: { slug: route.params.slug } }" class="course-reader-name">{{ course.title }}</RouterLink>
          <div class="course-reader-progress-label"><strong>{{ fa(progress.percent) }}٪ پیشرفت</strong><span>{{ fa(progress.count) }} از {{ fa(lessons.length) }} درس</span></div>
          <div class="course-reader-progress" role="progressbar" aria-label="پیشرفت دوره" :aria-valuenow="progress.percent" :aria-valuetext="`${progress.count} درس از ${lessons.length} درس تکمیل شده`" aria-valuemin="0" aria-valuemax="100"><span :style="{ width: `${progress.percent}%` }" /></div>
          <p class="course-reader-remaining" aria-live="polite">{{ progress.remaining ? `${fa(progress.remaining)} درس باقی مانده` : 'همهٔ درس‌ها را خوانده‌اید؛ آفرین!' }}</p>
          <p class="course-reader-storage">پیشرفت در همین مرورگر ذخیره می‌شود.</p>
          <button type="button" class="course-reader-list-toggle" :aria-expanded="sidebarOpen" aria-controls="course-reader-list" @click="sidebarOpen = !sidebarOpen">{{ sidebarOpen ? 'بستن فهرست درس‌ها' : 'نمایش فهرست درس‌ها' }} <span aria-hidden="true">{{ sidebarOpen ? '−' : '+' }}</span></button>
        </div>
        <nav id="course-reader-list" ref="sidebarList" class="course-reader-list" :class="{ 'is-open': sidebarOpen }" aria-label="درس‌های دوره">
          <RouterLink v-for="item in lessons" :key="item.id" :to="lessonRoute(item)" class="course-reader-item" :class="{ 'is-current': String(item.order) === route.params.order, 'is-completed': isCompleted(item) }" :aria-current="String(item.order) === route.params.order ? 'page' : undefined" @click="sidebarOpen = false">
            <span class="course-reader-item-number" aria-hidden="true">{{ isCompleted(item) ? '✓' : fa(item.order) }}</span>
            <span class="course-reader-item-title">{{ item.title.replace(/^\d+[.\s]+/, '') }}</span>
            <span v-if="isCompleted(item)" class="sr-only">خوانده شده</span>
          </RouterLink>
        </nav>
      </aside>
      <main class="course-reader-main" :aria-busy="loading">
      <RouterLink :to="{ name: 'course-detail', params: { slug: route.params.slug } }" class="text-primary inline-block mb-8">بازگشت به دوره و فهرست درس‌ها</RouterLink>
      <p v-if="loading" role="status" class="py-16 text-gray-300">در حال بارگذاری درس…</p>
      <div v-else-if="error" role="alert" class="p-6 bg-gray-800 rounded-xl">{{ error }}</div>
      <template v-else-if="lesson">
        <p class="text-gray-400 mb-6">{{ course.title }} · درس {{ lesson.order.toLocaleString('fa-IR') }}</p>
        <article v-if="lesson.content_format === 'html'" :key="lesson.id" ref="article" class="lesson-html" v-html="safeHtml" @click="onAction" />
        <article v-else class="bg-gray-800 rounded-xl p-5 sm:p-8">
          <h1 class="text-2xl font-bold mb-8">{{ lesson.title }}</h1>
          <div class="whitespace-pre-wrap [overflow-wrap:anywhere] leading-loose">{{ lesson.content }}</div>
        </article>
        <video v-if="lesson.video_url" :src="lesson.video_url" controls class="w-full mt-8 rounded-xl" />
        <div class="course-reader-completion">
          <button type="button" :aria-pressed="isCompleted(lesson)" class="course-reader-complete-button" :class="{ 'is-completed': isCompleted(lesson) }" @click="toggleCompleted">
            {{ isCompleted(lesson) ? '✓ این درس را خوانده‌ام — لغو علامت' : 'این درس را خواندم' }}
          </button>
          <p v-if="!progressSaved" role="status">ذخیرهٔ پیشرفت در این مرورگر ممکن نیست.</p>
        </div>
        <nav aria-label="پیمایش درس‌ها" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-10">
          <RouterLink v-if="previous" :to="lessonRoute(previous)" class="p-4 rounded-xl border border-white/20 min-w-0 [overflow-wrap:anywhere]">درس قبل: {{ previous.title }}</RouterLink>
          <RouterLink v-if="next" :to="lessonRoute(next)" class="p-4 rounded-xl border border-white/20 min-w-0 [overflow-wrap:anywhere]">درس بعد: {{ next.title }}</RouterLink>
        </nav>
      </template>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, nextTick, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import courseService from '@/services/courseService';
import { sanitizeLessonHtml, handleLessonAction } from '@/utils/lessonHtml';
import { courseProgressKey, readCourseProgress, writeCourseProgress, summarizeCourseProgress } from '@/utils/courseProgress';
import '@/assets/css/pythonLesson.css';
import '@/assets/css/courseReader.css';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const course = ref(null);
const lesson = ref(null);
const lessons = ref([]);
const article = ref(null);
const completed = ref([]);
const sidebarOpen = ref(false);
const sidebarList = ref(null);
const progressSaved = ref(true);
const progressStorage = { getItem: key => window.localStorage.getItem(key), setItem: (key, value) => window.localStorage.setItem(key, value) };
const progress = computed(() => summarizeCourseProgress(lessons.value, completed.value));
const fa = value => value.toLocaleString('fa-IR');
const isCompleted = item => completed.value.includes(String(item.id));
function toggleCompleted() {
  const id = String(lesson.value.id);
  completed.value = isCompleted(lesson.value) ? completed.value.filter(value => value !== id) : [...completed.value, id];
  progressSaved.value = writeCourseProgress(progressStorage, course.value.id, completed.value);
}
function syncProgress(event) {
  if (course.value && (event.key === null || event.key === courseProgressKey(course.value.id))) {
    completed.value = readCourseProgress(progressStorage, course.value.id);
  }
}
window.addEventListener('storage', syncProgress);
let sequence = 0;
const originalTitle = document.title;
const index = computed(() => lessons.value.findIndex(item => item.id === lesson.value?.id));
const previous = computed(() => lessons.value[index.value - 1]);
const next = computed(() => lessons.value[index.value + 1]);
const safeHtml = computed(() => sanitizeLessonHtml(lesson.value?.content));
const lessonRoute = item => ({ name: 'course-lesson', params: { slug: route.params.slug, order: item.order } });
const onAction = event => handleLessonAction(event, article.value);

watch([() => route.params.order, sidebarOpen, () => lessons.value.length], async () => {
  await nextTick();
  const list = sidebarList.value;
  const active = list?.querySelector('[aria-current="page"]');
  if (!active || !list.clientHeight) return;
  list.scrollTop += active.getBoundingClientRect().top - list.getBoundingClientRect().top - (list.clientHeight - active.offsetHeight) / 2;
});

watch(() => [route.params.slug, route.params.order], async ([slug, order]) => {
  const current = ++sequence;
  loading.value = true;
  error.value = '';
  lesson.value = null;
  sidebarOpen.value = false;
  if (course.value?.slug !== slug) { course.value = null; lessons.value = []; completed.value = []; }
  try {
    const response = await courseService.getPublicCourse(slug);
    if (current !== sequence) return;
    course.value = response.data.data.course;
    completed.value = readCourseProgress(progressStorage, course.value.id);
    const contents = await courseService.getPublicCourseContents(course.value.id);
    if (current !== sequence) return;
    lessons.value = [...contents.data.data].sort((a, b) => a.order - b.order);
    let selected = lessons.value.find(item => String(item.order) === order);
    if (!selected) throw new Error('not-found');
    if (!selected.is_free) {
      const authorized = await courseService.getAccessibleLesson(course.value.id, selected.id);
      if (current !== sequence) return;
      selected = authorized.data.data;
    }
    lesson.value = selected;
    document.title = `${selected.title} | ${course.value.title}`;
  } catch (failure) {
    if (current === sequence) error.value = [401, 403].includes(failure.response?.status)
      ? 'برای دسترسی به این درس، وارد حساب کاربری خود شوید و در دوره ثبت نام کنید.'
      : 'درس پیدا نشد یا بارگذاری آن ممکن نیست. دوباره تلاش کنید.';
  } finally {
    if (current === sequence) loading.value = false;
  }
}, { immediate: true });

onBeforeUnmount(() => { sequence++; document.title = originalTitle; window.removeEventListener('storage', syncProgress); });
</script>
