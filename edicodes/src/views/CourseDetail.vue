<template>
  <div class="min-h-screen bg-gray-900 pt-16" dir="rtl">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <!-- Course Detail -->
    <div v-else-if="course" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <!-- Back Button -->
      <div class="mb-8">
        <router-link
          to="/courses"
          class="inline-flex items-center text-gray-400 hover:text-white transition-colors font-vazir"
        >
          <font-awesome-icon icon="arrow-right" class="ml-2 h-4 w-4" />
          بازگشت به دوره‌ها
        </router-link>
      </div>

      <!-- Course Header -->
      <div class="bg-black/50 border border-white/10 rounded-lg overflow-hidden mb-8">
        <div class="md:flex">
          <!-- Course Thumbnail -->
          <div class="md:w-1/3">
            <img
              v-if="course.thumbnail_url"
              :src="course.thumbnail_url"
              :alt="course.title"
              class="w-full h-64 md:h-full object-cover"
            />
                <div
                  v-else
                  class="w-full h-64 md:h-full bg-gray-700 flex items-center justify-center"
                >
                  <font-awesome-icon icon="book" class="h-16 w-16 text-gray-500" />
                </div>
          </div>

          <!-- Course Info -->
          <div class="md:w-2/3 min-w-0 p-5 sm:p-8">
            <div class="flex flex-col sm:flex-row items-start justify-between gap-4 mb-4">
              <div class="min-w-0 [overflow-wrap:anywhere]">
                <h1 class="text-3xl font-bold text-white mb-2 font-vazir">{{ course.title }}</h1>

                <!-- Course Status and Featured Badges -->
                <div class="flex items-center gap-2 mb-4">
                  <span
                    :class="getStatusClasses(course.status)"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium font-vazir"
                  >
                    <font-awesome-icon :icon="getStatusIcon(course.status)" class="ml-1 h-3 w-3" />
                    {{ getStatusLabel(course.status) }}
                  </span>

                  <span v-if="course.featured" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400 font-vazir">
                    <font-awesome-icon icon="star" class="ml-1 h-3 w-3" />
                    ویژه
                  </span>

                  <span
                    :class="getLevelClasses(course.level)"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium font-vazir"
                  >
                    <font-awesome-icon :icon="getLevelIcon(course.level)" class="ml-1 h-3 w-3" />
                    {{ getLevelLabel(course.level) }}
                  </span>
                </div>
              </div>

              <!-- Price -->
              <div class="text-left">
                <div class="text-2xl font-bold text-primary font-vazir">
                  {{ formatPrice(course.price) }}
                </div>
              </div>
            </div>

            <!-- Course Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ course.students_count || 0 }}</div>
                <div class="text-sm text-gray-400 font-vazir">دانشجو</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ course.contents_count ?? courseContents.length }}</div>
                <div class="text-sm text-gray-400 font-vazir">درس</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-white">{{ course.duration || 'نامشخص' }}</div>
                <div class="text-sm text-gray-400 font-vazir">مدت زمان</div>
              </div>
              <div class="text-center">
                <div class="text-2xl font-bold text-white">
                  <font-awesome-icon :icon="getLevelIcon(course.level)" class="h-6 w-6" />
                </div>
                <div class="text-sm text-gray-400 font-vazir">سطح</div>
              </div>
            </div>

            <!-- Enroll Button -->
            <div v-if="!isEnrolled" class="w-full md:w-auto">
              <button
                @click="enrollInCourse"
                :disabled="enrollmentLoading"
                class="w-full px-8 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 font-vazir font-medium"
              >
                <span v-if="enrollmentLoading" class="inline-flex items-center">
                  <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white ml-2"></div>
                  در حال ثبت نام...
                </span>
                <span v-else class="inline-flex items-center">
                  <font-awesome-icon icon="user" class="ml-2 h-4 w-4" />
                  ثبت نام در دوره
                </span>
              </button>
            </div>

            <!-- Enrolled Status -->
            <div v-else class="w-full md:w-auto">
              <div class="px-8 py-3 bg-green-500/20 text-green-400 rounded-lg font-vazir font-medium text-center">
                <font-awesome-icon icon="check-circle" class="ml-2 h-4 w-4" />
                ثبت نام شده
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Description -->
      <div class="bg-black/50 border border-white/10 rounded-lg p-4 sm:p-8 mb-8">
        <h2 class="text-2xl font-bold text-white mb-6 font-vazir">توضیحات دوره</h2>
        <div class="prose prose-invert max-w-none">
          <p class="text-gray-300 font-vazir leading-relaxed">{{ course.description }}</p>
        </div>
      </div>

      <!-- Course Contents -->
      <div class="bg-black/50 border border-white/10 rounded-lg p-4 sm:p-8">
        <h2 class="text-2xl font-bold text-white mb-6 font-vazir">محتوای دوره</h2>

        <!-- Contents List -->
        <div v-if="courseContents.length > 0" class="space-y-4">
          <div
            v-for="content in courseContents"
            :key="content.id"
            class="bg-black/30 border border-white/10 rounded-lg p-4 hover:bg-black/40 transition-all duration-200"
          >
            <div class="grid grid-cols-[auto_minmax(0,1fr)] sm:grid-cols-[auto_minmax(0,1fr)_auto] items-start gap-4">
              <!-- Content Type Icon -->
              <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-lg bg-primary/20 flex items-center justify-center">
                  <font-awesome-icon
                    :icon="getContentTypeIcon(content)"
                    class="h-6 w-6 text-primary"
                  />
                </div>
              </div>

              <!-- Content Info -->
              <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                  <h3 class="min-w-0 text-lg font-medium text-white font-vazir [overflow-wrap:anywhere]">{{ content.title }}</h3>
                  <span
                    v-if="content.is_free"
                    class="px-2 py-1 bg-green-500/20 text-green-400 text-xs rounded-full font-vazir"
                  >
                    رایگان
                  </span>
                </div>

                <!-- Content description -->
                <p v-if="content.content" class="text-gray-300 text-sm font-vazir line-clamp-2 [overflow-wrap:anywhere]">
                  {{ content.content }}
                </p>
              </div>

              <!-- Play Button -->
              <div class="col-span-2 sm:col-span-1 min-w-0">
                <button
                  @click="viewContent(content)"
                  :disabled="contentLoading"
                  :class="[
                    'w-full sm:w-auto px-4 py-2 rounded-lg transition-colors duration-200 font-vazir text-sm',
                    (isEnrolled || content.is_free)
                      ? 'bg-primary text-white hover:bg-primary/90'
                      : 'bg-gray-500 text-gray-300 cursor-not-allowed'
                  ]"
                >
                  <span v-if="contentLoading" class="inline-flex items-center">
                    <div class="animate-spin rounded-full h-3 w-3 border-b-2 border-current ml-1"></div>
                    بارگذاری...
                  </span>
                  <span v-else class="inline-flex items-center">
                    <font-awesome-icon icon="eye" class="ml-1 h-3 w-3" />
                    مشاهده محتوا
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <font-awesome-icon icon="book" class="h-16 w-16 text-gray-500 mx-auto mb-4" />
          <h3 class="text-xl font-medium text-white mb-2 font-vazir">هیچ محتوایی یافت نشد</h3>
          <p class="text-gray-400 font-vazir">این دوره هنوز محتوایی ندارد.</p>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else class="text-center py-20">
      <font-awesome-icon icon="exclamation-triangle" class="h-16 w-16 text-red-500 mx-auto mb-4" />
      <h2 class="text-xl font-semibold text-white mb-2 font-vazir">دوره یافت نشد</h2>
      <p class="text-gray-400 mb-4 font-vazir">دوره مورد نظر وجود ندارد یا حذف شده است.</p>
      <router-link
        to="/courses"
        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
      >
        بازگشت به دوره‌ها
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import courseService from '@/services/courseService';
import enrollmentService from '@/services/enrollmentService';

