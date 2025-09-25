<template>
  <div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
      <h1 class="text-xl font-vazir text-white mb-4 md:mb-0">حمایت‌های مالی</h1>
      
      <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-reverse sm:space-x-2">
        <div class="flex">
          <select 
            v-model="filters.status" 
            @change="resetPageAndFetch"
            class="bg-black/30 border border-white/10 text-white/80 text-sm rounded-lg px-2.5 py-1.5 font-vazir appearance-none"
          >
            <option value="all">همه وضعیت‌ها</option>
            <option value="paid">پرداخت شده</option>
            <option value="pending">در انتظار پرداخت</option>
            <option value="failed">ناموفق</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Donations Table -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-black/50">
          <thead class="bg-black/50">
            <tr>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-white/50 font-vazir">نام</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-white/50 font-vazir">ایمیل</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-white/50 font-vazir">مبلغ</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-white/50 font-vazir">وضعیت</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-white/50 font-vazir">کد پیگیری</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-white/50 font-vazir">تاریخ</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-black/50 bg-black/20">
            <!-- Loading state -->
            <tr v-if="loading">
              <td colspan="6" class="px-6 py-8">
                <div class="flex justify-center">
                  <div class="w-8 h-8 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                </div>
              </td>
            </tr>

            <!-- No results -->
            <tr v-else-if="donations.length === 0">
              <td colspan="6" class="px-6 py-8 text-center text-white/50 font-vazir">
                حمایت مالی یافت نشد.
              </td>
            </tr>

            <!-- Results -->
            <tr v-for="(donation, index) in donations" :key="donation && donation.id ? donation.id : index" class="hover:bg-black/40 transition-colors duration-150">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-vazir text-white">
                {{ (donation && donation.name) ? donation.name : 'ناشناس' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-vazir text-white/80">
                {{ donation && donation.email ? donation.email : '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-vazir text-white font-bold">
                {{ donation && donation.formatted_amount ? donation.formatted_amount : '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="{
                  'px-2 py-1 text-xs rounded-md font-vazir': true,
                  'bg-green-500/10 text-green-500': donation && donation.status === 'paid',
                  'bg-yellow-500/10 text-yellow-500': donation && donation.status === 'pending',
                  'bg-red-500/10 text-red-500': donation && donation.status === 'failed'
                }">
                  {{ donation && donation.status_in_persian ? donation.status_in_persian : '-' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-vazir text-white/80">
                {{ donation && donation.ref_id ? donation.ref_id : '-' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-vazir text-white/80">
                {{ donation && donation.created_at ? formatDate(donation.created_at) : '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && pagination.last_page > 1" class="mt-6 flex justify-center">
      <div class="flex items-center space-x-reverse space-x-1 rtl">
        <button 
          @click="changePage(1)" 
          :disabled="pagination.current_page === 1"
          :class="[
            'px-2 py-1 rounded border font-vazir text-sm',
            pagination.current_page === 1 
              ? 'bg-black/20 text-white/40 border-white/5 cursor-not-allowed' 
              : 'bg-black/30 hover:bg-black/40 text-white border-white/10'
          ]"
        >
          اولین
        </button>

        <button 
          @click="changePage(pagination.current_page - 1)" 
          :disabled="pagination.current_page === 1"
          :class="[
            'px-2 py-1 rounded border font-vazir text-sm',
            pagination.current_page === 1 
              ? 'bg-black/20 text-white/40 border-white/5 cursor-not-allowed' 
              : 'bg-black/30 hover:bg-black/40 text-white border-white/10'
          ]"
        >
          قبلی
        </button>

        <span class="text-white font-vazir text-sm">
          صفحه {{ pagination.current_page }} از {{ pagination.last_page }}
        </span>

        <button 
          @click="changePage(pagination.current_page + 1)" 
          :disabled="pagination.current_page === pagination.last_page"
          :class="[
            'px-2 py-1 rounded border font-vazir text-sm',
            pagination.current_page === pagination.last_page 
              ? 'bg-black/20 text-white/40 border-white/5 cursor-not-allowed' 
              : 'bg-black/30 hover:bg-black/40 text-white border-white/10'
          ]"
        >
          بعدی
        </button>

        <button 
          @click="changePage(pagination.last_page)" 
          :disabled="pagination.current_page === pagination.last_page"
          :class="[
            'px-2 py-1 rounded border font-vazir text-sm',
            pagination.current_page === pagination.last_page 
              ? 'bg-black/20 text-white/40 border-white/5 cursor-not-allowed' 
              : 'bg-black/30 hover:bg-black/40 text-white border-white/10'
          ]"
        >
          آخرین
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue';
import axios from 'axios';
import { API_URL } from '@/config';

// State
const loading = ref(true);
const donations = ref([]);
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0
});

const filters = reactive({
  status: 'all',
  per_page: 10
});

// Methods
const fetchDonations = async () => {
  try {
    loading.value = true;
    const token = localStorage.getItem('admin_token');
    
    const params = {
      page: pagination.current_page,
      per_page: filters.per_page,
      status: filters.status,
      sort: '-created_at'
    };
    
    // API_URL already includes '/api', so we need to be careful not to duplicate it
    const apiUrl = `${API_URL}/admin/donations`;
    console.log('Fetching donations from:', apiUrl, params);
    
    const response = await axios.get(apiUrl, {
      params,
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    // Support both API shapes:
    // A) { success, data: { current_page, data: [...] } }
    // B) { current_page, data: [...] }
    const body = response.data || {};
    const paginated = body && body.data && typeof body.data === 'object' && !Array.isArray(body.data) && 'data' in body.data
      ? body.data
      : body;

    const items = Array.isArray(paginated.data)
      ? paginated.data
      : Array.isArray(body.data)
        ? body.data
        : [];

    donations.value = items.filter(Boolean);

    // Update pagination safely
    pagination.current_page = paginated.current_page || 1;
    pagination.last_page = paginated.last_page || 1;
    pagination.per_page = paginated.per_page || filters.per_page;
    pagination.total = paginated.total || items.length;
    
  } catch (error) {
    console.error('Error fetching donations:', error);
    donations.value = [];
  } finally {
    loading.value = false;
  }
};

const changePage = (page) => {
  if (page < 1 || page > pagination.last_page) return;
  pagination.current_page = page;
  fetchDonations();
};

const resetPageAndFetch = () => {
  pagination.current_page = 1;
  fetchDonations();
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };

  // Get the formatted date parts from Intl.DateTimeFormat
  const formatter = new Intl.DateTimeFormat('fa-IR', options);
  const parts = formatter.formatToParts(date);

  // Extract the individual parts
  let day = '';
  let month = '';
  let year = '';

  parts.forEach(part => {
    if (part.type === 'day') day = part.value;
    if (part.type === 'month') month = part.value;
    if (part.type === 'year') year = part.value;
  });

  // Format according to the required pattern: ۲۳ اردیبهشت ۱۴۰۴
  return `${day} ${month} ${year}`;
};

// Watch for filter changes
watch(filters, () => {
  resetPageAndFetch();
}, { deep: true });

// Lifecycle hooks
onMounted(() => {
  fetchDonations();
});
</script> 