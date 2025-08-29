<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white font-vazir">مدیریت دوره‌ها</h1>
        <p class="text-gray-400 mt-1">مدیریت و نظارت بر تمامی دوره‌های آموزشی</p>
      </div>
      <div class="mt-4 sm:mt-0">
        <router-link
          to="/admin/courses/create"
          class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200 font-vazir"
        >
          <font-awesome-icon icon="plus" class="ml-2 h-4 w-4" />
          ایجاد دوره جدید
        </router-link>
      </div>
    </div>

    <!-- Stats Cards -->
    <!-- Note: Total Revenue shows actual income from completed course sales, not course prices -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="graduation-cap" class="h-8 w-8 text-primary" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">کل دوره‌ها</p>
            <p class="text-2xl font-bold text-white">{{ stats.totalCourses || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="eye" class="h-8 w-8 text-green-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">دوره‌های منتشر شده</p>
            <p class="text-2xl font-bold text-white">{{ stats.publishedCourses || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="users" class="h-8 w-8 text-blue-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">کل دانشجویان</p>
            <p class="text-2xl font-bold text-white">{{ stats.totalStudents || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="money-bill-wave" class="h-8 w-8 text-yellow-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">درآمد کل از فروش</p>
            <p v-if="statsLoading" class="text-2xl font-bold text-white">
              <div class="inline-flex items-center">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-yellow-400 ml-2"></div>
                در حال بارگذاری...
              </div>
            </p>
            <p v-else class="text-2xl font-bold text-white">{{ formatPrice(stats.totalRevenue || 0) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-black/50 border border-white/10 rounded-lg p-6">
      <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="جستجو در دوره‌ها..."
            class="w-full px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          />
        </div>
        <div class="flex gap-2">
          <select
            v-model="statusFilter"
            class="px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          >
            <option value="">همه وضعیت‌ها</option>
            <option value="published">منتشر شده</option>
            <option value="draft">پیش‌نویس</option>
            <option value="archived">آرشیو شده</option>
          </select>
          <select
            v-model="levelFilter"
            class="px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          >
            <option value="">همه سطوح</option>
            <option value="beginner">مبتدی</option>
            <option value="intermediate">متوسط</option>
            <option value="advanced">پیشرفته</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Courses Table -->
    <!-- Note: Total Revenue shows actual income from completed course sales, not course prices -->
    <div v-if="canRenderTable && filteredCourses.length > 0" class="bg-black/50 border border-white/10 rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10">
          <thead class="bg-black/30">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دوره
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دسته‌بندی
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                سطح
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                قیمت
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                وضعیت
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دانشجویان
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                عملیات
              </th>
            </tr>
          </thead>
          <tbody class="bg-black/20 divide-y divide-white/10">
            <tr v-for="(course, index) in filteredCourses" :key="course.id || index" class="hover:bg-white/5">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12">
                    <img
                      v-if="course.thumbnail"
                      :src="course.thumbnail"
                      :alt="course.title || 'Course thumbnail'"
                      class="h-12 w-12 rounded-lg object-cover"
                    />
                    <div
                      v-else
                      class="h-12 w-12 rounded-lg bg-gray-600 flex items-center justify-center"
                    >
                      <font-awesome-icon icon="graduation-cap" class="h-6 w-6 text-gray-400" />
                    </div>
                  </div>
                  <div class="mr-4">
                    <div class="text-sm font-medium text-white font-vazir">{{ course.title || 'بدون عنوان' }}</div>
                    <div class="text-sm text-gray-400 font-vazir">{{ course.short_description || 'بدون توضیح' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="course.category && course.category.name" class="text-sm text-gray-300 font-vazir">
                  {{ course.category.name }}
                </span>
                <span v-else class="text-sm text-gray-500 font-vazir">بدون دسته</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-green-100 text-green-800': course.level === 'beginner',
                    'bg-yellow-100 text-yellow-800': course.level === 'intermediate',
                    'bg-red-100 text-red-800': course.level === 'advanced'
                  }"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full font-vazir"
                >
                  {{ getLevelText(course.level) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="course.price > 0" class="text-sm text-gray-300 font-vazir">
                  {{ formatPrice(course.price) }}
                </span>
                <span v-else class="text-sm text-green-400 font-vazir">رایگان</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-green-100 text-green-800': course.status === 'published',
                    'bg-yellow-100 text-yellow-800': course.status === 'draft',
                    'bg-gray-100 text-gray-800': course.status === 'archived'
                  }"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full font-vazir"
                >
                  {{ getStatusText(course.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-vazir">
                {{ course.students_count || 0 }} نفر
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2 space-x-reverse">
                  <router-link
                    :to="`/admin/courses/edit/${course.id}`"
                    class="text-primary hover:text-primary/80 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="edit" class="h-4 w-4" />
                  </router-link>
                  <router-link
                    :to="`/admin/courses/${course.id}/contents`"
                    class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="list" class="h-4 w-4" />
                  </router-link>
                  <button
                    @click="deleteCourse(course.id)"
                    class="text-red-400 hover:text-red-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="trash" class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="isReady && !loading && Array.isArray(filteredCourses) && pagination.total > pagination.per_page" class="px-6 py-4 bg-black/30 border-t border-white/10">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-400 font-vazir">
            نمایش {{ (pagination.current_page - 1) * pagination.per_page + 1 }} تا {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} از {{ pagination.total }} دوره
          </div>
          <div class="flex items-center space-x-2 space-x-reverse">
            <button
              v-if="pagination.current_page > 1"
              @click="changePage(pagination.current_page - 1)"
              class="px-3 py-2 text-sm text-gray-400 hover:text-white border border-white/20 rounded-lg hover:bg-white/10 transition-colors duration-200 font-vazir"
            >
              قبلی
            </button>
            <span class="px-3 py-2 text-sm text-white font-vazir">
              صفحه {{ pagination.current_page }} از {{ pagination.last_page }}
            </span>
            <button
              v-if="pagination.current_page < pagination.last_page"
              @click="changePage(pagination.current_page + 1)"
              class="px-3 py-2 text-sm text-gray-400 hover:text-white border border-white/20 rounded-lg hover:bg-white/10 transition-colors duration-200 font-vazir"
            >
              بعدی
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="isReady && !loading && Array.isArray(filteredCourses) && filteredCourses.length === 0" class="text-center py-12">
        <font-awesome-icon icon="graduation-cap" class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-400 font-vazir">دوره‌ای یافت نشد</h3>
        <p class="mt-1 text-sm text-gray-500 font-vazir">
          {{ searchQuery || statusFilter || levelFilter ? 'فیلترهای اعمال شده را تغییر دهید' : 'هنوز دوره‌ای ایجاد نشده است' }}
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="!isReady || loading" class="text-center py-12">
      <div class="inline-flex items-center px-4 py-2 text-sm text-gray-400">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary ml-2"></div>
        {{ loading ? 'در حال بارگذاری...' : 'در حال آماده‌سازی...' }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import courseService from '@/services/courseService';
import enrollmentService from '@/services/enrollmentService';
import { formatMoney } from '@/utils/moneyFormatter';

const router = useRouter();
const loading = ref(false);
const statsLoading = ref(false);
const courses = ref([]);
const stats = ref({});
const searchQuery = ref('');
const statusFilter = ref('');
const levelFilter = ref('');
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
});
const isReady = ref(false);

// Fetch courses on component mount
onMounted(async () => {
  await fetchCourses();
});

const fetchCourses = async () => {
  try {
    loading.value = true;
    const response = await courseService.getCourses();
    console.log('API Response:', response);
    console.log('Response data:', response.data);
    // Handle paginated response structure
    if (response.data.data && response.data.data.data) {
      courses.value = response.data.data.data; // Paginated response
      // Extract pagination info
      pagination.value = {
        current_page: response.data.data.current_page || 1,
        last_page: response.data.data.last_page || 1,
        per_page: response.data.data.per_page || 10,
        total: response.data.data.total || 0
      };
    } else if (response.data.data) {
      courses.value = response.data.data; // Direct array response
      pagination.value = {
        current_page: 1,
        last_page: 1,
        per_page: courses.value.length,
        total: courses.value.length
      };
    } else {
      courses.value = [];
      pagination.value = {
        current_page: 1,
        last_page: 1,
        per_page: 0,
        total: 0
      };
    }
    console.log('Courses value:', courses.value);
    console.log('Pagination:', pagination.value);
    
    // Fetch stats after courses are loaded
    await fetchStats();
    
    // Mark component as ready
    isReady.value = true;
  } catch (error) {
    console.error('Error fetching courses:', error);
    courses.value = []; // Ensure it's always an array
    // Handle error (show notification, etc.)
  } finally {
    loading.value = false;
    isReady.value = true;
  }
};

const fetchStats = async () => {
  try {
    statsLoading.value = true;
    
    // Ensure courses.value is an array before processing
    if (!Array.isArray(courses.value)) {
      courses.value = [];
    }
    
    // Get course-related stats
    const totalCourses = pagination.value.total || courses.value.length;
    const publishedCourses = courses.value.filter(c => c && c.status === 'published').length;
    const totalStudents = courses.value.reduce((sum, c) => sum + (c?.students_count || 0), 0);
    
    // Get real revenue data from enrollments instead of course prices
    // This fetches the actual income earned from completed course sales
    let totalRevenue = 0;
    try {
      const enrollmentStats = await enrollmentService.getStatistics();
      if (enrollmentStats.data && enrollmentStats.data.data) {
        totalRevenue = enrollmentStats.data.data.total_revenue || 0;
      }
    } catch (enrollmentError) {
      console.warn('Could not fetch enrollment stats, using fallback:', enrollmentError);
      // Fallback: calculate from completed enrollments if available in courses data
      totalRevenue = courses.value.reduce((sum, c) => sum + (c?.revenue || 0), 0);
    }
    
    stats.value = {
      totalCourses,
      publishedCourses,
      totalStudents,
      totalRevenue
    };
  } catch (error) {
    console.error('Error fetching stats:', error);
    // Set default stats on error
    stats.value = {
      totalCourses: 0,
      publishedCourses: 0,
      totalStudents: 0,
      totalRevenue: 0
    };
  } finally {
    statsLoading.value = false;
  }
};

const deleteCourse = async (courseId) => {
  if (!confirm('آیا از حذف این دوره اطمینان دارید؟')) {
    return;
  }

  try {
    await courseService.deleteCourse(courseId);
    await fetchCourses();
    await fetchStats();
    // Show success message
  } catch (error) {
    console.error('Error deleting course:', error);
    // Show error message
  }
};

const changePage = async (page) => {
  try {
    loading.value = true;
    const response = await courseService.getCourses(page);
    console.log('Page change response:', response);
    
    // Handle paginated response structure
    if (response.data.data && response.data.data.data) {
      courses.value = response.data.data.data;
      pagination.value = {
        current_page: response.data.data.current_page || page,
        last_page: response.data.data.last_page || 1,
        per_page: response.data.data.per_page || 10,
        total: response.data.data.total || 0
      };
    }
    
    // Recalculate stats for the new page
    await fetchStats();
  } catch (error) {
    console.error('Error changing page:', error);
  } finally {
    loading.value = false;
  }
};

const filteredCourses = computed(() => {
  // Ensure courses.value is always an array
  if (!Array.isArray(courses.value)) {
    return [];
  }
  
  // Filter out any invalid course objects
  let filtered = courses.value.filter(course => 
    course && 
    typeof course === 'object' && 
    course.id && 
    course.title
  );

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(course =>
      course.title && course.title.toLowerCase().includes(query) ||
      course.description && course.description.toLowerCase().includes(query)
    );
  }

  if (statusFilter.value) {
    filtered = filtered.filter(course => course.status === statusFilter.value);
  }

  if (levelFilter.value) {
    filtered = filtered.filter(course => course.level === levelFilter.value);
  }

  return filtered;
});

// Computed property to check if we can safely render the table
const canRenderTable = computed(() => {
  return isReady.value && 
         !loading.value && 
         Array.isArray(filteredCourses.value);
});

const formatPrice = (price) => {
  return formatMoney(price);
};

const getStatusText = (status) => {
  const statusMap = {
    published: 'منتشر شده',
    draft: 'پیش‌نویس',
    archived: 'آرشیو شده'
  };
  return statusMap[status] || status;
};

const getLevelText = (level) => {
  const levelMap = {
    beginner: 'مبتدی',
    intermediate: 'متوسط',
    advanced: 'پیشرفته'
  };
  return levelMap[level] || level;
};
</script>