const route = useRoute();
const router = useRouter();

// Reactive data
const loading = ref(true);
const course = ref(null);
const courseContents = ref([]);
const isEnrolled = ref(false);
const enrollmentLoading = ref(false);
const contentLoading = ref(false);

// Methods
const fetchCourse = async () => {
  try {
    loading.value = true;
    const slug = route.params.slug;

    const response = await courseService.getPublicCourse(slug);
    course.value = response.data.data?.course || null;

    // If course is found, fetch its contents and check enrollment status
    if (course.value) {
      await fetchCourseContents(course.value.id);
      await checkEnrollmentStatus(course.value.id);
    }
  } catch (error) {
    console.error('Error fetching course:', error);
    course.value = null;
  } finally {
    loading.value = false;
  }
};

const fetchCourseContents = async (courseId) => {
  try {
    const response = await courseService.getPublicCourseContents(courseId);
    courseContents.value = response.data.data || [];
  } catch (error) {
    console.error('Error fetching course contents:', error);
    courseContents.value = [];
  }
};

const checkEnrollmentStatus = async (courseId) => {
  try {
    // Check if user has a token (admin or user)
    const adminToken = localStorage.getItem('admin_token');
    const userToken = localStorage.getItem('user_token');

    if (!adminToken && !userToken) {
      isEnrolled.value = false;
      return;
    }

    // If user is admin, they have access to all content
    if (adminToken && !userToken) {
      isEnrolled.value = true;
      return;
    }

    const response = await enrollmentService.checkEnrollmentStatus(courseId);
    isEnrolled.value = response.data.data?.is_enrolled || false;
  } catch (error) {
    console.error('Error checking enrollment status:', error);
    // For now, set to false on error (user not enrolled)
    isEnrolled.value = false;
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
    advanced: 'star'
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

const formatPrice = (price) => {
  if (!price || price === 0) return 'رایگان';
  return new Intl.NumberFormat('fa-IR').format(price) + ' تومان';
};

const getContentTypeIcon = (content) => {
  // Determine content type based on available fields
  if (content.video_path) {
    return 'video';
  } else if (content.file_path) {
    return 'file';
  } else {
    return 'file-lines'; // Default to text for content without video/file
  }
};

const enrollInCourse = async () => {
  if (!course.value) return;

  // Check if user is authenticated
  const adminToken = localStorage.getItem('admin_token');
  const userToken = localStorage.getItem('user_token');

  if (!adminToken && !userToken) {
    alert('لطفا ابتدا وارد حساب کاربری خود شوید.');
    // TODO: Redirect to login page
    return;
  }

  // Check if logged-in user is an admin
  if (adminToken && !userToken) {
    // Admin user - they have access to all content
    isEnrolled.value = true;
    alert(`👨‍💼 به عنوان مدیر، شما به تمام محتوای دوره "${course.value.title}" دسترسی دارید.`);
    return;
  }

  try {
    enrollmentLoading.value = true;
    const response = await enrollmentService.enrollInCourse(course.value.id);

    if (response.data.success) {
      isEnrolled.value = true;
      alert(`✅ ثبت نام در دوره "${course.value.title}" با موفقیت انجام شد!`);

      // If course is free, user can access content immediately
      if (course.value.price === 0) {
        alert('🎉 دوره رایگان است! اکنون می‌توانید به محتوای دوره دسترسی داشته باشید.');
      } else {
        // For paid courses, redirect to payment
        alert('💳 لطفا مبلغ دوره را پرداخت کنید تا به محتوای دوره دسترسی پیدا کنید.');
        // TODO: Redirect to payment page
      }
    }
  } catch (error) {
    console.error('Error enrolling in course:', error);

    if (error.response?.status === 401) {
      alert('لطفا ابتدا وارد حساب کاربری خود شوید.');
      // TODO: Redirect to login page
    } else if (error.response?.status === 400) {
      alert(error.response.data.message || 'خطا در ثبت نام دوره');
    } else {
      alert('خطا در ثبت نام دوره. لطفا دوباره امتحان کنید.');
    }
  } finally {
    enrollmentLoading.value = false;
  }
};

const viewContent = async (content) => {
  if (!course.value) return;

  // Check if user is authenticated
  const adminToken = localStorage.getItem('admin_token');
  const userToken = localStorage.getItem('user_token');

  if (!adminToken && !userToken && !content.is_free) {
    alert('لطفا ابتدا وارد حساب کاربری خود شوید.');
    return;
  }

  // Check if user is admin (admins have access to all content)
  const isAdmin = adminToken && !userToken;

  // Check if user is enrolled or if content is free (admins bypass this check)
  if (!isAdmin && !isEnrolled.value && !content.is_free) {
    alert('برای دسترسی به این محتوا، ابتدا باید در دوره ثبت نام کنید.');
    return;
  }

  try {
    contentLoading.value = true;

    // Determine content type based on available fields
    if (content.video_path) {
      // Video content
      const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000').replace('/api', '');
      const videoUrl = `${baseUrl}/storage/${content.video_path}`;
      window.open(videoUrl, '_blank');
    } else if (content.file_path) {
      // File content
      const baseUrl = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000').replace('/api', '');
      const fileUrl = `${baseUrl}/storage/${content.file_path}`;
      window.open(fileUrl, '_blank');
    } else if (content.content) {
      // Text content
      showTextContentModal(content);
    } else {
      // No content available
      alert(`محتوای "${content.title}" هنوز آماده نیست. لطفا بعدا امتحان کنید.`);
    }
  } catch (error) {
    console.error('Error accessing content:', error);
    alert('خطا در دسترسی به محتوا. لطفا دوباره امتحان کنید.');
  } finally {
    contentLoading.value = false;
  }
};

const showTextContentModal = (content) => {
  // Create a modal-like overlay for text content
  const modal = document.createElement('div');
  modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
  modal.innerHTML = `
    <div class="bg-gray-800 rounded-lg min-w-0 max-w-2xl w-full max-h-[80vh] overflow-hidden" dir="rtl">
      <div class="p-6 border-b border-gray-700">
        <div class="flex items-center justify-between gap-3">
          <h3 class="min-w-0 text-xl font-bold text-white font-vazir [overflow-wrap:anywhere]" data-lesson-title></h3>
          <button onclick="this.closest('.fixed').remove()" class="shrink-0 text-gray-400 hover:text-white" aria-label="بستن">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="prose prose-invert max-w-none">
          <div class="min-w-0 text-gray-300 font-vazir leading-relaxed whitespace-pre-wrap [overflow-wrap:anywhere]" data-lesson-text></div>
        </div>
      </div>
      <div class="p-6 border-t border-gray-700">
        <button onclick="this.closest('.fixed').remove()" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir">
          بستن
        </button>
      </div>
    </div>
  `;

  modal.querySelector('[data-lesson-title]').textContent = content.title;
  const body = modal.querySelector('[data-lesson-text]');
  const text = content.content || 'محتوای متنی در دست توسعه است.';
  let offset = 0;
  for (const match of text.matchAll(/https:\/\/[^\s<>"']+/g)) {
    body.appendChild(document.createTextNode(text.slice(offset, match.index)));
    const link = document.createElement('a');
    link.href = match[0];
    link.textContent = match[0];
    link.target = '_blank';
    link.rel = 'noopener noreferrer';
    link.className = 'text-primary underline break-all';
    link.dir = 'ltr';
    body.appendChild(link);
    offset = match.index + match[0].length;
  }
  body.appendChild(document.createTextNode(text.slice(offset)));
  
  document.body.appendChild(modal);
  
  // Close modal when clicking outside
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.remove();
    }
  });
};

// Lifecycle
onMounted(() => {
  fetchCourse();
});
</script>

<style scoped>
.prose-invert {
  color: rgb(209 213 219);
}
</style>
