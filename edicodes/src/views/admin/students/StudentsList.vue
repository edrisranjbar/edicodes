<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white font-vazir">مدیریت دانشجویان</h1>
        <p class="text-gray-400 mt-1">نظارت بر تمامی دانشجویان و دوره‌های آنها</p>
      </div>
      <div class="mt-4 sm:mt-0">
        <button
          @click="exportStudents"
          class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 font-vazir"
        >
          <font-awesome-icon icon="download" class="ml-2 h-4 w-4" />
          خروجی اکسل
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="users" class="h-8 w-8 text-primary" />
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
            <font-awesome-icon icon="graduation-cap" class="h-8 w-8 text-green-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">دانشجویان فعال</p>
            <p class="text-2xl font-bold text-white">{{ stats.activeStudents || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="book" class="h-8 w-8 text-blue-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">میانگین دوره‌ها</p>
            <p class="text-2xl font-bold text-white">{{ stats.averageCourses || 0 }}</p>
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
            placeholder="جستجو در دانشجویان..."
            class="w-full px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          />
        </div>
        <div class="flex gap-2">
          <select
            v-model="courseFilter"
            class="px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          >
            <option value="">همه دوره‌ها</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">
              {{ course.title }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Students Table -->
    <div class="bg-black/50 border border-white/10 rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10">
          <thead class="bg-black/30">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دانشجو
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دوره‌های ثبت‌نام شده
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                تاریخ آخرین فعالیت
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                وضعیت
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                عملیات
              </th>
            </tr>
          </thead>
          <tbody class="bg-black/20 divide-y divide-white/10">
            <tr v-for="student in filteredStudents" :key="student.id" class="hover:bg-white/5">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12">
                    <div class="h-12 w-12 rounded-full bg-gray-600 flex items-center justify-center">
                      <font-awesome-icon icon="user" class="h-6 w-6 text-gray-400" />
                    </div>
                  </div>
                  <div class="mr-4">
                    <div class="text-sm font-medium text-white font-vazir">{{ student.name || 'نامشخص' }}</div>
                    <div class="text-sm text-gray-400 font-vazir">{{ student.email || 'ایمیل نامشخص' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-white font-vazir">{{ student.enrolled_courses_count || 0 }} دوره</div>
                <div class="text-sm text-gray-400 font-vazir">
                  {{ student.enrolled_courses?.map(c => c.title).join(', ') || 'بدون دوره' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-300 font-vazir">
                  {{ formatDate(student.last_activity) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-green-100 text-green-800': student.status === 'active',
                    'bg-yellow-100 text-yellow-800': student.status === 'inactive',
                    'bg-red-100 text-red-800': student.status === 'suspended'
                  }"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full font-vazir"
                >
                  {{ getStatusText(student.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2 space-x-reverse">
                  <button
                    @click="viewStudentDetails(student)"
                    class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="eye" class="h-4 w-4" />
                  </button>
                  <button
                    @click="viewStudentEnrollments(student.id)"
                    class="text-green-400 hover:text-green-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="list" class="h-4 w-4" />
                  </button>
                  <button
                    @click="suspendStudent(student.id)"
                    v-if="student.status !== 'suspended'"
                    class="text-orange-400 hover:text-orange-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="ban" class="h-4 w-4" />
                  </button>
                  <button
                    @click="activateStudent(student.id)"
                    v-if="student.status === 'suspended'"
                    class="text-green-400 hover:text-green-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="check" class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredStudents.length === 0" class="text-center py-12">
        <font-awesome-icon icon="users" class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-400 font-vazir">دانشجویی یافت نشد</h3>
        <p class="mt-1 text-sm text-gray-500 font-vazir">
          {{ searchQuery || courseFilter ? 'فیلترهای اعمال شده را تغییر دهید' : 'هنوز دانشجویی ثبت‌نام نکرده است' }}
        </p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-flex items-center px-4 py-2 text-sm text-gray-400">
        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-primary ml-2"></div>
        در حال بارگذاری...
      </div>
    </div>

    <!-- Student Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-black/90 border border-white/10 rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">جزئیات دانشجو</h3>
            
            <div v-if="selectedStudent" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">نام:</label>
                <p class="text-white font-vazir">{{ selectedStudent.name || 'نامشخص' }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">ایمیل:</label>
                <p class="text-white font-vazir">{{ selectedStudent.email || 'نامشخص' }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">تاریخ عضویت:</label>
                <p class="text-white font-vazir">{{ formatDate(selectedStudent.created_at) }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">وضعیت:</label>
                <p class="text-white font-vazir">{{ getStatusText(selectedStudent.status) }}</p>
              </div>
            </div>
          </div>
          
          <div class="px-6 py-4 bg-black/30 flex justify-end space-x-3 space-x-reverse">
            <button
              @click="showDetailsModal = false"
              class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200 font-vazir"
            >
              بستن
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const students = ref([]);
const courses = ref([]);
const stats = ref({});
const searchQuery = ref('');
const courseFilter = ref('');
const showDetailsModal = ref(false);
const selectedStudent = ref(null);

// Fetch data on component mount
onMounted(async () => {
  await fetchStudents();
  await fetchStats();
  await fetchCourses();
});

const fetchStudents = async () => {
  try {
    loading.value = true;
    // This would come from an API call
    // For now, we'll use mock data
    students.value = [
      {
        id: 1,
        name: 'علی محمدی',
        email: 'ali@example.com',
        status: 'active',
        enrolled_courses_count: 2,
        enrolled_courses: [
          { id: 1, title: 'آموزش کامل Laravel' },
          { id: 2, title: 'مبانی برنامه نویسی' }
        ],
        last_activity: '2024-01-15T10:30:00Z',
        created_at: '2023-12-01T08:00:00Z'
      },
      {
        id: 2,
        name: 'فاطمه احمدی',
        email: 'fateme@example.com',
        status: 'active',
        enrolled_courses_count: 1,
        enrolled_courses: [
          { id: 1, title: 'آموزش کامل Laravel' }
        ],
        last_activity: '2024-01-14T15:45:00Z',
        created_at: '2023-12-10T09:30:00Z'
      }
    ];
  } catch (error) {
    console.error('Error fetching students:', error);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    // This would come from an API call
    stats.value = {
      totalStudents: students.value.length,
      activeStudents: students.value.filter(s => s.status === 'active').length,
      averageCourses: students.value.reduce((sum, s) => sum + s.enrolled_courses_count, 0) / students.value.length || 0
    };
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const fetchCourses = async () => {
  try {
    // This would come from a courses service
    courses.value = [
      { id: 1, title: 'آموزش کامل Laravel' },
      { id: 2, title: 'مبانی برنامه نویسی' },
      { id: 3, title: 'توسعه API پیشرفته' }
    ];
  } catch (error) {
    console.error('Error fetching courses:', error);
  }
};

const viewStudentDetails = (student) => {
  selectedStudent.value = student;
  showDetailsModal.value = true;
};

const viewStudentEnrollments = (studentId) => {
  router.push(`/admin/enrollments?student=${studentId}`);
};

const suspendStudent = async (studentId) => {
  if (!confirm('آیا از تعلیق این دانشجو اطمینان دارید؟')) {
    return;
  }

  try {
    // This would be an API call
    const student = students.value.find(s => s.id === studentId);
    if (student) {
      student.status = 'suspended';
    }
    await fetchStats();
    // Show success message
  } catch (error) {
    console.error('Error suspending student:', error);
    // Show error message
  }
};

const activateStudent = async (studentId) => {
  if (!confirm('آیا از فعال‌سازی این دانشجو اطمینان دارید؟')) {
    return;
  }

  try {
    // This would be an API call
    const student = students.value.find(s => s.id === studentId);
    if (student) {
      student.status = 'active';
    }
    await fetchStats();
    // Show success message
  } catch (error) {
    console.error('Error activating student:', error);
    // Show error message
  }
};

const exportStudents = () => {
  // Implementation for exporting to Excel
  console.log('Exporting students...');
};

const filteredStudents = computed(() => {
  let filtered = students.value;

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(student =>
      student.name?.toLowerCase().includes(query) ||
      student.email?.toLowerCase().includes(query)
    );
  }

  if (courseFilter.value) {
    filtered = filtered.filter(student =>
      student.enrolled_courses?.some(course => course.id === parseInt(courseFilter.value))
    );
  }

  return filtered;
});

const formatDate = (dateString) => {
  if (!dateString) return 'نامشخص';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('fa-IR').format(date);
};

const getStatusText = (status) => {
  const statusMap = {
    active: 'فعال',
    inactive: 'غیرفعال',
    suspended: 'تعلیق شده'
  };
  return statusMap[status] || status;
};
</script>
















