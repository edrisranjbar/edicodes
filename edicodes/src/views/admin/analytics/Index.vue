<template>
  <main class="analytics-page" dir="rtl" :aria-busy="loading">
    <header class="analytics-header">
      <div>
        <p class="eyebrow">نمای کلی عملکرد</p>
        <h1>آمار بازدید</h1>
        <p>رفتار بازدیدکنندگان و عملکرد محتوای سایت در بازه انتخابی</p>
      </div>
      <button class="refresh" @click="fetchAnalytics" :disabled="loading">
        {{ loading ? 'در حال دریافت…' : 'به‌روزرسانی آمار' }}
      </button>
    </header>
    <div class="period-bar">
      <div class="periods" aria-label="بازه زمانی">
        <button
          v-for="period in periods"
          :key="period.value"
          :aria-pressed="selectedPeriod === period.value"
          :class="{ active: selectedPeriod === period.value }"
          @click="selectedPeriod = period.value"
        >
          {{ period.label }}
        </button>
      </div>
      <span v-if="analytics.period"
        >{{ date(analytics.period.start) }} تا {{ date(analytics.period.end) }}</span
      >
    </div>
    <div v-if="error" class="error" role="alert">
      {{ error }} <button @click="fetchAnalytics">تلاش دوباره</button>
    </div>
    <p v-if="loading" class="loading" role="status">در حال دریافت آمار بازه انتخابی…</p>
    <template v-else-if="!error">
      <div class="metrics">
        <article v-for="metric in metrics" :key="metric.label">
          <span class="metric-icon"><font-awesome-icon :icon="metric.icon" /></span>
          <p>{{ metric.label }}</p>
          <strong>{{ number(metric.value) }}</strong
          ><small>{{ metric.help }}</small>
        </article>
      </div>
      <section class="panel">
        <div class="panel-heading">
          <div>
            <h2>روند بازدید روزانه</h2>
            <p>روزهای بدون بازدید نیز در نمودار و میانگین محاسبه می‌شوند.</p>
          </div>
          <span class="badge">{{ number(days.length) }} روز</span>
        </div>
        <p v-if="!Number(analytics.totalViews)" class="empty">در این بازه بازدیدی ثبت نشده است.</p>
        <div v-else class="chart"><LineChart :data="chartData" :options="chartOptions" /></div>
      </section>
      <div class="insights">
        <section class="panel">
          <h2>پربازدیدترین روز</h2>
          <strong class="insight-value">{{ peak.views ? date(peak.date) : '—' }}</strong>
          <p>{{ number(peak.views) }} بازدید در این روز</p>
        </section>
        <section class="panel">
          <h2>روزهای دارای بازدید</h2>
          <strong class="insight-value"
            >{{ number(activeDays) }} <small>از {{ number(days.length) }} روز</small></strong
          >
          <p>تعداد روزهایی که حداقل یک بازدید ثبت شده است</p>
        </section>
      </div>
      <section v-for="table in tables" :key="table.title" class="panel">
        <div class="panel-heading">
          <div>
            <h2>{{ table.title }}</h2>
            <p>{{ table.description }}</p>
          </div>
          <span class="badge">۵ مورد برتر</span>
        </div>
        <p v-if="!table.rows.length" class="empty">داده‌ای برای این بازه وجود ندارد.</p>
        <div v-else class="table-scroll">
          <table>
            <thead>
              <tr>
                <th>عنوان / مسیر</th>
                <th>بازدید</th>
                <th>بازدیدکننده یکتا</th>
                <th>سهم از کل بازدید سایت</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in table.rows" :key="row.id ?? row.path">
                <td>
                  <router-link v-if="row.id" :to="`/admin/posts/edit/${row.id}`">{{
                    row.title
                  }}</router-link
                  ><span v-else dir="ltr" class="page-path">{{ row.path }}</span
                  ><small v-if="row.category">{{ row.category.name }}</small>
                </td>
                <td>{{ number(row.views_count) }}</td>
                <td>{{ number(row.unique_visitors_count ?? row.unique_visitors) }}</td>
                <td>
                  <div class="share">
                    <span>{{ number(share(row.views_count)) }}٪</span>
                    <div><i :style="{ width: share(row.views_count) + '%' }" /></div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
      <section class="panel">
        <div class="panel-heading">
          <div>
            <h2>جزئیات روزانه</h2>
            <p>
              بازدیدکننده یکتا در هر روز جداگانه شمارش می‌شود؛ جمع این ستون برابر تعداد یکتای کل
              بازه نیست.
            </p>
          </div>
          <button class="refresh" @click="exportCsv" :disabled="!days.length">دریافت CSV</button>
        </div>
        <div class="table-scroll daily-table">
          <table>
            <thead>
              <tr>
                <th>تاریخ</th>
                <th>بازدید</th>
                <th>بازدیدکننده یکتا</th>
                <th>بازدید به‌ازای هر بازدیدکننده</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="day in [...days].reverse()" :key="day.date">
                <td>{{ date(day.date) }}</td>
                <td>{{ number(day.views) }}</td>
                <td>{{ number(day.unique_visitors) }}</td>
                <td>{{ number(day.unique_visitors ? day.views / day.unique_visitors : 0) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
      <p class="method-note">
        بازدیدکنندگان یکتا بر اساس IP شمارش می‌شوند و تخمینی از تعداد افراد هستند. تمام اعداد مربوط
        به بازه انتخابی‌اند؛ تاریخ‌ها بر اساس منطقه زمانی سرور ثبت می‌شوند.
      </p>
    </template>
  </main>
</template>

<script setup>
defineOptions({ name: 'AnalyticsPage' })
import { ref, computed, watch, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { API_URL } from '@/config'
import { Line as LineChart } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend, Filler)
const analytics = ref({})
const loading = ref(true)
const error = ref('')
const selectedPeriod = ref('week')
const periods = [
  { label: 'امروز', value: 'today' },
  { label: '۷ روز', value: 'week' },
  { label: '۳۰ روز', value: 'month' },
  { label: '۳۶۵ روز', value: 'year' },
]
const number = (value) =>
  new Intl.NumberFormat('fa-IR', { maximumFractionDigits: 1 }).format(Number(value) || 0)
const date = (value) =>
  value
    ? new Intl.DateTimeFormat('fa-IR', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        timeZone: 'UTC',
      }).format(new Date(value + 'T00:00:00Z'))
    : '—'
let controller
let requestId = 0
const fetchAnalytics = async () => {
  controller?.abort()
  controller = new AbortController()
  const id = ++requestId
  loading.value = true
  error.value = ''
  analytics.value = {}
  try {
    const response = await axios.get(`${API_URL}/admin/analytics`, {
      params: { period: selectedPeriod.value },
      signal: controller.signal,
      headers: { Authorization: `Bearer ${localStorage.getItem('admin_token')}` },
    })
    if (id === requestId) analytics.value = response.data
  } catch (err) {
    if (id === requestId && !axios.isCancel(err))
      error.value = 'دریافت آمار ممکن نشد. اتصال خود را بررسی و دوباره تلاش کنید.'
  } finally {
    if (id === requestId) loading.value = false
  }
}
const days = computed(() => {
  if (!analytics.value.period) return []
  const rows = new Map((analytics.value.dailyViews || []).map((row) => [row.date, row]))
  const start = new Date(analytics.value.period.start + 'T00:00:00Z')
  const end = new Date(analytics.value.period.end + 'T00:00:00Z')
  const result = []
  for (let cursor = start; cursor <= end; cursor.setUTCDate(cursor.getUTCDate() + 1)) {
    const key = cursor.toISOString().slice(0, 10)
    const row = rows.get(key)
    result.push({
      date: key,
      views: Number(row?.views) || 0,
      unique_visitors: Number(row?.unique_visitors) || 0,
    })
  }
  return result
})
const metrics = computed(() => [
  {
    label: 'بازدید کل بازه',
    value: analytics.value.totalViews,
    icon: 'eye',
    help: 'شامل مطالب و صفحات سایت',
  },
  {
    label: 'بازدیدکنندگان یکتا',
    value: analytics.value.uniqueVisitors,
    icon: 'users',
    help: 'IPهای متمایز در کل بازه',
  },
  {
    label: 'میانگین بازدید روزانه',
    value: days.value.length ? Number(analytics.value.totalViews) / days.value.length : 0,
    icon: 'chart-line',
    help: 'با احتساب روزهای بدون بازدید',
  },
  {
    label: 'بازدید به‌ازای هر بازدیدکننده',
    value: Number(analytics.value.uniqueVisitors)
      ? Number(analytics.value.totalViews) / Number(analytics.value.uniqueVisitors)
      : 0,
    icon: 'globe',
    help: 'بازدید کل تقسیم بر بازدیدکنندگان یکتا',
  },
])
const peak = computed(() =>
  days.value.reduce((best, day) => (day.views > best.views ? day : best), { views: 0 }),
)
const activeDays = computed(() => days.value.filter((day) => day.views > 0).length)
const share = (count) =>
  Number(analytics.value.totalViews)
    ? Math.min(100, (Number(count) / Number(analytics.value.totalViews)) * 100)
    : 0
const tables = computed(() => [
  {
    title: 'پربازدیدترین مطالب',
    description: 'مطالب برتر در بازه انتخابی؛ برای ویرایش روی عنوان کلیک کنید.',
    rows: analytics.value.topPosts || [],
  },
  {
    title: 'پربازدیدترین صفحات',
    description: 'مسیرهای عمومی سایت، جدا از بازدید مطالب',
    rows: analytics.value.topPages || [],
  },
])
const chartData = computed(() => ({
  labels: days.value.map((row) => date(row.date)),
  datasets: [
    {
      label: 'بازدیدها',
      data: days.value.map((row) => row.views),
      borderColor: '#b7c4ff',
      backgroundColor: '#b7c4ff16',
      fill: true,
      tension: 0.25,
      pointRadius: days.value.length > 30 ? 0 : 3,
    },
    {
      label: 'بازدیدکنندگان یکتا',
      data: days.value.map((row) => row.unique_visitors),
      borderColor: '#6ee7b7',
      backgroundColor: 'transparent',
      tension: 0.25,
      pointRadius: days.value.length > 30 ? 0 : 3,
    },
  ],
}))
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  animation: false,
  interaction: { mode: 'index', intersect: false },
  plugins: {
    legend: { labels: { color: '#d5dcee', font: { family: 'Vazirmatn' } } },
    tooltip: {
      rtl: true,
      callbacks: { label: (ctx) => `${ctx.dataset.label}: ${number(ctx.parsed.y)}` },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: { color: '#b9c2d7', precision: 0, callback: number },
      grid: { color: '#ffffff0d' },
    },
    x: { ticks: { color: '#b9c2d7', maxTicksLimit: 8, maxRotation: 0 }, grid: { display: false } },
  },
}
const exportCsv = () => {
  const csv =
    '\uFEFF' +
    [
      'date,views,unique_visitors',
      ...days.value.map((row) => `${row.date},${row.views},${row.unique_visitors}`),
    ].join('\r\n')
  const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }))
  const link = document.createElement('a')
  link.href = url
  link.download = `analytics-${analytics.value.period.start}-${analytics.value.period.end}.csv`
  link.click()
  setTimeout(() => URL.revokeObjectURL(url), 1000)
}
watch(selectedPeriod, fetchAnalytics, { immediate: true })
onBeforeUnmount(() => controller?.abort())
</script>

