<template>
  <div class="pt-16" dir="rtl">
    <!-- Hero Section -->
    <section class="relative py-20 bg-gradient-to-br from-gray-800 via-gray-900 to-black">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 font-vazir">
            دوره های آموزشی
          </h1>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto font-vazir">
            یادگیری برنامه‌نویسی و توسعه مهارت‌های فنی با دوره‌های جامع و کاربردی
          </p>
        </div>
      </div>
    </section>

    <!-- Courses Section -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center py-20">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-20">
          <font-awesome-icon icon="exclamation-triangle" class="h-16 w-16 text-red-500 mx-auto mb-4" />
          <h2 class="text-xl font-semibold text-white mb-2 font-vazir">خطا در بارگذاری دوره‌ها</h2>
          <p class="text-gray-400 mb-4 font-vazir">{{ error }}</p>
          <button
            @click="fetchCourses"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
          >
            تلاش مجدد
          </button>
        </div>

        <!-- Courses Grid -->
        <div v-else-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="course in courses"
            :key="course.id"
            class="bg-black/50 border border-white/10 rounded-lg overflow-hidden hover:bg-black/60 transition-all duration-300 hover:transform hover:scale-105"
          >
            <!-- Course Thumbnail -->
            <router-link :to="{ name: 'course-detail', params: { slug: course.slug || course.id } }" class="block">
              <div class="relative">
                <img
                  v-if="course.thumbnail_url"
                  :src="course.thumbnail_url"
                  :alt="course.title"
                  class="w-full h-48 object-cover"
                />
                <div
                  v-else
                  class="w-full h-48 bg-gray-700 flex items-center justify-center"
                >
                  <font-awesome-icon icon="book-open" class="h-12 w-12 text-gray-500" />
                </div>

              <!-- Course Status Badge -->
              <div class="absolute top-4 right-4">
                <span
                  :class="getStatusClasses(course.status)"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium font-vazir"
                >
                  <font-awesome-icon :icon="getStatusIcon(course.status)" class="ml-1 h-3 w-3" />
                  {{ getStatusLabel(course.status) }}
                </span>
              </div>

              <!-- Featured Badge -->
              <div v-if="course.featured" class="absolute top-4 left-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 font-vazir">
                  <font-awesome-icon icon="star" class="ml-1 h-3 w-3" />
                  ویژه
                </span>
              </div>
            </div>
            </router-link>

            <!-- Course Content -->
            <div class="p-6">
              <router-link :to="{ name: 'course-detail', params: { slug: course.slug || course.id } }">
                <h3 class="text-xl font-bold text-white mb-2 font-vazir hover:text-primary transition-colors">{{ course.title }}</h3>
              </router-link>
              <p class="text-gray-300 text-sm mb-4 line-clamp-3 font-vazir">{{ course.description }}</p>

              <!-- Course Stats -->
              <div class="flex items-center justify-between text-sm text-gray-400 mb-4">
                <span class="font-vazir">
                  <font-awesome-icon icon="clock" class="ml-1 h-3 w-3" />
                  {{ course.duration || 'نامشخص' }}
                </span>
                <span class="font-vazir">
                  <font-awesome-icon icon="users" class="ml-1 h-3 w-3" />
                  {{ course.students_count || 0 }} دانشجو
                </span>
                <span class="font-vazir">
                  <font-awesome-icon icon="file-lines" class="ml-1 h-3 w-3" />
                  {{ course.contents_count || 0 }} درس
                </span>
              </div>

              <!-- Course Level -->
              <div class="mb-4">
                <span
                  :class="getLevelClasses(course.level)"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium font-vazir"
                >
                  <font-awesome-icon :icon="getLevelIcon(course.level)" class="ml-1 h-3 w-3" />
                  {{ getLevelLabel(course.level) }}
                </span>
              </div>

              <!-- Action Button -->
              <button
                @click="viewCourse(course)"
                class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
              >
                <font-awesome-icon icon="eye" class="ml-2 h-4 w-4" />
                مشاهده دوره
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-20">
          <font-awesome-icon icon="book-open" class="h-16 w-16 text-gray-500 mx-auto mb-4" />
          <h2 class="text-xl font-semibold text-white mb-2 font-vazir">هیچ دوره‌ای یافت نشد</h2>
          <p class="text-gray-400 font-vazir">در حال حاضر دوره آموزشی فعالی وجود ندارد.</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import courseService from '@/services/courseService';

const router = useRouter();

// Reactive data
const loading = ref(true);
const error = ref(null);
const courses = ref([]);

// Methods
const fetchCourses = async () => {
  try {
    loading.value = true;
    error.value = null;

    const response = await courseService.getCourses();
    console.log('Courses API response:', response.data);

    // Handle paginated response
    courses.value = response.data.data?.data || response.data.data || [];
    console.log('Extracted courses:', courses.value);
  } catch (err) {
    console.error('Error fetching courses:', err);
    error.value = 'خطا در بارگذاری دوره‌ها';
  } finally {
    loading.value = false;
  }
};

const getStatusLabel = (status) => {
  const statusMap = {
    draft: 'پیش‌نویس',
    published: 'منتشر شده',
    archived: 'آرشیو شده'
  };
  return statusMap[status] || status;
};

const getStatusIcon = (status) => {
  const iconMap = {
    draft: 'edit',
    published: 'check-circle',
    archived: 'archive'
  };
  return iconMap[status] || 'circle';
};

const getStatusClasses = (status) => {
  const classMap = {
    draft: 'bg-yellow-500/20 text-yellow-400',
    published: 'bg-green-500/20 text-green-400',
    archived: 'bg-gray-500/20 text-gray-400'
  };
  return classMap[status] || 'bg-gray-500/20 text-gray-400';
};

const getLevelLabel = (level) => {
  const levelMap = {
    beginner: 'مبتدی',
    intermediate: 'متوسط',
    advanced: 'پیشرفته'
  };
  return levelMap[level] || level;
};

const getLevelIcon = (level) => {
  const iconMap = {
    beginner: 'seedling',
    intermediate: 'chart-line',
    advanced: 'crown'
  };
  return iconMap[level] || 'circle';
};

const getLevelClasses = (level) => {
  const classMap = {
    beginner: 'bg-green-500/20 text-green-400',
    intermediate: 'bg-blue-500/20 text-blue-400',
    advanced: 'bg-purple-500/20 text-purple-400'
  };
  return classMap[level] || 'bg-gray-500/20 text-gray-400';
};

const viewCourse = (course) => {
  router.push({ name: 'course-detail', params: { slug: course.slug || course.id } });
};

// Lifecycle
onMounted(() => {
  fetchCourses();
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 3;
}
</style>

