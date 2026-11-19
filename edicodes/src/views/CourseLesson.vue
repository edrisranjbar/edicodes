<template>
  <div class="lesson-page min-h-screen bg-gray-900 pt-24 pb-16" dir="rtl" lang="fa">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
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
        <nav aria-label="پیمایش درس‌ها" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-10">
          <RouterLink v-if="previous" :to="lessonRoute(previous)" class="p-4 rounded-xl border border-white/20 min-w-0 [overflow-wrap:anywhere]">درس قبل: {{ previous.title }}</RouterLink>
          <RouterLink v-if="next" :to="lessonRoute(next)" class="p-4 rounded-xl border border-white/20 min-w-0 [overflow-wrap:anywhere]">درس بعد: {{ next.title }}</RouterLink>
        </nav>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import courseService from '@/services/courseService';
import { sanitizeLessonHtml, handleLessonAction } from '@/utils/lessonHtml';
import '@/assets/css/pythonLesson.css';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const course = ref(null);
const lesson = ref(null);
const lessons = ref([]);
const article = ref(null);
let sequence = 0;
const originalTitle = document.title;
const index = computed(() => lessons.value.findIndex(item => item.id === lesson.value?.id));
const previous = computed(() => lessons.value[index.value - 1]);
const next = computed(() => lessons.value[index.value + 1]);
const safeHtml = computed(() => sanitizeLessonHtml(lesson.value?.content));
const lessonRoute = item => ({ name: 'course-lesson', params: { slug: route.params.slug, order: item.order } });
const onAction = event => handleLessonAction(event, article.value);

watch(() => [route.params.slug, route.params.order], async ([slug, order]) => {
  const current = ++sequence;
  loading.value = true;
  error.value = '';
  lesson.value = null;
  try {
    const response = await courseService.getPublicCourse(slug);
    if (current !== sequence) return;
    course.value = response.data.data.course;
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

onBeforeUnmount(() => { sequence++; document.title = originalTitle; });
</script>
