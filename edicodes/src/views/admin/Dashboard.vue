<template>
  <div>
    <!-- Dashboard widgets (4-widget stats) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-black/30 rounded-lg p-5 border border-white/10">
        <div class="flex items-center">
          <div class="w-12 h-12 rounded-lg bg-primary/20 flex items-center justify-center ml-3">
            <font-awesome-icon icon="newspaper" class="text-xl text-primary" />
          </div>
          <div>
            <p class="text-white/70 text-sm font-vazir">تعداد مطالب</p>
            <h3 class="text-white text-2xl font-vazir font-bold mt-1">{{ stats.posts || 0 }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-black/30 rounded-lg p-5 border border-white/10">
        <div class="flex items-center">
          <div class="w-12 h-12 rounded-lg bg-emerald-500/20 flex items-center justify-center ml-3">
            <font-awesome-icon icon="folder" class="text-xl text-emerald-500" />
          </div>
          <div>
            <p class="text-white/70 text-sm font-vazir">تعداد دسته‌بندی‌ها</p>
            <h3 class="text-white text-2xl font-vazir font-bold mt-1">{{ stats.categories || 0 }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-black/30 rounded-lg p-5 border border-white/10">
        <div class="flex items-center">
          <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center ml-3">
            <font-awesome-icon icon="comments" class="text-xl text-yellow-500" />
          </div>
          <div>
            <p class="text-white/70 text-sm font-vazir">دیدگاه‌های در انتظار</p>
            <h3 class="text-white text-2xl font-vazir font-bold mt-1">{{ stats.pendingComments || 0 }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-black/30 rounded-lg p-5 border border-white/10">
        <div class="flex items-center">
          <div class="w-12 h-12 rounded-lg bg-purple-500/20 flex items-center justify-center ml-3">
            <font-awesome-icon icon="donate" class="text-xl text-purple-500" />
          </div>
          <div>
            <p class="text-white/70 text-sm font-vazir">تعداد حامیان</p>
            <h3 class="text-white text-2xl font-vazir font-bold mt-1">{{ stats.donations || 0 }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Posts -->
    <div class="rounded-lg bg-black/50 border border-white/10 p-6 mb-8">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-vazir text-white">آخرین مطالب</h2>
        <router-link to="/admin/posts"
          class="text-primary text-sm hover:text-primary-light transition-colors duration-150 font-vazir">
          مشاهده همه
        </router-link>
      </div>

      <div v-if="loading" class="flex justify-center py-6">
        <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-primary"></div>
      </div>

      <div v-else-if="recentPosts.length === 0" class="text-center py-6 text-white/50 font-vazir">
        هنوز مطلبی ایجاد نشده است.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="text-right border-b border-white/10">
              <th class="py-3 pr-3 text-sm font-vazir text-white/60 font-normal">عنوان</th>
              <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">دسته‌بندی</th>
              <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">تاریخ</th>
              <th class="py-3 pl-3 text-sm font-vazir text-white/60 font-normal">وضعیت</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="post in recentPosts" :key="post.id" class="text-right border-b border-white/5 hover:bg-white/5">
              <td class="py-4 pr-3">
                <div class="font-vazir text-white text-sm truncate max-w-xs">{{ post.title }}</div>
              </td>
              <td class="py-4 px-3">
                <span class="inline-flex px-2 py-1 bg-primary/10 text-primary text-xs rounded-full font-vazir">
                  {{ post.category?.name || 'بدون دسته‌بندی' }}
                </span>
              </td>
              <td class="py-4 px-3">
                <div class="font-vazir text-white/70 text-sm">{{ formatDate(post.published_at) }}</div>
              </td>
              <td class="py-4 pl-3">
                <span :class="post.published ? 'bg-green-500/10 text-green-500' : 'bg-yellow-500/10 text-yellow-500'"
                  class="inline-flex px-2 py-1 text-xs rounded-full font-vazir">
                  {{ post.published ? 'منتشر شده' : 'پیش‌نویس' }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Analytics Summary -->
    <div class="rounded-lg bg-black/50 border border-white/10 p-6 mb-8">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-vazir text-white">خلاصه آمار بازدید</h2>
        <router-link to="/admin/analytics"
          class="text-primary text-sm hover:text-primary-light transition-colors duration-150 font-vazir">
          مشاهده جزئیات
        </router-link>
      </div>

      <div v-if="loading" class="flex justify-center py-6">
        <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-primary"></div>
      </div>

      <div v-else>
        <!-- Weekly views mini chart -->
        <div class="h-64 mb-6">
          <div v-if="!analytics.dailyViews || analytics.dailyViews.length === 0" class="text-center py-6 text-white/50 font-vazir">
            داده‌ای برای نمایش نمودار وجود ندارد.
          </div>
          <div v-else class="h-full">
            <LineChart :data="chartData" :options="chartOptions" />
          </div>
        </div>

        <!-- Summary table -->
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="text-right border-b border-white/10">
                <th class="py-3 pr-3 text-sm font-vazir text-white/60 font-normal">بازدید کل</th>
                <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">بازدیدکنندگان منحصر به فرد</th>
                <th class="py-3 px-3 text-sm font-vazir text-white/60 font-normal">بازدید امروز</th>
                <th class="py-3 pl-3 text-sm font-vazir text-white/60 font-normal">بازدید هفته</th>
              </tr>
            </thead>
            <tbody>
              <tr class="text-right border-b border-white/5 hover:bg-white/5">
                <td class="py-4 pr-3">
                  <div class="font-vazir text-white text-lg">{{ stats.views.total || 0 }}</div>
                </td>
                <td class="py-4 px-3">
                  <div class="font-vazir text-white text-lg">{{ stats.views.unique || 0 }}</div>
                </td>
                <td class="py-4 px-3">
                  <div class="font-vazir text-white text-lg">{{ stats.views.today || 0 }}</div>
                </td>
                <td class="py-4 pl-3">
                  <div class="font-vazir text-white text-lg">{{ stats.views.week || 0 }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Recent comments widget -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden mb-8">
      <div class="bg-black/50 p-4 border-b border-white/10 flex items-center justify-between">
        <h3 class="text-white font-vazir text-lg flex items-center">
          <font-awesome-icon icon="comments" class="text-primary ml-2" />
          آخرین دیدگاه‌ها
        </h3>
        <router-link to="/admin/comments"
          class="bg-primary/10 hover:bg-primary/20 text-primary text-xs px-3 py-1 rounded-md font-vazir transition-colors">
          مشاهده همه
        </router-link>
      </div>

      <div class="divide-y divide-black/50">
        <div v-if="recentComments.loading" class="p-6 flex justify-center">
          <div class="w-8 h-8 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
        </div>

        <div v-else-if="recentComments.error" class="p-6 text-center text-red-400 font-vazir text-sm">
          خطایی در بارگذاری دیدگاه‌ها رخ داده است.
        </div>

        <div v-else-if="recentComments.data.length === 0" class="p-6 text-center text-white/50 font-vazir text-sm">
          در حال حاضر دیدگاهی ثبت نشده است.
        </div>

        <template v-else>
          <div v-for="comment in recentComments.data" :key="comment.id" class="p-4 hover:bg-black/10 transition-colors">
            <div class="flex items-start">
              <div class="rounded-full w-10 h-10 bg-white/10 flex items-center justify-center ml-3">
                <font-awesome-icon icon="user" class="text-white/70" />
              </div>
              <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                  <div class="flex items-center">
                    <h4 class="font-vazir text-white text-sm">
                      {{ comment.name }}
                    </h4>
                    <span :class="[
                      comment.status === 'pending' ? 'bg-yellow-500/20 text-yellow-500' :
                        comment.status === 'approved' ? 'bg-emerald-500/20 text-emerald-500' :
                          'bg-red-500/20 text-red-500'
                    ]" class="text-xs px-2 py-0.5 rounded-full mr-2 font-vazir">
                      {{
                        comment.status === 'pending' ? 'در انتظار' :
                          comment.status === 'approved' ? 'تایید شده' :
                            'حذف شده'
                      }}
                    </span>
                  </div>
                  <span class="text-white/50 text-xs font-vazir">{{ formatDate(comment.created_at) }}</span>
                </div>
                <p class="text-white/70 text-sm font-vazir">
                  {{ truncateText(comment.content, 80) }}
                </p>
                <div class="mt-2 flex items-center justify-between">
                  <span v-if="comment.post" class="text-white/50 text-xs font-vazir flex items-center">
                    <font-awesome-icon icon="newspaper" class="text-primary/70 ml-1 text-xs" />
                    {{ truncateText(comment.post.title, 30) }}
                  </span>
                  <router-link :to="`/admin/comments/${comment.id}`"
                    class="bg-primary/10 text-primary hover:bg-primary/20 transition-colors duration-150 rounded-md px-2 py-0.5 text-xs font-vazir flex items-center">
                    <font-awesome-icon icon="eye" class="ml-1" />
                    مشاهده
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- Donations list -->
    <div class="bg-black/30 rounded-lg border border-white/10 overflow-hidden mb-8">
      <div class="bg-black/50 p-4 border-b border-white/10 flex items-center justify-between">
        <h3 class="text-white font-vazir text-lg flex items-center">
          <font-awesome-icon icon="donate" class="text-purple-500 ml-2" />
          لیست حمایت‌های مالی
        </h3>
        <router-link to="/admin/donations"
          class="bg-purple-500/10 hover:bg-purple-500/20 text-purple-500 text-xs px-3 py-1 rounded-md font-vazir transition-colors">
          مشاهده همه
        </router-link>
      </div>

      <div class="divide-y divide-black/50">
        <div v-if="recentDonations.loading" class="p-6 flex justify-center">
          <div class="w-8 h-8 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin"></div>
        </div>

        <div v-else-if="recentDonations.error" class="p-6 text-center text-red-400 font-vazir text-sm">
          خطایی در بارگذاری حمایت‌ها رخ داده است.
        </div>

        <div v-else-if="recentDonations.data.length === 0" class="p-6 text-center text-white/50 font-vazir text-sm">
          در حال حاضر حمایت مالی ثبت نشده است.
        </div>

        <template v-else>
          <div v-for="(donation, index) in recentDonations.data" :key="donation && donation.id ? donation.id : index" class="py-4 px-6 hover:bg-black/20 transition-colors duration-200">
            <div class="flex flex-col md:flex-row md:justify-between">
              <div class="flex items-center mb-2 md:mb-0">
                <span class="text-white font-vazir">{{ (donation && donation.name) ? donation.name : 'ناشناس' }}</span>
                <span class="mx-2 text-white/30">|</span>
                <span class="text-gray-400 text-sm">{{ donation && donation.created_at ? formatDate(donation.created_at) : '' }}</span>
              </div>
              <div class="flex items-center">
                <span :class="{
                  'px-2 py-1 text-xs rounded-md font-vazir': true,
                  'bg-green-500/10 text-green-500': donation && donation.status === 'paid',
                  'bg-yellow-500/10 text-yellow-500': donation && donation.status === 'pending',
                  'bg-red-500/10 text-red-500': donation && donation.status === 'failed'
                }">
                  {{ donation && donation.status_in_persian ? donation.status_in_persian : '-' }}
                </span>
                <span class="mx-2 text-white/30">|</span>
                <span class="text-white font-vazir">{{ donation && donation.formatted_amount ? donation.formatted_amount : '-' }}</span>
              </div>
            </div>
            <p v-if="donation && donation.message" class="mt-2 text-white/70 text-sm font-vazir">{{ donation.message }}</p>
          </div>
        </template>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="rounded-lg bg-black/50 border border-white/10 p-6 mb-8">
      <h2 class="text-lg font-vazir text-white mb-6">دسترسی سریع</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <router-link to="/admin/posts/create"
          class="p-4 bg-primary/5 hover:bg-primary/10 border border-primary/20 rounded-lg transition-colors duration-200 flex items-center">
          <div class="bg-primary/10 rounded-lg w-10 h-10 flex items-center justify-center ml-3">
            <font-awesome-icon icon="plus" class="h-4 w-4 text-primary" />
          </div>
          <span class="text-white font-vazir text-sm">ایجاد مطلب جدید</span>
        </router-link>

        <router-link to="/admin/categories/create"
          class="p-4 bg-green-500/5 hover:bg-green-500/10 border border-green-500/20 rounded-lg transition-colors duration-200 flex items-center">
          <div class="bg-green-500/10 rounded-lg w-10 h-10 flex items-center justify-center ml-3">
            <font-awesome-icon icon="folder-plus" class="h-4 w-4 text-green-500" />
          </div>
          <span class="text-white font-vazir text-sm">ایجاد دسته‌بندی جدید</span>
        </router-link>

        <a href="/" target="_blank"
          class="p-4 bg-blue-500/5 hover:bg-blue-500/10 border border-blue-500/20 rounded-lg transition-colors duration-200 flex items-center">
          <div class="bg-blue-500/10 rounded-lg w-10 h-10 flex items-center justify-center ml-3">
            <font-awesome-icon icon="globe" class="h-4 w-4 text-blue-500" />
          </div>
          <span class="text-white font-vazir text-sm">مشاهده سایت</span>
        </a>

        <a href="/blog" target="_blank"
          class="p-4 bg-purple-500/5 hover:bg-purple-500/10 border border-purple-500/20 rounded-lg transition-colors duration-200 flex items-center">
          <div class="bg-purple-500/10 rounded-lg w-10 h-10 flex items-center justify-center ml-3">
            <font-awesome-icon icon="rss" class="h-4 w-4 text-purple-500" />
          </div>
          <span class="text-white font-vazir text-sm">مشاهده وبلاگ</span>
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { API_URL } from '@/config';
import { Line as LineChart } from 'vue-chartjs'
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler } from 'chart.js'
import moment from 'moment-jalaali'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler)

const loading = ref(true);
const stats = ref({
  postsCount: 0,
  categoriesCount: 0,
  publishedCount: 0,
  posts: 0,
  categories: 0,
  pendingComments: 0,
  views: 0,
  donations: 0,
  totalDonations: 0
});
const recentPosts = ref([]);
const recentComments = ref({
  loading: true,
  error: null,
  data: []
});
const recentDonations = ref({
  loading: true,
  error: null,
  data: []
});

// --- Analytics mini chart state ---
const analytics = ref({ dailyViews: [] })

const toPersianDigits = (str) => {
  const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹']
  return str.toString().replace(/[0-9]/g, (w) => persianDigits[+w])
}

const formatChartLabel = (dateString) => {
  const date = moment(dateString)
  const day = toPersianDigits(date.format('jD'))
  const monthNames = {
    'Farvardin': 'فروردین',
    'Ordibehesht': 'اردیبهشت',
    'Khordad': 'خرداد',
    'Tir': 'تیر',
    'Mordad': 'مرداد',
    'Shahrivar': 'شهریور',
    'Mehr': 'مهر',
    'Aban': 'آبان',
    'Azar': 'آذر',
    'Dey': 'دی',
    'Bahman': 'بهمن',
    'Esfand': 'اسفند'
  }
  const month = monthNames[date.format('jMMMM')]
  return `${day} ${month}`
}

const chartData = computed(() => {
  const labels = analytics.value.dailyViews?.map(item => formatChartLabel(item.date)) || []
  const views = analytics.value.dailyViews?.map(item => item.views) || []
  const uniques = analytics.value.dailyViews?.map(item => item.unique_visitors) || []

  return {
    labels,
    datasets: [
      {
        label: 'بازدیدها',
        data: views,
        borderColor: '#6366f1',
        backgroundColor: 'rgba(99, 102, 241, 0.1)',
        tension: 0.4,
        fill: true
      },
      {
        label: 'بازدیدکنندگان منحصر به فرد',
        data: uniques,
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true
      }
    ]
  }
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
      labels: {
        font: { family: 'Vazirmatn' },
        color: 'rgba(255, 255, 255, 0.7)'
      }
    },
    tooltip: {
      mode: 'index',
      intersect: false,
      backgroundColor: 'rgba(0, 0, 0, 0.8)',
      titleColor: '#fff',
      bodyColor: '#fff',
      borderColor: 'rgba(255, 255, 255, 0.1)',
      borderWidth: 1,
      callbacks: {
        label: (context) => {
          const label = context.dataset.label || ''
          const value = context.parsed.y
          return `${label}: ${toPersianDigits(value)}`
        }
      }
    }
  },
  scales: {
    y: {
      beginAtZero: true,
      grid: { color: 'rgba(255, 255, 255, 0.1)' },
      ticks: {
        color: 'rgba(255, 255, 255, 0.7)',
        callback: (value) => toPersianDigits(value)
      }
    },
    x: {
      grid: { color: 'rgba(255, 255, 255, 0.1)' },
      ticks: { color: 'rgba(255, 255, 255, 0.7)', maxRotation: 45, minRotation: 45 }
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '';
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

const truncateText = (text, maxLength) => {
  if (!text) return '';
  if (text.length <= maxLength) return text;
  return text.substring(0, maxLength) + '...';
};

const fetchDashboardData = async () => {
  try {
    loading.value = true;

    const token = localStorage.getItem('admin_token');

    const response = await axios.get(`${API_URL}/admin/dashboard`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    // Update stats
    stats.value = response.data.stats;
    
    // Update recent posts from dashboard data
    recentPosts.value = response.data.recentPosts || [];

    // Fetch weekly analytics mini series
    const analyticsRes = await axios.get(`${API_URL}/admin/analytics?period=week`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    analytics.value = analyticsRes.data || { dailyViews: [] }
  } catch (err) {
    console.error('Error fetching dashboard data:', err);
  } finally {
    loading.value = false;
  }
};

const fetchRecentComments = async () => {
  try {
    recentComments.value.loading = true;
    recentComments.value.error = null;

    const token = localStorage.getItem('admin_token');

    const response = await axios.get(`${API_URL}/admin/comments`, {
      params: {
        per_page: 5,
        sort: '-created_at',
        status: 'all'
      },
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    recentComments.value.data = response.data.data || [];
  } catch (err) {
    console.error('Error fetching recent comments:', err);
    recentComments.value.error = 'خطایی در بارگذاری دیدگاه‌ها رخ داده است';
    recentComments.value.data = [];
  } finally {
    recentComments.value.loading = false;
  }
};

const fetchRecentDonations = async () => {
  try {
    recentDonations.value.loading = true;
    recentDonations.value.error = null;

    const token = localStorage.getItem('admin_token');
    
    const apiUrl = `${API_URL}/admin/donations`;
    console.log('Fetching donations from:', apiUrl);

    const response = await axios.get(apiUrl, {
      params: {
        per_page: 5,
        sort: '-created_at',
        status: 'all'
      },
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    const body = response.data || {};
    const paginated = body && body.data && typeof body.data === 'object' && !Array.isArray(body.data) && 'data' in body.data
      ? body.data
      : body;

    const items = Array.isArray(paginated.data)
      ? paginated.data
      : Array.isArray(body.data)
        ? body.data
        : [];

    recentDonations.value.data = items.filter(Boolean);
  } catch (err) {
    console.error('Error fetching recent donations:', err);
    recentDonations.value.error = 'خطایی در بارگذاری حمایت‌ها رخ داده است';
    recentDonations.value.data = [];
  } finally {
    recentDonations.value.loading = false;
  }
};

onMounted(() => {
  fetchDashboardData();
  fetchRecentComments();
  fetchRecentDonations();
});
</script>