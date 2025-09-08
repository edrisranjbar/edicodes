<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
      <div v-if="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
      </div>

      <div v-else-if="error" class="text-center py-20">
        <font-awesome-icon icon="exclamation-triangle" class="h-16 w-16 text-red-500 mx-auto mb-4" />
        <h2 class="text-xl font-semibold text-white mb-2 font-vazir">خطا در بارگذاری دوره</h2>
        <p class="text-gray-400 mb-4 font-vazir">{{ error }}</p>
          <button
          @click="fetchCourse"
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
            >
          تلاش مجدد
          </button>
      </div>

      <div v-else-if="course" class="space-y-6">
        <!-- Course Info Card -->
        <div class="bg-black/50 border border-white/10 rounded-lg p-6">
          <div class="flex items-start space-x-6 space-x-reverse">
            <!-- Course Thumbnail -->
            <div class="flex-shrink-0">
              <img
                v-if="course.thumbnail_url"
                :src="course.thumbnail_url"
                :alt="course.title"
                class="w-24 h-24 rounded-lg object-cover"
              />
              <div
                v-else
                class="w-24 h-24 bg-gray-700 rounded-lg flex items-center justify-center"
              >
                <font-awesome-icon icon="image" class="h-8 w-8 text-gray-500" />
              </div>
            </div>

            <!-- Course Details -->
            <div class="flex-1">
              <h1 class="text-2xl font-bold text-white mb-2 font-vazir">{{ course.title }}</h1>
              <p class="text-gray-300 mb-4 font-vazir">{{ course.description }}</p>
              
              <div class="flex items-center space-x-6 space-x-reverse text-sm text-gray-400">
                <span class="font-vazir">
                  <font-awesome-icon icon="clock" class="ml-1 h-3 w-3" />
                  {{ course.duration || 'نامشخص' }}
                </span>
                <span class="font-vazir">
                  <font-awesome-icon icon="signal" class="ml-1 h-3 w-3" />
                  {{ getLevelLabel(course.level) }}
                </span>
                <span class="font-vazir">
                  <font-awesome-icon icon="users" class="ml-1 h-3 w-3" />
                  {{ course.students_count || 0 }} دانشجو
                </span>
                <span class="font-vazir">
                  <font-awesome-icon icon="file-lines" class="ml-1 h-3 w-3" />
                  {{ course.contents_count || 0 }} اپیزود
              </span>
            </div>

              <!-- Course Status -->
              <div class="mt-4">
                <span
                  :class="getStatusClasses(course.status)"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium font-vazir"
                >
                  <font-awesome-icon :icon="getStatusIcon(course.status)" class="ml-1 h-3 w-3" />
                  {{ getStatusLabel(course.status) }}
                </span>
                
                <span
                  v-if="course.featured"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 ml-1 font-vazir"
                >
                  <font-awesome-icon icon="star" class="m-1 h-3 w-3" />
                  ویژه
                </span>
              </div>
            </div>

            <!-- Course Actions -->
            <div class="flex flex-col space-y-2">
              <router-link
                :to="{ name: 'admin-courses-edit', params: { id: course.id } }"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200 font-vazir"
              >
                <font-awesome-icon icon="edit" class="ml-2 h-3 w-3" />
                ویرایش دوره
              </router-link>
              
              <button
                @click="previewCourse"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors duration-200 font-vazir"
              >
                <font-awesome-icon icon="eye" class="ml-2 h-3 w-3" />
                پیش‌نمایش
              </button>
            </div>
          </div>
        </div>

        <!-- Content Manager -->
        <CourseContentManager :course="course" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import courseService from '@/services/courseService';
import CourseContentManager from '@/components/admin/CourseContentManager.vue';

const route = useRoute();
const courseId = route.params.id;

// Reactive data
const loading = ref(true);
const error = ref(null);
const course = ref(null);

// Methods
const fetchCourse = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await courseService.getCourse(courseId);
    course.value = response.data.data;
  } catch (err) {
    console.error('Error fetching course:', err);
    error.value = 'خطا در بارگذاری اطلاعات دوره';
      } finally {
    loading.value = false;
  }
};

const getLevelLabel = (level) => {
  const levelMap = {
    beginner: 'مبتدی',
    intermediate: 'متوسط',
    advanced: 'پیشرفته'
  };
  return levelMap[level] || level;
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

const previewCourse = () => {
  // TODO: Implement course preview functionality
  alert('پیش‌نمایش دوره در حال توسعه است');
};

// Lifecycle
    onMounted(() => {
  fetchCourse();
});
</script>
