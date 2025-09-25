<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold text-white font-vazir">مدیریت ثبت‌نام‌ها</h1>
        <p class="text-gray-400 mt-1">نظارت بر تمامی ثبت‌نام‌ها و پرداخت‌های دوره‌ها</p>
      </div>
      <div class="mt-4 sm:mt-0">
        <button
          @click="exportEnrollments"
          class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 font-vazir"
        >
          <font-awesome-icon icon="download" class="ml-2 h-4 w-4" />
          خروجی اکسل
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="user-graduate" class="h-8 w-8 text-primary" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">کل ثبت‌نام‌ها</p>
            <p class="text-2xl font-bold text-white">{{ stats.totalEnrollments || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="check-circle" class="h-8 w-8 text-green-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">پرداخت‌های تکمیل شده</p>
            <p class="text-2xl font-bold text-white">{{ stats.completedPayments || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="clock" class="h-8 w-8 text-yellow-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">در انتظار پرداخت</p>
            <p class="text-2xl font-bold text-white">{{ stats.pendingPayments || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-black/50 border border-white/10 rounded-lg p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <font-awesome-icon icon="money-bill-wave" class="h-8 w-8 text-blue-400" />
          </div>
          <div class="mr-4">
            <p class="text-sm font-medium text-gray-400 font-vazir">درآمد کل</p>
            <p class="text-2xl font-bold text-white">{{ formatPrice(stats.totalRevenue || 0) }}</p>
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
            placeholder="جستجو در ثبت‌نام‌ها..."
            class="w-full px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          />
        </div>
        <div class="flex gap-2">
          <select
            v-model="statusFilter"
            class="px-4 py-2 bg-black/50 border border-white/20 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent font-vazir"
          >
            <option value="">همه وضعیت‌ها</option>
            <option value="pending">در انتظار پرداخت</option>
            <option value="completed">تکمیل شده</option>
            <option value="failed">ناموفق</option>
            <option value="refunded">بازپرداخت شده</option>
          </select>
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

    <!-- Enrollments Table -->
    <div class="bg-black/50 border border-white/10 rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10">
          <thead class="bg-black/30">
            <tr>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دانشجو
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                دوره
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                تاریخ ثبت‌نام
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                مبلغ
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                وضعیت پرداخت
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider font-vazir">
                عملیات
              </th>
            </tr>
          </thead>
          <tbody class="bg-black/20 divide-y divide-white/10">
            <tr v-for="(enrollment, index) in filteredEnrollments" :key="enrollment && enrollment.id ? enrollment.id : index" class="hover:bg-white/5">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                      <font-awesome-icon icon="user" class="h-5 w-5 text-gray-400" />
                    </div>
                  </div>
                  <div class="mr-4">
                    <div class="text-sm font-medium text-white font-vazir">{{ (enrollment && enrollment.user?.name) ? enrollment.user.name : 'نامشخص' }}</div>
                    <div class="text-sm text-gray-400 font-vazir">{{ (enrollment && enrollment.user?.email) ? enrollment.user.email : 'ایمیل نامشخص' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-white font-vazir">{{ (enrollment && enrollment.course?.title) ? enrollment.course.title : 'دوره نامشخص' }}</div>
                <div class="text-sm text-gray-400 font-vazir">{{ (enrollment && enrollment.course?.level) ? enrollment.course.level : '' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-300 font-vazir">
                  {{ enrollment && enrollment.enrolled_at ? formatDate(enrollment.enrolled_at) : 'نامشخص' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span v-if="enrollment && enrollment.amount_paid > 0" class="text-sm text-gray-300 font-vazir">
                  {{ formatPrice(enrollment.amount_paid) }}
                </span>
                <span v-else class="text-sm text-green-400 font-vazir">رایگان</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  :class="{
                    'bg-yellow-100 text-yellow-800': enrollment && enrollment.payment_status === 'pending',
                    'bg-green-100 text-green-800': enrollment && enrollment.payment_status === 'completed',
                    'bg-red-100 text-red-800': enrollment && enrollment.payment_status === 'failed',
                    'bg-gray-100 text-gray-800': enrollment && enrollment.payment_status === 'refunded'
                  }"
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full font-vazir"
                >
                  {{ enrollment && enrollment.payment_status ? getPaymentStatusText(enrollment.payment_status) : 'نامشخص' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2 space-x-reverse">
                  <button
                    @click="enrollment && viewEnrollmentDetails(enrollment)"
                    class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="eye" class="h-4 w-4" />
                  </button>
                  <button
                    v-if="enrollment && enrollment.payment_status === 'pending'"
                    @click="enrollment && enrollment.id && markAsCompleted(enrollment.id)"
                    class="text-green-400 hover:text-green-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="check" class="h-4 w-4" />
                  </button>
                  <button
                    v-if="enrollment && enrollment.payment_status === 'completed'"
                    @click="enrollment && enrollment.id && processRefund(enrollment.id)"
                    class="text-orange-400 hover:text-orange-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="undo" class="h-4 w-4" />
                  </button>
                  <button
                    @click="enrollment && enrollment.id && cancelEnrollment(enrollment.id)"
                    class="text-red-400 hover:text-red-300 transition-colors duration-200"
                  >
                    <font-awesome-icon icon="times" class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredEnrollments.length === 0" class="text-center py-12">
        <font-awesome-icon icon="user-graduate" class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-400 font-vazir">ثبت‌نام‌ای یافت نشد</h3>
        <p class="mt-1 text-sm text-gray-500 font-vazir">
          {{ searchQuery || statusFilter || courseFilter ? 'فیلترهای اعمال شده را تغییر دهید' : 'هنوز ثبت‌نام‌ای انجام نشده است' }}
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

    <!-- Enrollment Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-black/90 border border-white/10 rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="px-6 py-4">
            <h3 class="text-lg font-medium text-white mb-4 font-vazir">جزئیات ثبت‌نام</h3>
            
            <div v-if="selectedEnrollment" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">دانشجو:</label>
                <p class="text-white font-vazir">{{ selectedEnrollment.user?.name || 'نامشخص' }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">دوره:</label>
                <p class="text-white font-vazir">{{ selectedEnrollment.course?.title || 'نامشخص' }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">تاریخ ثبت‌نام:</label>
                <p class="text-white font-vazir">{{ formatDate(selectedEnrollment.enrolled_at) }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">مبلغ:</label>
                <p class="text-white font-vazir">{{ formatPrice(selectedEnrollment.amount_paid) }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-400 font-vazir">وضعیت:</label>
                <p class="text-white font-vazir">{{ getPaymentStatusText(selectedEnrollment.payment_status) }}</p>
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
import enrollmentService from '@/services/enrollmentService';
import { formatMoney } from '@/utils/moneyFormatter';

const router = useRouter();
const loading = ref(false);
const enrollments = ref([]);
const courses = ref([]);
const stats = ref({});
const searchQuery = ref('');
const statusFilter = ref('');
const courseFilter = ref('');
const showDetailsModal = ref(false);
const selectedEnrollment = ref(null);

// Fetch data on component mount
onMounted(async () => {
  await fetchEnrollments();
  await fetchStats();
  await fetchCourses();
});

const fetchEnrollments = async () => {
  try {
    loading.value = true;
    const response = await enrollmentService.getEnrollments();
    enrollments.value = Array.isArray(response.data.data)
      ? response.data.data.filter(Boolean)
      : [];
  } catch (error) {
    console.error('Error fetching enrollments:', error);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    const response = await enrollmentService.getStatistics();
    stats.value = response.data.data || {};
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
};

const fetchCourses = async () => {
  try {
    // This would come from a courses service
    // For now, we'll use mock data
    courses.value = [
      { id: 1, title: 'آموزش کامل Laravel' },
      { id: 2, title: 'مبانی برنامه نویسی' },
      { id: 3, title: 'توسعه API پیشرفته' }
    ];
  } catch (error) {
    console.error('Error fetching courses:', error);
  }
};

const viewEnrollmentDetails = (enrollment) => {
  selectedEnrollment.value = enrollment;
  showDetailsModal.value = true;
};

const markAsCompleted = async (enrollmentId) => {
  if (!confirm('آیا از تکمیل این پرداخت اطمینان دارید؟')) {
    return;
  }

  try {
    await enrollmentService.updateEnrollmentStatus(enrollmentId, { payment_status: 'completed' });
    await fetchEnrollments();
    await fetchStats();
    // Show success message
  } catch (error) {
    console.error('Error updating enrollment:', error);
    // Show error message
  }
};

const processRefund = async (enrollmentId) => {
  if (!confirm('آیا از بازپرداخت این ثبت‌نام اطمینان دارید؟')) {
    return;
  }

  try {
    await enrollmentService.processRefund(enrollmentId, { reason: 'Admin request' });
    await fetchEnrollments();
    await fetchStats();
    // Show success message
  } catch (error) {
    console.error('Error processing refund:', error);
    // Show error message
  }
};

const cancelEnrollment = async (enrollmentId) => {
  if (!confirm('آیا از لغو این ثبت‌نام اطمینان دارید؟')) {
    return;
  }

  try {
    await enrollmentService.cancelEnrollment(enrollmentId, { reason: 'Admin cancellation' });
    await fetchEnrollments();
    await fetchStats();
    // Show success message
  } catch (error) {
    console.error('Error cancelling enrollment:', error);
    // Show error message
  }
};

const exportEnrollments = () => {
  // Implementation for exporting to Excel
  console.log('Exporting enrollments...');
};

const filteredEnrollments = computed(() => {
  let filtered = Array.isArray(enrollments.value) ? enrollments.value.filter(Boolean) : [];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter((enrollment) => {
      const name = enrollment?.user?.name?.toLowerCase() || '';
      const email = enrollment?.user?.email?.toLowerCase() || '';
      const courseTitle = enrollment?.course?.title?.toLowerCase() || '';
      return name.includes(query) || email.includes(query) || courseTitle.includes(query);
    });
  }

  if (statusFilter.value) {
    filtered = filtered.filter((enrollment) => enrollment && enrollment.payment_status === statusFilter.value);
  }

  if (courseFilter.value) {
    const courseId = parseInt(courseFilter.value);
    filtered = filtered.filter((enrollment) => enrollment && enrollment.course_id === courseId);
  }

  return filtered;
});

const formatPrice = (price) => {
  return formatMoney(price);
};

const formatDate = (dateString) => {
  if (!dateString) return 'نامشخص';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('fa-IR').format(date);
};

const getPaymentStatusText = (status) => {
  const statusMap = {
    pending: 'در انتظار پرداخت',
    completed: 'تکمیل شده',
    failed: 'ناموفق',
    refunded: 'بازپرداخت شده'
  };
  return statusMap[status] || status;
};
</script>