<style scoped>
.analytics-page {
  color: #f3f5fc;
  max-width: 1440px;
  margin: auto;
}
.analytics-header,
.period-bar,
.panel-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}
.analytics-header {
  margin-bottom: 28px;
}
.analytics-header h1 {
  font-size: 28px;
  font-weight: 800;
  margin: 6px 0 10px;
}
.analytics-page p,
.method-note {
  color: #bcc6db;
  font-size: 13px;
  line-height: 1.9;
}
.analytics-header .eyebrow {
  color: #b7c4ff;
  font-size: 12px;
}
.refresh,
.periods button {
  padding: 10px 16px;
  border: 1px solid #52607a;
  border-radius: 10px;
  color: #dce4fb;
  background: #1c2538;
  font-size: 13px;
}
.refresh:disabled {
  opacity: 0.6;
  cursor: wait;
}
.period-bar {
  background: #151c2a;
  border: 1px solid #35415a;
  border-radius: 14px;
  padding: 12px;
  margin-bottom: 24px;
}
.period-bar > span {
  font-size: 12px;
  color: #c0cbe0;
}
.periods {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.periods button.active {
  background: #b7c4ff;
  color: #14203b;
  border-color: #b7c4ff;
  font-weight: 700;
}
.metrics {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}
.metrics article,
.panel {
  background: #151c2a;
  border: 1px solid #35415a;
  border-radius: 16px;
  padding: 24px;
}
.metric-icon {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  background: #b7c4ff16;
  color: #c4d0ff;
  border-radius: 12px;
  margin-bottom: 18px;
}
.metrics strong {
  display: block;
  font-size: 32px;
  margin: 8px 0;
}
.metrics small {
  color: #aebcd4;
  font-size: 11px;
}
.panel {
  margin-bottom: 24px;
  min-width: 0;
}
.panel h2 {
  font-size: 17px;
  font-weight: 700;
}
.panel-heading {
  margin-bottom: 24px;
}
.panel-heading p {
  margin-top: 8px;
}
.badge {
  font-size: 12px;
  white-space: nowrap;
  color: #c4d0ff;
  background: #b7c4ff12;
  border-radius: 8px;
  padding: 7px 10px;
}
.chart {
  height: 330px;
}
.insights {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.insight-value {
  display: block;
  font-size: 24px;
  margin: 20px 0 8px;
  color: #c4d0ff;
}
.insight-value small {
  font-size: 14px;
  color: #c0cbe0;
}
.table-scroll {
  overflow: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  text-align: right;
  white-space: nowrap;
}
th {
  font-size: 12px;
  color: #b8c5dc;
  font-weight: 500;
  padding: 14px 12px;
  border-bottom: 1px solid #46516a;
}
td {
  padding: 18px 12px;
  font-size: 13px;
  border-bottom: 1px solid #2c374e;
}
td:first-child {
  white-space: normal;
  min-width: 180px;
  max-width: 440px;
  overflow-wrap: anywhere;
}
td a {
  color: #c4d0ff;
}
td small {
  display: block;
  color: #b9c5da;
  margin-top: 6px;
}
.page-path {
  display: inline-block;
}
.share {
  min-width: 120px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.share > div {
  height: 5px;
  background: #35425d;
  border-radius: 8px;
  flex: 1;
}
.share i {
  display: block;
  height: 5px;
  background: #b7c4ff;
  border-radius: 8px;
}
.daily-table {
  max-height: 400px;
}
.daily-table th {
  position: sticky;
  top: 0;
  background: #151c2a;
}
.error {
  padding: 20px;
  background: #402733;
  color: #ffd0d7;
  border: 1px solid #9b5a6b;
  border-radius: 12px;
  margin-bottom: 24px;
}
.error button {
  margin-right: 16px;
  text-decoration: underline;
}
.empty,
.loading {
  text-align: center;
  padding: 36px;
}
.method-note {
  margin-bottom: 24px;
}
button:focus-visible,
a:focus-visible {
  outline: 3px solid #c4d0ff;
  outline-offset: 4px;
}
button:hover:not(:disabled) {
  filter: brightness(1.1);
}
@media (max-width: 1000px) {
  .metrics {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
@media (max-width: 600px) {
  .analytics-header,
  .period-bar,
  .panel-heading {
    align-items: flex-start;
    flex-direction: column;
  }
  .metrics {
    gap: 10px;
  }
  .metrics article,
  .panel {
    padding: 16px;
  }
  .metrics strong {
    font-size: 26px;
  }
  .insights {
    grid-template-columns: 1fr;
    gap: 0;
  }
  .chart {
    height: 270px;
  }
  .periods button {
    padding: 9px 12px;
  }
}
</style>
